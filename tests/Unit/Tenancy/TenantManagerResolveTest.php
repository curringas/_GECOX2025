<?php
// tests/Unit/Tenancy/TenantManagerResolveTest.php
namespace Tests\Unit\Tenancy;

use App\Tenancy\TenantManager;
use Tests\TestCase;

class TenantManagerResolveTest extends TestCase
{
    private function fixture(): void
    {
        config(['tenants' => [
            'default' => 'granadaesnoticia',
            'tenants' => [
                'granadaesnoticia' => [
                    'name' => 'Granada Es Noticia',
                    'hosts' => ['admin.granadaesnoticia.com', 'granadaesnoticia.test'],
                    'db' => [], 'storage' => '', 'logo' => 'granadaesnoticia',
                ],
                'granadaenjuego' => [
                    'name' => 'Granada En Juego',
                    'hosts' => ['admin.granadaenjuego.com', 'granadaenjuego.test'],
                    'db' => [], 'storage' => 'tenants/granadaenjuego', 'logo' => 'granadaenjuego',
                ],
            ],
        ]]);
    }

    public function test_resolves_known_host(): void
    {
        $this->fixture();
        $m = new TenantManager();
        $this->assertSame('granadaenjuego', $m->resolveFromHost('admin.granadaenjuego.com'));
        $this->assertSame('granadaesnoticia', $m->resolveFromHost('granadaesnoticia.test'));
    }

    public function test_unknown_host_falls_back_to_default(): void
    {
        $this->fixture();
        $m = new TenantManager();
        $this->assertSame('granadaesnoticia', $m->resolveFromHost('localhost'));
    }
}
