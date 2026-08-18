<?php

namespace App\Tenancy;

class Tenant
{
    public function __construct(
        private string $key,
        private array $config,
    ) {}

    public function key(): string
    {
        return $this->key;
    }

    public function name(): string
    {
        return $this->config['name'] ?? $this->key;
    }

    /**
     * URL del logo del tenant (p. ej. 'logo-dark.png', 'logo.svg').
     * Si el tenant no tiene su fichero propio, cae en los assets por defecto.
     */
    public function logo(string $file): string
    {
        $slug = $this->config['logo'] ?? $this->key;
        $relative = "images/tenants/{$slug}/{$file}";

        if (is_file(public_path($relative))) {
            return asset($relative);
        }
        return asset("build/images/{$file}");
    }
}
