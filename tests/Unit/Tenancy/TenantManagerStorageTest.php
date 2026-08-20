<?php
// tests/Unit/Tenancy/TenantManagerStorageTest.php
namespace Tests\Unit\Tenancy;

use App\Tenancy\TenantManager;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TenantManagerStorageTest extends TestCase
{
    private function fixture(): void
    {
        config(['app.url' => 'https://admin.example.com']);
        config(['tenants' => [
            'default' => 'actual',
            'tenants' => [
                'actual' => ['name'=>'Actual','hosts'=>[], 'logo'=>'actual', 'storage'=>'',
                             'db'=>['database'=>'x']],
                'nuevo'  => ['name'=>'Nuevo','hosts'=>[], 'logo'=>'nuevo', 'storage'=>'tenants/nuevo',
                             'db'=>['database'=>'y']],
            ],
        ]]);
    }

    public function test_default_tenant_keeps_current_location(): void
    {
        $this->fixture();
        (new TenantManager())->configure('actual');

        $this->assertSame(storage_path('app/public'), config('filesystems.disks.public.root'));
        // URL relativa al host actual (retrocompatible con asset('storage/...'))
        $this->assertSame('/storage/banners/x.jpg',
            Storage::disk('public')->url('banners/x.jpg'));
    }

    public function test_new_tenant_is_isolated_in_subfolder(): void
    {
        $this->fixture();
        (new TenantManager())->configure('nuevo');

        $this->assertSame(storage_path('app/public/tenants/nuevo'), config('filesystems.disks.public.root'));
        $this->assertSame('/storage/tenants/nuevo/banners/x.jpg',
            Storage::disk('public')->url('banners/x.jpg'));
    }
}
