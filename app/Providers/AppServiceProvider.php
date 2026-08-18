<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Tenancy\TenantManager;
//para la gestión de imágenes
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Imagick\Driver as ImagickDriver; // Aunque uses GD, se recomienda incluirla


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // === REGISTRO DE IMAGEMANAGER ===
        // Obtenemos el driver configurado
        $driver = config('image.driver', 'gd'); 

        // Le decimos al contenedor cómo construir la clase ImageManager
        $this->app->bind(ImageManager::class, function ($app) use ($driver) {
            
            $driverInstance = match ($driver) {
                'imagick' => new ImagickDriver(),
                default => new GdDriver(),
            };

            return new ImageManager($driverInstance);
        });
        // ================================

        $this->app->singleton(\App\Tenancy\TenantManager::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);

        if ($this->app->runningInConsole()) {
            $manager = $this->app->make(TenantManager::class);
            $tenant = $manager->resolveFromConsole($_SERVER['argv'] ?? [], env('TENANT'));
            $manager->configure($tenant);
        }
    }
}
