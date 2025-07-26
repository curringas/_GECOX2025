<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PermissionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    //
    public function index(Request $request): View
    {
        $permissions = Permission::paginate();

        return view('permissions.index', compact('permissions'))
            ->with('i', ($request->input('page', 1) - 1) * $permissions->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        
        $permission = new Permission();
        $roles = Role::all();
        return view('permissions.create', compact('permission','roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request): RedirectResponse
    {
        $permission=Permission::create($request->validated());
        $permission->syncRoles($request->input('roles'));

        return Redirect::route('permissions.index')
            ->with('success', 'Permiso '.__('messages.creado'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $permission = Permission::find($id);
        $roles = Role::all();

        return view('permissions.show', compact('permission','roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $permission = Permission::find($id);
        $roles = Role::all();
        return view('permissions.edit', compact('permission','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, Permission $permission): RedirectResponse
    {        
        $permission->update($request->validated());
        $permission->syncRoles($request->input('roles'));

        return Redirect::route('permissions.index')
            ->with('success', 'Permiso '.__('messages.actualizado'));
    }

    public function destroy($id): RedirectResponse
    {
        Permission::find($id)->delete();

        return Redirect::route('permissions.index')
            ->with('success', 'Permiso '.__('messages.eliminado'));
    }
}
