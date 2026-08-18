<?php
// tests/Unit/Tenancy/TenantLogoTest.php
namespace Tests\Unit\Tenancy;

use App\Tenancy\Tenant;
use Tests\TestCase;

class TenantLogoTest extends TestCase
{
    public function test_logo_falls_back_to_default_assets_when_tenant_file_missing(): void
    {
        config(['app.url' => 'https://x.test']);
        $t = new Tenant('inexistente', ['name'=>'X','logo'=>'inexistente']);
        // No existe public/images/tenants/inexistente/logo-dark.png → fallback build/images
        $this->assertStringEndsWith('/build/images/logo-dark.png', $t->logo('logo-dark.png'));
    }

    public function test_name_and_key(): void
    {
        $t = new Tenant('nuevo', ['name'=>'Nuevo','logo'=>'nuevo']);
        $this->assertSame('nuevo', $t->key());
        $this->assertSame('Nuevo', $t->name());
    }
}
