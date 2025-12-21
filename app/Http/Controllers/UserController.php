<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::query()->select(['id', 'name', 'email', 'avatar', 'activo', 'created_at']);

            $this->applySearchFilter($query, $request);
            $this->applyRoleFilter($query, $request);
            $this->applyActiveFilter($query, $request);
            $this->applyDateFilter($query, $request);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('avatar', function ($row) {
                    return $row->avatar ? '<img src="' . asset('storage/avatares/' . $row->avatar) . '" width="40" height="40" class="rounded-circle"/>' : '';
                })
                ->addColumn('activo', function ($row) {
                    return $row->activo ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-danger">No</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn =
                        '<a href="' .
                        route('users.show', $row->id) .
                        '" class="btn btn-sm btn-primary">
                <i class="fa fa-fw fa-eye"></i> ' .
                        __('botones.Ver') .
                        '
            </a> ';
                    $btn .=
                        '<a href="' .
                        route('users.edit', $row->id) .
                        '" class="btn btn-sm btn-success">
                <i class="fa fa-fw fa-edit"></i> ' .
                        __('botones.Editar') .
                        '
            </a> ';
                    $btn .=
                        '<form action="' .
                        route('users.destroy', $row->id) .
                        '" method="POST" style="display:inline-block; margin-bottom:0;">
                ' .
                        csrf_field() .
                        '
                ' .
                        method_field('DELETE') .
                        '
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este elemento?\')">
                    <i class="fa fa-fw fa-trash"></i> ' .
                        __('botones.Eliminar') .
                        '
                </button>
            </form>';
                    return $btn;
                })
                ->rawColumns(['avatar', 'activo', 'action'])
                ->make(true);
        }

        return view('users.index');
    }

        /**
     * Aplica filtro de búsqueda general.
     */
    private function applySearchFilter($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }
    }

        /**
     * Aplica filtro por rol.
     */
    private function applyRoleFilter($query, Request $request)
    {
        if ($request->filled('role')) {
            $role = $request->input('role');
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }
    }

        /**
     * Aplica filtro por estado activo.
     */
    private function applyActiveFilter($query, Request $request)
    {
        if ($request->has('active')) {
            $active = $request->input('active');
            $query->where('activo', $active);
        }
    }

     /**
     * Aplica filtro por fecha de creación.
     */
    private function applyDateFilter($query, Request $request)
    {
        if ($request->filled('date')) {
            $date = $request->input('date');
            // Si es un rango (ej: "2025-05-13 - 2025-05-15")
            if (strpos($date, ' - ') !== false) {
                [$start, $end] = explode(' - ', $date);
                $query->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
            } else {
                // Si es solo una fecha
                $query->whereDate('created_at', $date);
            }
        }
    }

        public function export(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $active = $request->input('active');
        $date = $request->input('date');

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UsersExport($search, $role, $active, $date), 'usuarios.xlsx');
    }

    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    $roles = \Spatie\Permission\Models\Role::all(); // O como obtengas los roles
    $permissions = \Spatie\Permission\Models\Permission::all(); // Si usas permisos

    return view('users.create', [
        'roles' => $roles,
        'permissions' => $permissions,
        'user' => null // o new User si lo necesitas
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->get('newpassword')),
            'activo' => $request->activo]);

        //Asignamos los roles y permisos        
        $user->syncRoles($request->input('role', []));
        $user->syncPermissions($request->input('permissions', []));
        
        //Preparo la imagen del avatar
        if ($request->hasFile('avatar')) {

            $avatar = $request->file('avatar');                    
            $extension = $avatar->getClientOriginalExtension();        
            
            $imageName = "avatar_" . uniqid() . '.' . $extension; // Usamos la extensión real del archivo        
            
            $directory = public_path('storage/avatares');                    
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }        
            
            $avatar->move($directory, $imageName);

            $user->avatar = $imageName;
            $user->save();
        }

        return Redirect::route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = User::find($id);


        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $user = User::find($id);
        $roles = Role::all();
        $permissions = Permission::all();

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {        

        
        $user->update($request->except('avatar'));

        //Asignamos los roles y permisos        
        $user->syncRoles($request->input('role', []));
        $user->syncPermissions($request->input('permissions', []));
        
        //Preparo la imagen del avatar
        if ($request->hasFile('avatar')) {

            $avatar = $request->file('avatar');                    
            $extension = $avatar->getClientOriginalExtension();        
            
            $imageName = "avatar_" . uniqid() . '.' . $extension; // Usamos la extensión real del archivo        
            
            $directory = public_path('storage/avatares');                    
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }        
            
            $avatar->move($directory, $imageName);

            $user->avatar = $imageName;
            $user->save();
        }

        return Redirect::route('users.index')
            ->with('success', __('messages.user_updated'));
    }

    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();

        return Redirect::route('users.index')
            ->with('success', __('messages.user_deleted'));
    }
}
