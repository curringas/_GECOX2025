<?php

namespace App\Tenancy;

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
}
