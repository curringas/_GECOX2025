<?php

namespace App\Console\Commands;

use App\Tenancy\TenantManager;
use Illuminate\Console\Command;

class TenantMigrate extends Command
{
    /**
     * Ejemplos:
     *   php artisan tenant:migrate granadaenjuego --force
     *   php artisan tenant:migrate granadaesnoticia --force
     *
     * Pensado para ejecutarse desde la UI de Laravel en Plesk, donde no se
     * puede anteponer la variable de entorno TENANT= al comando.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate {tenant : Slug del tenant (p. ej. granadaenjuego)} {--force : Ejecutar sin confirmación}';

    /**
     * @var string
     */
    protected $description = 'Ejecuta las migraciones sobre la BD de un tenant concreto';

    public function handle(TenantManager $manager): int
    {
        $tenant = $this->argument('tenant');

        if (config("tenants.tenants.$tenant") === null) {
            $disponibles = implode(', ', array_keys(config('tenants.tenants', [])));
            $this->error("Tenant desconocido: '{$tenant}'. Disponibles: {$disponibles}");

            return self::FAILURE;
        }

        $manager->configure($tenant);

        $this->info("Migrando tenant '{$tenant}' (BD: ".config('database.connections.tenant.database').')');

        return $this->call('migrate', ['--force' => (bool) $this->option('force')]);
    }
}
