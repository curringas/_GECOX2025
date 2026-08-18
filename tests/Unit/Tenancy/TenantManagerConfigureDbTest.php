<?php
// tests/Unit/Tenancy/TenantManagerConfigureDbTest.php
namespace Tests\Unit\Tenancy;

use App\Tenancy\TenantManager;
use Tests\TestCase;

class TenantManagerConfigureDbTest extends TestCase
{
    public function test_configure_switches_default_connection_and_db_name(): void
    {
        config(['tenants' => [
            'default' => 'a',
            'tenants' => [
                'a' => ['name'=>'A','hosts'=>[], 'storage'=>'', 'logo'=>'a',
                        'db'=>['host'=>'h1','port'=>'3306','database'=>'db_a','username'=>'u','password'=>'p']],
            ],
        ]]);

        $m = new TenantManager();
        $m->configure('a');

        $this->assertSame('tenant', config('database.default'));
        $this->assertSame('db_a', config('database.connections.tenant.database'));
        $this->assertSame('h1', config('database.connections.tenant.host'));
        // Hereda la plantilla mysql (charset)
        $this->assertSame('utf8mb4', config('database.connections.tenant.charset'));
        $this->assertSame('a', $m->current());
    }
}
