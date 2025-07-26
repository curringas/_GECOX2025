<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $rol1=Role::create(['name' => 'Admin']);
        $rol2=Role::create(['name' => 'User']);
        $rol3=Role::create(['name' => 'Super-admin']);

        //----------- SOLO EL SUPERADMIN PODRA CREAR ROLES Y PERMISOS ----------------------------- */
        Permission::create(['name' => 'roles.index'])->assignRole($rol3);
        Permission::create(['name' => 'roles.create'])->assignRole($rol3);
        Permission::create(['name' => 'roles.show'])->assignRole($rol3);
        Permission::create(['name' => 'roles.edit'])->assignRole($rol3);

        Permission::create(['name' => 'permissions.index'])->assignRole($rol3);
        Permission::create(['name' => 'permissions.create'])->assignRole($rol3);
        Permission::create(['name' => 'permissions.show'])->assignRole($rol3);
        Permission::create(['name' => 'permissions.edit'])->assignRole($rol3);

        
        //----------------- EL ADMIN YA PODRA CRUD DE USUARIOS Y ASIGNAR PERMISOS YA CREADOS POR EL SUPERADMIN ----------------------------- */
        Permission::create(['name' => 'users.index'])->assignRole($rol3)->assignRole($rol1);
        Permission::create(['name' => 'users.create'])->assignRole($rol3)->assignRole($rol1);
        Permission::create(['name' => 'users.show'])->assignRole($rol3)->assignRole($rol1);
        Permission::create(['name' => 'users.edit'])->assignRole($rol3)->assignRole($rol1);

        //------ DAMOS ROL AL USUARIO DE PRUEBA ----------------------------- */
        $user = \App\Models\User::find(1);
        $user->assignRole($rol3);
        
        
    }
}
