<?php
// tests/Feature/Tenancy/IdentifyTenantMiddlewareTest.php
namespace Tests\Feature\Tenancy;

use App\Http\Middleware\IdentifyTenant;
use App\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Tests\TestCase;

class IdentifyTenantMiddlewareTest extends TestCase
{
    private function fixture(): void
    {
        config(['app.url' => 'https://x.test']);
        config(['tenants' => [
            'default' => 'granadaesnoticia',
            'tenants' => [
                'granadaesnoticia' => ['name'=>'ESN','hosts'=>['admin.granadaesnoticia.com'],
                    'logo'=>'granadaesnoticia','storage'=>'', 'db'=>['database'=>'granadaen']],
                'granadaenjuego' => ['name'=>'ENJ','hosts'=>['admin.granadaenjuego.com'],
                    'logo'=>'granadaenjuego','storage'=>'tenants/granadaenjuego','db'=>['database'=>'enjuego_db']],
            ],
        ]]);
    }

    public function test_host_selects_tenant_database(): void
    {
        $this->fixture();
        $manager = app(TenantManager::class);
        $mw = new IdentifyTenant($manager);

        $request = Request::create('https://admin.granadaenjuego.com/');
        $mw->handle($request, fn ($r) => response('ok'));

        $this->assertSame('granadaenjuego', $manager->current());
        $this->assertSame('enjuego_db', config('database.connections.tenant.database'));
    }

    public function test_default_host_uses_default_tenant(): void
    {
        $this->fixture();
        $manager = app(TenantManager::class);
        $mw = new IdentifyTenant($manager);

        $request = Request::create('https://desconocido.example/');
        $mw->handle($request, fn ($r) => response('ok'));

        $this->assertSame('granadaesnoticia', $manager->current());
        $this->assertSame('granadaen', config('database.connections.tenant.database'));
    }
}
