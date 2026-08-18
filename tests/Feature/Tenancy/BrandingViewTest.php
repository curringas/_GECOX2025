<?php
// tests/Feature/Tenancy/BrandingViewTest.php
namespace Tests\Feature\Tenancy;

use App\Tenancy\Tenant;
use Tests\TestCase;

class BrandingViewTest extends TestCase
{
    public function test_login_renders_tenant_logo(): void
    {
        config(['app.url' => 'https://x.test']);
        // Simula lo que hace configure(): compartir el tenant
        $tenant = new Tenant('granadaenjuego', ['name'=>'Granada En Juego','logo'=>'granadaenjuego']);
        view()->share('tenant', $tenant);
        // La vista usa @error (necesita $errors, que normalmente inyecta el middleware de sesión)
        view()->share('errors', new \Illuminate\Support\ViewErrorBag());

        $html = view('auth.login')->render();

        // Debe usar el helper del tenant (fallback a build/images si no hay logo propio)
        $this->assertStringContainsString('logo-dark.png', $html);
        $this->assertStringNotContainsString("URL::asset('build/images/logo-dark.png')", $html);
    }
}
