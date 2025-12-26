<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Requests\PublicacionRequest;
use App\Models\DocumentoEnPublicacion;
use App\Models\ImagenEnPublicacion;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Intervention\Image\ImageManager;
use Spatie\Permission\Models\Permission as ModelsPermission;
use Spatie\Permission\Models\Role as ModelsRole;
use Yajra\DataTables\Facades\DataTables;



class PublicacionController extends Controller
{
    public function index(Request $request)
    {
        //dd($request->all());
        $categorias=Categoria::all();
        $users=User::all();
        if ($request->ajax()) {
            $query = Publicacion::query()->select(['Identificador', 'Titulo', 'Autor', 'Fecha', 'FechaInicio','Activa','Visitas','updated_at'])->with('categorias') ;

            $this->applySearchFilter($query, $request);
            $this->applyCategoriaFilter($query, $request);
            $this->applyActivaFilter($query, $request);
            $this->applyFechaFilter($query, $request);
            $this->applyUserFilter($query, $request);

            return DataTables::of($query)
                ->editColumn('Fecha', function ($row) {
                    // Asegura el orden correcto
                    return $row->Fecha ? $row->Fecha->format('d/m/Y') : null;
                })
                ->addColumn('Titulo', function ($row) {
                    $url = route('publicacion.edit', $row->Identificador);
                    return '<a href="' . $url . '">' . e($row->Titulo) . '</a>';
                })
                ->addColumn('Categorias', function ($row) {
                    $etiquetas = $row->categorias->pluck('Etiqueta')->implode(', ');
                    return $row->categorias ? $etiquetas : 'Sin categoría';
                })
                ->addColumn('Activa', function ($row) {
                    return $row->Activa ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-danger">No</span>';
                })
                ->addColumn('action', function ($row) {
                    
                    $btn =
                    /*    '<a href="' .
                        route('publicacion.edit', $row->Identificador) .
                        '" class="btn btn-sm btn-success">
                <i class="fa fa-fw fa-edit"></i> ' .
                        __('botones.Editar') .
                        '
            </a> ';
                    $btn .=*/
                        '<form action="' .
                        route('publicacion.destroy', $row->Identificador) .
                        '" method="POST" style="display:inline-block; margin-bottom:0;">
                ' .
                        csrf_field() .
                        '
                ' .
                        method_field('DELETE') .
                        '
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este elemento?\')">
                    <i class="fa fa-fw fa-trash"></i> 
                </button>
            </form>';
                    return $btn;
                })
                ->rawColumns(['Activa', 'action', 'Titulo'])
                ->make(true);
        }
        return view('publicaciones.index', compact('categorias', 'users'));
    }

    private function applySearchFilter($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('Titulo', 'like', "%{$search}%")
                ->orWhere('Subtitulo', 'like', "%{$search}%")
                ->orWhere('Keywords', 'like', "%{$search}%");
            });
        }
    }
    
    private function applyCategoriaFilter($query, Request $request)
    {
        if ($request->filled('autor')) {
            $categoria = $request->input('categoria');
            $query->whereHas('pagina', function ($q) use ($categoria) {
                $q->where('Identificador', $categoria);
            });
        }
    }
    private function applyUserFilter($query, Request $request)
    {
        if ($request->filled('user')) {
            $user = $request->input('user');
            $query->where('Creador', $user);
        }
    }

        /**
     * Aplica filtro por estado activo.
     */
    private function applyActivaFilter($query, Request $request)
    {
        if ($request->has('activa')) {
            $active = $request->input('activa');
            $query->where('Activa', 1);
        }
    }

     /**
     * Aplica filtro por fecha de creación.
     */
    private function applyFechaFilter($query, Request $request)
    {
        //dd ($request->all());
        if ($request->filled('date')) {
            $date = $request->input('date');
            //dd($date);
            // Si es un rango (ej: "2025-05-13 - 2025-05-15")
            if (strpos($date, ' - ') !== false) {
                [$start, $end] = explode(' - ', $date);
                $query->whereBetween('Fecha', [$start . ' 00:00:00', $end . ' 23:59:59']);
            } else {
                // Si es solo una fecha
                $query->whereDate('Fecha', $date);
            }
        }
    }

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all(); // O como obtengas los roles
        $permissions = \Spatie\Permission\Models\Permission::all(); // Si usas permisos
        $categoria= new Categoria();
        $categorias = $categoria->obtenterCategoriasValidasArbol();


        return view('publicaciones.edit', [
            'roles' => $roles,
            'permissions' => $permissions,
            'publicacion' => null // o new User si lo necesitas
            ,'categorias' => $categorias
        ]);
    }

    public function edit($id): View
    {
        $publicacion = Publicacion::with(['imagenes', 'documentos'])->findOrFail($id);

        $roles = ModelsRole::all();
        $permissions = ModelsPermission::all();
        $categoria= new Categoria();
        $categorias = $categoria->obtenterCategoriasValidasArbol();

        return view('publicaciones.edit', compact('publicacion', 'categorias','roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PublicacionRequest $request , ImageManager $imageManager): RedirectResponse
    {
        $validatedData = $request->validated();
        //$validatedData['Activa'] = $request->has('Activa') ? 1 : 0;
        $modificando = false;

        $categoriaIds = $request->input('categorias', []);
        if ($request->Identificador) {

            $modificando = true;
            // Actualizar categoría existente
            $publicacion = Publicacion::findOrFail($request->input('Identificador'));
            $publicacion->update($validatedData);
            //dd($validatedData);
        } else {
            // Crear nueva categoría
            $validatedData['Visitas'] = 0;
            $publicacion = Publicacion::create($validatedData);
        }

        //Categorias ------
        //dd( $categoriaIds );
        $publicacion->categorias()->sync($categoriaIds);
        
        // === GUARDAR IMAGENES Y DOCUMENTOS ===
        //dd($request->documentos_json);
        $this->guardarDocumentos($request, $publicacion, $modificando);
        $this->guardarImagenes($request, $publicacion, $modificando, $imageManager);

        return Redirect::route('publicaciones.index')
            ->with('success', __('messages.publicacion_created'));
    }

    public function destroy($id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $publicacion->delete();
        return response()->json(null, 204);
    }


    private function guardarImagenes(Request $request, $publicacion, bool $modificando = false, ImageManager $imageManager)
    {
        // 1. Configuración de destino
        $disk = 'public'; 
        $folderName = "ficheros/{$publicacion->Fecha->format('Ym')}";
        $publicacionId = $publicacion->Identificador;

        // ----------------------------------------------------
        // PASO 1: PREPARACIÓN DE DATOS (Descripciones y Claves)
        // ----------------------------------------------------
        
        // Obtener la cadena de texto de descripciones y dividirla
        $descripcionesString = $request->input('descripciones', '');
        $descripcionesArray = array_filter(explode("\n", trim($descripcionesString)));
        
        // Obtener las claves compuestas 'Publicacion|Orden' de las imágenes existentes
        $existingKeysString = $request->input('existing_image_keys', ''); // Campo oculto de Blade
        $existingKeys = array_filter(explode(',', $existingKeysString));
        
        $countExisting = count($existingKeys); // Número de imágenes que ya están en la BD

        // ----------------------------------------------------
        // PASO 2: ACTUALIZAR DESCRIPCIONES DE IMÁGENES EXISTENTES
        // ----------------------------------------------------
        if ($modificando) {
            for ($i = 0; $i < $countExisting; $i++) {
                $key = $existingKeys[$i]; // ej: '64193|0'
                $nuevaDescripcion = $descripcionesArray[$i] ?? null; // La descripción en la misma posición

                if ($nuevaDescripcion !== null) {
                    // Dividir la clave compuesta en sus partes: [Publicacion, Orden]
                    list($pubId, $orden) = explode('|', $key);

                    // ACTUALIZAR USANDO LA CLAVE COMPUESTA
                    ImagenEnPublicacion::where('Publicacion', $pubId)
                                    ->where('Orden', $orden)
                                    ->update([
                                        'Descripcion' => trim($nuevaDescripcion)
                                    ]);
                }
            }
        }


        // --- 3. Lógica de Eliminación (Edición) ---
        if ($modificando) {
            $docsAEliminarJson = $request->input('imagenes_a_eliminar', '[]');
            $imagenesAEliminarKeys = json_decode($docsAEliminarJson, true);

            if (!empty($imagenesAEliminarKeys)) {
                foreach ($imagenesAEliminarKeys as $compositeKey) {
                    $parts = explode('|', $compositeKey); 
                    
                    if (count($parts) === 2) {
                        $orden = $parts[1]; 
                        
                        // Cargar el modelo para obtener el nombre del archivo (con su extensión)
                        $img = ImagenEnPublicacion::where('Publicacion', $publicacionId)
                                                ->where('Orden', $orden)
                                                ->first();
                        
                        if ($img) {
                            // Lógica de eliminación de archivos (sin cambios)
                            $baseName = pathinfo($img->Imagen, PATHINFO_FILENAME); 
                            $baseName = substr($baseName, 0, strrpos($baseName, '_')); 
                            $ext = pathinfo($img->Imagen, PATHINFO_EXTENSION);
                            
                            Storage::disk($disk)->delete([
                                "{$folderName}/{$baseName}_original.{$ext}",
                                "{$folderName}/{$baseName}_ppal.{$ext}",
                                "{$folderName}/{$baseName}_portada.{$ext}",
                                "{$folderName}/{$baseName}_thumb.{$ext}",
                            ]);
                            
                            // Eliminar el registro de la BD usando la clave compuesta
                            ImagenEnPublicacion::where('Publicacion', $publicacionId)
                                            ->where('Orden', $orden)
                                            ->delete();
                        }
                    }
                }
            }
        }

        // --- 4. Lógica de Guardado de Nuevas Imágenes ---
        $uploadedFiles = $request->file('imagenes', []);
        if (empty($uploadedFiles)) {
            return;
        }

        // Las descripciones restantes en el array original son para las nuevas imágenes
        // (Asegúrate de que este slice se haga DESPUÉS de actualizar si el array de descripciones
        // se rellenó con las nuevas y viejas)
        $newDescriptions = array_slice($descripcionesArray, $countExisting); 
        
        // Calcular el orden inicial después de las eliminaciones/actualizaciones
        $maxOrdenExistente = ImagenEnPublicacion::where('Publicacion', $publicacionId)->max('Orden') ?? -1;
        $orden = $maxOrdenExistente + 1;

        Storage::disk($disk)->makeDirectory($folderName);
        
        $i = 0; // Índice para las nuevas descripciones
        
        foreach ($uploadedFiles as $file) {
            if (!$file->isValid()) continue; 

            // 4.1 Obtener descripción de los archivos nuevos
            $currentDescription = $newDescriptions[$i] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // 4.2 Preparación
            $ext = 'jpg'; // Salida unificada a JPEG
            $baseName = "imagen{$publicacionId}_{$orden}";

            // Cargar la instancia de imagen ORIGINAL (v3)
            $originalImageInstance = $imageManager->read($file->getRealPath());
            
            // --- 4.3 Procesamiento y Guardado de Versiones (Intervention Image v3) ---

            // A. Original (Max 1200px)
            $pathOriginal = "{$folderName}/{$baseName}_original.{$ext}";
            $image1200 = clone $originalImageInstance;
            $image1200->scaleDown(width: config('gecox_imagenes.tamanos.original.0'), height: config('gecox_imagenes.tamanos.original.1')); // Proporcional sin Upscaling (v3)
            Storage::disk($disk)->put($pathOriginal, (string) $image1200->toJpeg(90)); 


            // B. Principal (800px)
            $pathPpal = "{$folderName}/{$baseName}_ppal.{$ext}";
            $image800 = clone $originalImageInstance;
            $image800->scaleDown(width: config('gecox_imagenes.tamanos.principal.0'), height: config('gecox_imagenes.tamanos.principal.1')); // ¡Corregido a 800!
            Storage::disk($disk)->put($pathPpal, (string) $image800->toJpeg(90));
            
            
            // C. Portada (400px)
            $pathPortada = "{$folderName}/{$baseName}_portada.{$ext}";
            $image400 = clone $originalImageInstance;
            $image400->scaleDown(width: config('gecox_imagenes.tamanos.portada.0'), height: config('gecox_imagenes.tamanos.portada.1')); // ¡Corregido a 400!
            Storage::disk($disk)->put($pathPortada, (string) $image400->toJpeg(90));

            
            // D. Thumbnail (120x120 crop)
            $pathThumb = "{$folderName}/{$baseName}_thumb.{$ext}";
            $thumbInstance = clone $originalImageInstance;
            $thumbInstance->cover(config('gecox_imagenes.tamanos.thumbnail.0'), config('gecox_imagenes.tamanos.thumbnail.1')); // Recorte cuadrado (v3)
            Storage::disk($disk)->put($pathThumb, (string) $thumbInstance->toJpeg(90));
            
            // 4.4 Guardar el registro en la base de datos
            ImagenEnPublicacion::create([
                'Imagen' => "{$folderName}/{$baseName}_ppal.{$ext}", 
                'Descripcion' => $currentDescription,
                'Ancho' => 800, 
                'Publicacion' => $publicacionId,
                'Orden' => $orden,
                'Repositorio' => null,
            ]);

            $orden++;
            $i++; // Incrementar el contador de descripciones
        }
    }

    private function guardarDocumentos(Request $request, $publicacion, bool $modificando = false)
    {
        // Define el disco y la carpeta de destino
        $disk = 'public'; // Usa el disco 'public' (requiere `php artisan storage:link`)
        $orden = 0;


        // --- Lógica de Modificación (Limpieza de documentos antiguos) ---
        if ($modificando) {
            // 1. Decodificar la lista de IDs a eliminar enviada desde el frontend
            $docsAEliminarJson = $request->input('documentos_a_eliminar', '[]');
            $docsAEliminarKeys = json_decode($docsAEliminarJson, true);

            if (!empty($docsAEliminarKeys)) {
            
                // Colección para almacenar los pares de claves [Publicacion, Orden]
                $keysToDelete = [];

                foreach ($docsAEliminarKeys as $compositeKey) {
                    // Separamos la clave: $parts = ["64193", "0"]
                    $parts = explode('|', $compositeKey); 
                    
                    if (count($parts) === 2) {
                        $keysToDelete[] = [
                            'Publicacion' => $parts[0],
                            'Orden' => $parts[1]
                        ];
                    }
                }

                if (!empty($keysToDelete)) {
                    // Usaremos una consulta RAW o WHERE anidado para claves compuestas
                    // Laravel no soporta directamente whereIn en claves compuestas, 
                    // así que iteramos o usamos un WHERE RAW (más eficiente).
                    
                    foreach ($keysToDelete as $keyPair) {
                        // Consulta para encontrar el documento por clave compuesta
                        $doc = DocumentoEnPublicacion::where('Publicacion', $keyPair['Publicacion'])
                                                    ->where('Orden', $keyPair['Orden'])
                                                    ->first();

                        if ($doc) {
                            // 1. Eliminar el archivo físico (si existe)
                            Storage::disk($disk)->delete("{$doc->Documento}"); 
                            // 2. Eliminar el registro de la BD
                            DocumentoEnPublicacion::where('Publicacion', $keyPair['Publicacion'])
                                              ->where('Orden', $keyPair['Orden'])
                                              ->delete();
                        }
                    }
                }
            }
            
            // Nota: Los documentos antiguos que NO están en $docsAEliminar se conservan automáticamente.
        }
        
        // Si no hay archivos subidos, terminamos (si estamos modificando y no subimos nada, se conservan los antiguos)
        $uploadedFiles = $request->file('documentos', []);
        if (empty($uploadedFiles)) {
            return;
        }
        
        // --- Lógica de Guardado de Nuevos Documentos ---
    
        // El nuevo orden debe ser el máximo existente + 1.
        $maxOrdenExistente = DocumentoEnPublicacion::where('Publicacion', $publicacion->Identificador)->max('Orden') ?? -1;
        $orden = $maxOrdenExistente + 1;
        $folderName = "ficheros/{$publicacion->Fecha->format('Ym')}";
        foreach ($uploadedFiles as $file) {
            if (!$file->isValid()) continue; // Salta archivos no válidos
            // Generar un nombre de archivo seguro y único (relacionado con la publicación y el orden)
            $ext = $file->getClientOriginalExtension();
            $baseName = "documento{$publicacion->Identificador}_{$orden}";
            $fileNameToStore = "{$baseName}.{$ext}";

            // 1. Almacenar el archivo
            // Laravel lo mueve de la ruta temporal a la ruta final de forma segura
            $path = $file->storeAs($folderName, $fileNameToStore, $disk);

            // 2. Guardar el registro en la base de datos
            DocumentoEnPublicacion::create([
                'Documento' => $folderName."/".$fileNameToStore, // Nombre del archivo guardado
                'Tamano' => $file->getSize(), // Tamaño en bytes
                'Nombre' => $file->getClientOriginalName(), // Nombre original del usuario
                'Publicacion' => $publicacion->Identificador,
                // 'Icono' (lo puedes omitir o calcular la clase CSS en el front-end de lectura)
                'Orden' => $orden,
            ]);

            $orden++;
        }
    }

}
