<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['verify' => true]);

// Agrupamos todas las rutas que requieren autenticación
Route::middleware(['auth','active'])->group(function () {

    // Ruta principal
    Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');
    // customers route
    Route::get('/customers', [App\Http\Controllers\CustomerController::class, 'index'])->name('customers.list');

    //--- BACKEND ------------------------------
    Route::resource('categorias', App\Http\Controllers\CategoriaController::class);
    Route::post('/categorias/reorder', [App\Http\Controllers\CategoriaController::class, 'reorder'])
    ->name('categorias.reorder');
    /*Route::post('/categoria/store', [App\Http\Controllers\CategoriaController::class, 'store'])
    ->name('categoria.store');*/
    Route::get('/categoria/{id}', [App\Http\Controllers\CategoriaController::class, 'edit'])
    ->name('categorias.edit');


    //--- ADMIN ------------------------------
    // Todas las rutas de usuarios estan en esta sola linea si se respetan los estándares de Laravel 
    Route::get('users/export', [App\Http\Controllers\UserController::class, 'export'])->name('users.export');
    Route::resource('users', App\Http\Controllers\UserController::class);

    //--- SUPERADMIN ------------------------------
    // Todas las rutas de roles y permisos
    Route::get('/roles/get-permissions/{id}', [App\Http\Controllers\RoleController::class, 'getPermissions'])->name('roles.getPermissions');
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::resource('permissions', App\Http\Controllers\PermissionController::class);

    /************************************************************************************************************************ */
    // Actualizar detalles del usuario
    Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

    // Del sistema
    // Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
    Route::get('mantenimiento/maintenance', function() {
        return view('mantenimiento.maintenance');
    })->name('mantenimiento');
    Route::get('mantenimiento/comingsoon', function() {
        return view('mantenimiento.comingsoon');
    })->name('comingsoon');

    // Traducción de idioma
    Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

    // Home
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

Auth::routes();
