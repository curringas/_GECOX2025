<?php
// tests/Unit/Tenancy/TenantManagerSessionCacheTest.php
namespace Tests\Unit\Tenancy;

use App\Tenancy\TenantManager;
use Tests\TestCase;

class TenantManagerSessionCacheTest extends TestCase
{
    public function test_configure_sets_session_cookie_and_cache_prefix_and_shares_tenant(): void
    {
        config(['app.url' => 'https://x.test']);
        config(['tenants' => [
            'default' => 'nuevo',
            'tenants' => [
                'nuevo' => ['name'=>'Nuevo','hosts'=>[], 'logo'=>'nuevo', 'storage'=>'tenants/nuevo',
                            'db'=>['database'=>'y']],
            ],
        ]]);

        $m = new TenantManager();
        $m->configure('nuevo');

        $this->assertSame('nuevo_session', config('session.cookie'));
        $this->assertSame('nuevo', config('cache.prefix'));
        $this->assertInstanceOf(\App\Tenancy\Tenant::class, view()->shared('tenant'));
        $this->assertSame('Nuevo', $m->tenant()->name());
    }
}
