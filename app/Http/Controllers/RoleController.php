<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Js;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    //
    public function index(Request $request): View
    {
        $roles = Role::paginate();

        return view('roles.index', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * $roles->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $role = new Role();
        $permissions = Permission::all();
        return view('roles.create', compact('role','permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request): RedirectResponse
    {
        $role=Role::create($request->validated());        
        $role->syncPermissions($request->input('permissions'));

        return Redirect::route('roles.index')
            ->with('success', 'Rol '.__('messages.creado'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $role = Role::find($id);
        $permissions = Permission::all();

        return view('roles.show', compact('role','permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $role = Role::find($id);
        $permissions = Permission::all();

        return view('roles.edit', compact('role','permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role): RedirectResponse
    {                
        $role->update($request->validated());
        $role->syncPermissions($request->input('permissions'));


        return Redirect::route('roles.index')
            ->with('success', 'Rol '.__('messages.actualizado'));
    }

    public function destroy($id): RedirectResponse
    {
        Role::find($id)->delete();

        return Redirect::route('roles.index')
            ->with('success', 'Rol '.__('messages.eliminado'));
    }

    public function getPermissions($id): JsonResponse
    {
        $role = Role::where('name', $id)->first();
        $permissions = Permission::all();
        $permissionsRole = $role->permissions;

        return response()->json([
            'permissions' => $permissions,
            'permissionsRole' => $permissionsRole,
        ]);
    }
}
