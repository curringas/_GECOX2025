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

    public function test_usa_la_ip_real_de_x_forwarded_for(): void
    {
        // Detrás del proxy: REMOTE_ADDR es el proxy, la IP real va en X-Forwarded-For.
        config(['mantenimiento.activo' => true, 'mantenimiento.ips' => ['80.102.160.22']]);

        $mw = new CheckMaintenanceMode();
        $req = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $req->headers->set('X-Forwarded-For', '80.102.160.22, 127.0.0.1');
        $passed = $mw->handle($req, fn ($r) => response('OK', 200));
        $this->assertSame(200, $passed->getStatusCode());

        // Un visitante cualquiera (otra IP en XFF) ve mantenimiento.
        $req2 = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $req2->headers->set('X-Forwarded-For', '9.9.9.9, 127.0.0.1');
        $blocked = $mw->handle($req2, fn ($r) => response('OK', 200));
        $this->assertSame(503, $blocked->getStatusCode());
    }
}
