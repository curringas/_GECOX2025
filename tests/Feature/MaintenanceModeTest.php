<?php
// tests/Feature/MaintenanceModeTest.php
namespace Tests\Feature;

use App\Http\Middleware\CheckMaintenanceMode;
use Illuminate\Http\Request;
use Tests\TestCase;

class MaintenanceModeTest extends TestCase
{
    private function handle(string $ip)
    {
        $mw = new CheckMaintenanceMode();
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
        return $mw->handle($request, fn ($r) => response('OK', 200));
    }

    public function test_desactivado_deja_pasar_a_todos(): void
    {
        config(['mantenimiento.activo' => false, 'mantenimiento.ips' => []]);
        $this->assertSame(200, $this->handle('9.9.9.9')->getStatusCode());
    }

    public function test_activado_bloquea_ip_no_permitida(): void
    {
        config(['mantenimiento.activo' => true, 'mantenimiento.ips' => ['1.2.3.4']]);
        $this->assertSame(503, $this->handle('9.9.9.9')->getStatusCode());
    }

    public function test_activado_deja_pasar_ip_permitida(): void
    {
        config(['mantenimiento.activo' => true, 'mantenimiento.ips' => ['1.2.3.4']]);
        $this->assertSame(200, $this->handle('1.2.3.4')->getStatusCode());
    }
}
