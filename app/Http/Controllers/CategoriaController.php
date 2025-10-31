<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CategoriaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function obtenterCategorias()
    {
        return Categoria::whereNull('Padre')
            ->whereIn('Menu', [0, -1]) // Solo categorías que deben mostrarse en el menú
             ->with(['hijos' => function ($q) {
                                    $q->whereIn('Menu', [0, -1]) // ✅ aplicar también a hijos
                                    ->orderBy('Orden');
                            }])
            ->orderBy('Orden')
            ->get();

    }
    public function index()
    {
        // Obtener las páginas raíz (sin padre)
        $amodificar = null;
        $categorias = $this->obtenterCategorias();
        return view('categorias.index', compact('categorias'));
    }

    public function reorder(Request $request)
    {
        $tree = $request->input('tree');

        if (!$tree || !is_array($tree)) {
            return response()->json(['success' => false, 'message' => 'Estructura inválida'], 400);
        }

        $this->actualizarJerarquia($tree, null);

        return response()->json(['success' => true]);
    }

    private function actualizarJerarquia($nodos, $padreId)
    {
        foreach ($nodos as $index => $nodo) {
            $categoria = \App\Models\Categoria::find($nodo['id']);
            if ($categoria) {
                $categoria->Padre = $padreId;
                $categoria->Orden = $index + 1;
                $categoria->save();

                if (!empty($nodo['children'])) {
                    $this->actualizarJerarquia($nodo['children'], $categoria->Identificador);
                }
            }
        }
    }



    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    $roles = \Spatie\Permission\Models\Role::all(); // O como obtengas los roles
    $permissions = \Spatie\Permission\Models\Permission::all(); // Si usas permisos

    return view('categorias.create', [
        'roles' => $roles,
        'permissions' => $permissions,
        'categoria' => null // o new Categoria si lo necesitas
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoriaRequest $request): RedirectResponse
    {
        //dd($request->all());
        $validatedData = $request->validated();
        //dd($validatedData);

        if ($request->filled('Identificador')) {
            // Actualizar categoría existente
            $categoria = Categoria::findOrFail($request->input('Identificador'));
            $categoria->update($validatedData);

            return Redirect::route('categorias.edit',$categoria->Identificador)
                ->with('success', __('messages.categoria_updated'));
        } else {
            // Crear nueva categoría
            Categoria::create($validatedData);

            return Redirect::route('categorias.index')
                ->with('success', __('messages.categoria_created'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        // Obtener las páginas raíz (sin padre)
        $categorias = $this->obtenterCategorias();

        $amodificar = Categoria::find($id);

        return view('categorias.index', compact('categorias','amodificar'));
    }


    public function destroy($id): RedirectResponse
    {
        Categoria::find($id)->delete();

        return Redirect::route('categorias.index')
            ->with('success', __('messages.categoria_deleted'));
    }
}
