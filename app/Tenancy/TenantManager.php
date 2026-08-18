<?php

namespace App\Tenancy;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class TenantManager
{
    private ?string $current = null;

    private ?Tenant $tenant = null;

    public function tenant(): ?Tenant
    {
        return $this->tenant;
    }

    /** Devuelve la clave del tenant para un host, con fallback al default. */
    public function resolveFromHost(string $host): string
    {
        $host = strtolower(trim($host));
        foreach (config('tenants.tenants', []) as $key => $cfg) {
            foreach (($cfg['hosts'] ?? []) as $h) {
                if (strtolower($h) === $host) {
                    return $key;
                }
            }
        }
        return config('tenants.default');
    }

    public function current(): ?string
    {
        return $this->current;
    }

    public function resolveFromConsole(array $argv, ?string $envTenant): string
    {
        $picked = null;

        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--tenant=')) {
                $picked = substr($arg, strlen('--tenant='));
                break;
            }
        }

        $picked = $picked ?? ($envTenant ?: null);

        if ($picked !== null && config("tenants.tenants.$picked") !== null) {
            return $picked;
        }
        return config('tenants.default');
    }

    public function configure(string $tenant): void
    {
        $cfg = config("tenants.tenants.$tenant");
        if ($cfg === null) {
            $tenant = config('tenants.default');
            $cfg = config("tenants.tenants.$tenant");
        }

        $this->configureDatabase($cfg['db']);
        $this->configureStorage($cfg['storage'] ?? '');

        config(['session.cookie' => $tenant.'_session']);
        config(['cache.prefix' => $tenant]);

        $this->tenant = new Tenant($tenant, $cfg);
        View::share('tenant', $this->tenant);

        $this->current = $tenant;
    }

    private function configureDatabase(array $db): void
    {
        $conn = array_merge(config('database.connections.mysql'), [
            'host'     => $db['host'] ?? '127.0.0.1',
            'port'     => $db['port'] ?? '3306',
            'database' => $db['database'] ?? null,
            'username' => $db['username'] ?? null,
            'password' => $db['password'] ?? null,
        ]);

        config(['database.connections.tenant' => $conn]);
        config(['database.default' => 'tenant']);
        DB::purge('tenant');
    }

    private function configureStorage(string $prefix): void
    {
        $suffix = $prefix !== '' ? '/'.trim($prefix, '/') : '';

        config(['filesystems.disks.public.root' => storage_path('app/public'.$suffix)]);
        config(['filesystems.disks.public.url'  => rtrim(config('app.url'), '/').'/storage'.$suffix]);

        Storage::forgetDisk('public'); // fuerza recrear el disco con la nueva config
    }
}
