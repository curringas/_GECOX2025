<?php

namespace Tests\Feature;

use App\Models\ImagenEnPublicacion;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * La URL de preview (thumb_url) debe construirse con el disco `public`, que el
 * TenantManager reconfigura por tenant (p. ej. enjuego -> /storage/tenants/granadaenjuego).
 * Si se hardcodea /storage/ se rompe en los tenants con subcarpeta de storage.
 */
class ImagenEnPublicacionThumbUrlTest extends TestCase
{
    private function configurarDiscoTenant(string $url, string $rootSuffix = ''): void
    {
        config(['filesystems.disks.public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'.$rootSuffix),
            'url' => $url,
            'visibility' => 'public',
        ]]);
        Storage::forgetDisk('public');
    }

    public function test_thumb_url_usa_el_prefijo_de_storage_del_tenant(): void
    {
        $this->configurarDiscoTenant('/storage/tenants/granadaenjuego', '/tenants/granadaenjuego');

        $img = new ImagenEnPublicacion(['Imagen' => 'ficheros/202608/imagen66602_0_ppal.jpg']);

        $this->assertSame(
            '/storage/tenants/granadaenjuego/ficheros/202608/imagen66602_0_portada.jpg',
            $img->thumb_url
        );
    }

    public function test_thumb_url_en_tenant_sin_subcarpeta(): void
    {
        $this->configurarDiscoTenant('/storage');

        $img = new ImagenEnPublicacion(['Imagen' => 'ficheros/202608/imagen66602_0_ppal.jpg']);

        $this->assertSame(
            '/storage/ficheros/202608/imagen66602_0_portada.jpg',
            $img->thumb_url
        );
    }
}
