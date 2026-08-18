<?php

namespace App\Tenancy;

use Illuminate\Support\Facades\DB;

class TenantManager
{
    private ?string $current = null;

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

    public function configure(string $tenant): void
    {
        $cfg = config("tenants.tenants.$tenant");
        if ($cfg === null) {
            $tenant = config('tenants.default');
            $cfg = config("tenants.tenants.$tenant");
        }

        $this->configureDatabase($cfg['db']);

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
}
