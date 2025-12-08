<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BannerController;

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
   // Actualizar detalles del usuario
    Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');
// Traducción de idioma
    Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

    // Home
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('banner/datos', [App\Http\Controllers\HomeController::class, 'ajaxDatosBanner'])->name('banner.datos');
    Route::post('banner/guardar', [App\Http\Controllers\HomeController::class, 'ajaxGuardarBanner'])->name('banner.guardar');
    Route::post('noticia/datos', [App\Http\Controllers\HomeController::class, 'ajaxDatosNoticia'])->name('noticia.datos');
    Route::post('noticia/guardar', [App\Http\Controllers\HomeController::class, 'ajaxGuardarNoticia'])->name('noticia.guardar');
    Route::post('noticia/eliminar', [App\Http\Controllers\HomeController::class, 'ajaxEliminarNoticia'])->name('noticia.eliminar');
    Route::post('reordenar', [App\Http\Controllers\HomeController::class, 'ajaxReordenar'])->name('portada.reordenar');    
    Route::post('eliminar', [App\Http\Controllers\HomeController::class, 'ajaxEliminar'])->name('portada.eliminar');
    Route::get('publicacion/buscar', [App\Http\Controllers\HomeController::class, 'ajaxBuscarPublicaciones'])->name('publicacion.buscar');

    // Del sistema
    // Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
   
    
    //Route::resource('publicaciones', App\Http\Controllers\PublicacionController::class);
    //--- PUBLICACIONES ------------------------------
    Route::get('/publicacion/{id}/edit', [App\Http\Controllers\PublicacionController::class, 'edit'])->name('publicacion.edit');
    Route::patch('/publicacion', [App\Http\Controllers\PublicacionController::class, 'store'])->name('publicacion.store');
    Route::get('/publicaciones/create', [App\Http\Controllers\PublicacionController::class, 'create'])->name('publicacion.create');
    Route::delete('/publicacion/{id}', [App\Http\Controllers\PublicacionController::class, 'destroy'])->name('publicacion.destroy');
    Route::get('/publicaciones', [App\Http\Controllers\PublicacionController::class, 'index'])->name('publicaciones.index');

    //--- BACKEND - BANNERS ------------------------------
    Route::get('/banner/{id}/edit', [App\Http\Controllers\BannerController::class, 'edit'])->name('banner.edit');
    Route::patch('/banner', [App\Http\Controllers\BannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/create', [App\Http\Controllers\BannerController::class, 'create'])->name('banners.create');
    Route::delete('/banner/{id}', [App\Http\Controllers\BannerController::class, 'destroy'])->name('banner.destroy');
    Route::get('/banners', [App\Http\Controllers\BannerController::class, 'index'])->name('banners.index');
    Route::post('banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');

    //--- BACKEND - CATEGORIAS ------------------------------
    Route::resource('categorias', App\Http\Controllers\CategoriaController::class);
    /*Route::post('/categoria/store', [App\Http\Controllers\CategoriaController::class, 'store'])
    ->name('categoria.store');*/
    Route::post('/categorias/reorder', [App\Http\Controllers\CategoriaController::class, 'reorder'])->name('categorias.reorder');
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
     Route::get('mantenimiento/maintenance', function() {
        return view('mantenimiento.maintenance');
    })->name('mantenimiento');
    Route::get('mantenimiento/comingsoon', function() {
        return view('mantenimiento.comingsoon');
    })->name('comingsoon');

    
});

Auth::routes();
