<?php
// tests/Unit/Tenancy/TenantManagerConsoleTest.php
namespace Tests\Unit\Tenancy;

use App\Tenancy\TenantManager;
use Tests\TestCase;

class TenantManagerConsoleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['tenants' => [
            'default' => 'granadaesnoticia',
            'tenants' => [
                'granadaesnoticia' => ['name'=>'A','hosts'=>[], 'logo'=>'a','storage'=>'','db'=>['database'=>'x']],
                'granadaenjuego' => ['name'=>'B','hosts'=>[], 'logo'=>'b','storage'=>'','db'=>['database'=>'y']],
            ],
        ]]);
    }

    public function test_option_takes_priority(): void
    {
        $m = new TenantManager();
        $this->assertSame('granadaenjuego',
            $m->resolveFromConsole(['artisan','migrate','--tenant=granadaenjuego'], null));
    }

    public function test_env_used_when_no_option(): void
    {
        $m = new TenantManager();
        $this->assertSame('granadaenjuego', $m->resolveFromConsole(['artisan','migrate'], 'granadaenjuego'));
    }

    public function test_default_when_nothing_given(): void
    {
        $m = new TenantManager();
        $this->assertSame('granadaesnoticia', $m->resolveFromConsole(['artisan','migrate'], null));
    }

    public function test_unknown_tenant_falls_back_to_default(): void
    {
        $m = new TenantManager();
        $this->assertSame('granadaesnoticia', $m->resolveFromConsole(['artisan','migrate','--tenant=noexiste'], null));
    }
}
