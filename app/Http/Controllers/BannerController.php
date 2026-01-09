<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Permission as ModelsPermission;
use Spatie\Permission\Models\Role as ModelsRole;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    // ...existing code...

    /**
     * Lista (DataTable) de banners con filtros similares a PublicacionController.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // seleccionar nombres completamente calificados para evitar ambigüedades
            $query = Banner::query()->select([
                'P0114_banner.Identificador',
                'P0114_banner.Titulo',
                'P0114_banner.Banner',
                'P0114_banner.Tipo',
                'P0114_banner.Creador',
                'P0114_banner.Fecha',
                'P0114_banner.Target',
            ]);

            // Solo traer p.Orden si hay categoría y tipo (posicion) -> evitar duplicados
            if ($request->filled('categoria') && $request->filled('tipo')) {
                $categoriaId = (int) $request->input('categoria');
                $posicion = (int) $request->input('tipo');

                // INNER JOIN para devolver únicamente banners asignados a la categoria+posicion
                $query->join('P0114_bannerenpagina as p', function ($join) use ($categoriaId, $posicion) {
                    $join->on('p.Banner', '=', 'P0114_banner.Identificador')
                         ->where('p.Pagina', $categoriaId)
                         ->where('p.Posicion', $posicion);
                });

                // traer Orden/Posicion desde pivot y ordenar por Orden
                $query->addSelect('p.Orden as Orden', 'p.Posicion as Posicion')
                      ->orderByRaw('COALESCE(p.Orden, 999999) ASC, P0114_banner.Identificador ASC');
            } else {
                // devolver columna Orden/Posicion NULL cuando no haya filtro completo
                $query->addSelect(DB::raw('NULL as Orden'), DB::raw('NULL as Posicion'));

                // aplicar filtros habituales (busqueda, tipo 'general' si se necesita, fecha...)
                $this->applySearchFilter($query, $request);
                $this->applyTipoFilter($query, $request);
                $query->orderBy('P0114_banner.Fecha', 'desc');
            }

            return DataTables::of($query)
                ->editColumn('Fecha', function ($row) {
                    return $row->Fecha ? \Carbon\Carbon::parse($row->Fecha)->format('d/m/Y') : null;
                })
                ->addColumn('Banner', function ($row) {
                    if (!empty($row->Banner) && !empty($row->Identificador)) {
                        $url = route('banner.edit', $row->Identificador);
                        return '<a href="' . $url . '"><img src="'.asset('storage/banners/'.$row->Banner).'" style="max-height:60px" /></a>';
                    }
                    return 'CODIGO';
                })
                ->editColumn('Tipo', function ($row) {
                    $tipos = config('gecox_banners.tipos', []);
                    return $tipos[$row->Tipo] ?? $row->Tipo;
                })
                ->editColumn('Target', function ($row) {
                    $targets = config('gecox_banners.targets', []);
                    return $targets[$row->Target] ?? $row->Target;
                })
                ->addColumn('Titulo', function ($row) {
                    $url = route('banner.edit', $row->Identificador);
                    return '<strong><a href="' . $url . '">' . e($row->Titulo) . '</a></strong>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<form action="' .
                        route('banner.destroy', $row->Identificador) .
                        '" method="POST" style="display:inline-block; margin-bottom:0;">'
                        . csrf_field()
                        . method_field('DELETE')
                        . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este elemento?\')">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </form>';
                    return $btn;
                })
                ->rawColumns(['Titulo', 'Banner','action'])
                ->make(true);
        }

        $categoria= new Categoria();
        $categorias = $categoria->obtenterCategoriasValidasArbol();

        return view('banners.index', ['categorias' => $categorias]);
    }

    // ----------------- filtros privados -----------------

    private function applySearchFilter($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('Titulo', 'like', "%{$search}%")
                  ->orWhere('Banner', 'like', "%{$search}%")
                  ->orWhere('Codigo', 'like', "%{$search}%");
            });
        }
    }

    private function applyTipoFilter($query, Request $request)
    {
        if ($request->filled('tipo')) {
            $tipo = $request->input('tipo');
            $query->where('Tipo', $tipo);
        }
    }

    /*private function applyCategoriaFilter($query, Request $request)
    {
        if ($request->filled('categoria')) {
            $categoriaId = $request->input('categoria');
            $query->whereHas('categorias', function ($q) use ($categoriaId) {
                $q->where('Pagina', $categoriaId);
            });
        }
    }*/
    // ----------------- fin filtros privados -----------------

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all(); // O como obtengas los roles
        $permissions = \Spatie\Permission\Models\Permission::all(); // Si usas permisos
        $categoria= new Categoria();
        $categorias = $categoria->obtenterCategoriasValidasArbol();


        return view('banners.edit', [
            'roles' => $roles,
            'permissions' => $permissions,
            'banner' => null // o new User si lo necesitas
            ,'categorias' => $categorias
        ]);
    }

    public function edit($id): View
    {
        $banner = Banner::findOrFail($id);

        $roles = ModelsRole::all();
        $permissions = ModelsPermission::all();
        $categoria= new Categoria();
        $categorias = $categoria->obtenterCategoriasValidasArbol();
        return view('banners.edit', compact('banner', 'categorias','roles', 'permissions'));
    }

    /**
     * Store (create or update) a banner.
     * - Si viene Identificador y existe, hace update.
     * - Si viene Identificador y no existe, intenta crear con ese ID.
     * - Si no viene Identificador, crea uno nuevo.
     */
    public function store(BannerRequest $request): RedirectResponse
    {
        // 1. OBTENER Y PREPARAR DATOS
        
        // Solo toma los campos validados por BannerRequest (Titulo, URL, Tipo, Codigo, etc.)
        $data = $request->validated(); 
        
        $imageFile = $request->file('Banner');
        $removeImage = $request->input('remove_banner') === '1';
        $imageFileMovil = $request->file('BannerMovil');
        
        // Obtener los IDs de las páginas (categorías) directamente del request sin validación
        // Asumimos que el input del formulario se llama 'categorias[]' o similar
        $paginaIds = $request->input('categorias', []); 
        
        // Limpieza de datos antes de la asignación masiva
        unset($data['Identificador']);
        // Quitar el objeto UploadedFile de $data antes de pasarlo a create/update
        if (array_key_exists('Banner', $data)) {
            unset($data['Banner']);
        }
        if (array_key_exists('BannerMovil', $data)) {
            unset($data['BannerMovil']);
        }

        $banner = null;

        // 2. CREAR O ACTUALIZAR EL REGISTRO EN LA BASE DE DATOS
        $id = $request->input('Identificador');
        
        if ($id && $existing = Banner::find($id)) {
            // ACTUAZACIÓN:
            $existing->update($data); // Actualiza campos de texto/números
            $banner = $existing;
        } else {
            // CREACIÓN:
            $banner = Banner::create($data); // Crea el nuevo registro
        }

        // 3. GESTIÓN DE LA IMAGEN EN EL DISCO Y LA DB
        if ($imageFile || $imageFileMovil || $removeImage) {
            
            // A. Eliminar imagen antigua 
            if (($imageFile || $removeImage) && $banner->Banner) {
                Storage::disk('public')->delete('banners/' . $banner->Banner);
                $banner->Banner = null; 
            }

            // B. Subir nueva imagen (si existe)
            if ($imageFile) {
                $extension = $imageFile->getClientOriginalExtension();
                $filename = "banner{$banner->Identificador}.{$extension}";
                
                $imageFile->storeAs('banners', $filename, 'public');
                $banner->Banner = $filename;
            }

            //IMAGEN PARA MOVIL
            if ($imageFileMovil || $removeImage) {
            
                // A. Eliminar imagen antigua 
                if ($banner->BannerMovil) {
                    Storage::disk('public')->delete('banners/' . $banner->Banner);
                    $banner->BannerMovil = null; 
                }

                // B. Subir nueva imagen (si existe)
                if ($imageFileMovil) {
                    $extension = $imageFileMovil->getClientOriginalExtension();
                    $filename = "banner_movil{$banner->Identificador}.{$extension}";
                    
                    $imageFileMovil->storeAs('banners', $filename, 'public');
                    $banner->BannerMovil = $filename;
                }
                
                // C. Guardar el modelo para persistir el nuevo/null nombre del archivo Banner
                $banner->save(); 
            }
            
            // C. Guardar el modelo para persistir el nuevo/null nombre del archivo Banner
            $banner->save(); 
        }


        // 4. SINCRONIZACIÓN DE RELACIONES CON POSICIÓN FIJA POR TIPO
        
        $sync = [];
        
        // Determinar la posición fija basada en el campo 'Tipo' del banner (0, 1, 2)
        $posicionFija = (int) $banner->Tipo; 

        if (is_array($paginaIds) && !empty($paginaIds)) {
            foreach ($paginaIds as $paginaId) {
                //buscamos el orden siguiente para esta página y posición
                $nextOrder = Banner::getNextOrder($paginaId, $posicionFija);

                // Se usa el ID de la página/categoría como la clave del sync
                $sync[$paginaId] = [
                    'Posicion' => $posicionFija, 
                    'Orden'    => $nextOrder, // Valor por defecto para Orden
                ];
            }
        }
        
        // Sincronizar (conectará los IDs que vengan y desconectará los que no)
        $banner->categorias()->sync($sync);

        return redirect()->route('banners.index')->with('success', 'Banner guardado correctamente.');
    }

    public function reorder(Request $request): JsonResponse
    {
        // ahora 'posicion' es required porque Orden vive por (Pagina, Posicion)
        $data = $request->validate([
            'categoria' => 'required|integer',
            'posicion'  => 'required|integer',
            'orden'     => 'required|array',
            'orden.*.Banner' => 'required|integer',
            'orden.*.Orden'  => 'required|integer',
        ]);

        $pagina = (int) $data['categoria'];
        $posicion = (int) $data['posicion'];

        DB::beginTransaction();
        try {
            // iterar y guardar el Orden para cada Banner en la pagina+posicion indicada
            foreach ($data['orden'] as $row) {
                $bannerId = (int) $row['Banner'];
                $ordenVal = (int) $row['Orden'];

                DB::table('P0114_bannerenpagina')->updateOrInsert(
                    ['Banner' => $bannerId, 'Pagina' => $pagina, 'Posicion' => $posicion],
                    ['Orden' => $ordenVal]
                );
            }
            DB::commit();

            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $banner = Banner::findOrFail($id);

        // Eliminar imagen del disco si existe
        if ($banner->Banner) {
            Storage::disk('public')->delete('banners/' . $banner->Banner);
        }
        if ($banner->BannerMovil) {
            Storage::disk('public')->delete('banners/' . $banner->BannerMovil);
        }

        // Eliminar relaciones en la tabla pivot
        $banner->categorias()->detach();

        // Eliminar el registro de la base de datos
        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Banner eliminado correctamente.');
    }
    
}