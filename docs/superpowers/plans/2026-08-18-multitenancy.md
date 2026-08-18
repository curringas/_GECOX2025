# Multitenancy por dominio — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Que el panel sirva a varios dominios conectando a una BD, storage y branding distintos según el `Host` de la petición.

**Architecture:** Un registro de tenants en `config/tenants.php` (secretos en `.env`). Un `TenantManager` resuelve el tenant por `Host` y reconfigura en tiempo de ejecución la conexión de BD por defecto, el disco `public`, la cookie de sesión, el prefijo de caché y el branding. Un middleware global (`IdentifyTenant`) lo aplica el primero en cada petición web; en consola se resuelve por opción/variable. Los modelos usan la conexión por defecto, así que no se tocan.

**Tech Stack:** PHP 8.2, Laravel 11, PHPUnit, MySQL. Plantilla Skote (Blade/Bootstrap).

**Spec:** `docs/superpowers/specs/2026-08-18-multitenancy-design.md`

## Global Constraints

- **Idioma español** en columnas, UI, comentarios y mensajes de commit (estilo del repo: `feat:`, `fix:`, `docs:`).
- **No mover ni cambiar la ubicación física del storage del tenant actual** (`granadaesnoticia`): su prefijo de storage es `''` y debe seguir escribiendo/sirviendo en `storage/app/public` como hoy (symlink del front en producción).
- **Los modelos no se modifican**: usan la conexión por defecto (ya declaran `$table`).
- **Tenant por defecto** = `granadaesnoticia`; host desconocido cae en él.
- Rama de trabajo: `feature/multitenant`. **No** tocar `main` (deploy).
- Tests: `php artisan test`. Los tests NO deben depender de las BD reales: fijan `config('tenants')` con un fixture y sólo verifican config/Storage/branding (sin ejecutar queries).

---

### Task 1: Registro de tenants + `resolveFromHost`

**Files:**
- Create: `config/tenants.php`
- Create: `app/Tenancy/TenantManager.php`
- Modify: `app/Providers/AppServiceProvider.php` (bind singleton)
- Test: `tests/Unit/Tenancy/TenantManagerResolveTest.php`

**Interfaces:**
- Produces:
  - `App\Tenancy\TenantManager::resolveFromHost(string $host): string`
  - `App\Tenancy\TenantManager::current(): ?string`
  - Config `tenants` con forma `['default' => string, 'tenants' => array<string,array>]`.

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Unit/Tenancy/TenantManagerResolveTest.php
namespace Tests\Unit\Tenancy;

use App\Tenancy\TenantManager;
use Tests\TestCase;

class TenantManagerResolveTest extends TestCase
{
    private function fixture(): void
    {
        config(['tenants' => [
            'default' => 'granadaesnoticia',
            'tenants' => [
                'granadaesnoticia' => [
                    'name' => 'Granada Es Noticia',
                    'hosts' => ['admin.granadaesnoticia.com', 'granadaesnoticia.test'],
                    'db' => [], 'storage' => '', 'logo' => 'granadaesnoticia',
                ],
                'granadaenjuego' => [
                    'name' => 'Granada En Juego',
                    'hosts' => ['admin.granadaenjuego.com', 'granadaenjuego.test'],
                    'db' => [], 'storage' => 'tenants/granadaenjuego', 'logo' => 'granadaenjuego',
                ],
            ],
        ]]);
    }

    public function test_resolves_known_host(): void
    {
        $this->fixture();
        $m = new TenantManager();
        $this->assertSame('granadaenjuego', $m->resolveFromHost('admin.granadaenjuego.com'));
        $this->assertSame('granadaesnoticia', $m->resolveFromHost('granadaesnoticia.test'));
    }

    public function test_unknown_host_falls_back_to_default(): void
    {
        $this->fixture();
        $m = new TenantManager();
        $this->assertSame('granadaesnoticia', $m->resolveFromHost('localhost'));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=TenantManagerResolveTest`
Expected: FAIL (`Class "App\Tenancy\TenantManager" not found`).

- [ ] **Step 3: Create `config/tenants.php`**

```php
<?php
// config/tenants.php
return [
    'default' => env('TENANT_DEFAULT', 'granadaesnoticia'),

    'tenants' => [
        'granadaesnoticia' => [
            'name'  => 'Granada Es Noticia',
            'hosts' => ['admin.granadaesnoticia.com', 'granadaesnoticia.test'],
            'db' => [
                'host'     => env('TENANT_ESNOTICIA_DB_HOST', '127.0.0.1'),
                'port'     => env('TENANT_ESNOTICIA_DB_PORT', '3306'),
                'database' => env('TENANT_ESNOTICIA_DB_DATABASE', env('DB_DATABASE')),
                'username' => env('TENANT_ESNOTICIA_DB_USERNAME', env('DB_USERNAME')),
                'password' => env('TENANT_ESNOTICIA_DB_PASSWORD', env('DB_PASSWORD')),
            ],
            'storage' => '',
            'logo'    => 'granadaesnoticia',
        ],

        'granadaenjuego' => [
            'name'  => 'Granada En Juego',
            'hosts' => ['admin.granadaenjuego.com', 'granadaenjuego.test'],
            'db' => [
                'host'     => env('TENANT_ENJUEGO_DB_HOST', '127.0.0.1'),
                'port'     => env('TENANT_ENJUEGO_DB_PORT', '3306'),
                'database' => env('TENANT_ENJUEGO_DB_DATABASE'),
                'username' => env('TENANT_ENJUEGO_DB_USERNAME'),
                'password' => env('TENANT_ENJUEGO_DB_PASSWORD'),
            ],
            'storage' => 'tenants/granadaenjuego',
            'logo'    => 'granadaenjuego',
        ],
    ],
];
```

- [ ] **Step 4: Create `app/Tenancy/TenantManager.php` (solo resolución)**

```php
<?php
// app/Tenancy/TenantManager.php
namespace App\Tenancy;

class TenantManager
{
    private ?string $current = null;

    /** Devuelve la clave del tenant para un host, con fallback al default. */
    public function resolveFromHost(string $host): string
    {
        $host = strtolower(trim($host));
        foreach (config('tenants.tenants', []) as $key => $cfg) {
            foreach (($cfg['hosts'] ?? []) as $h) {
                if (strtolower($h) === $host) {
                    return $key;
                }
            }
        }
        return config('tenants.default');
    }

    public function current(): ?string
    {
        return $this->current;
    }
}
```

- [ ] **Step 5: Bind singleton en `AppServiceProvider::register()`**

En `app/Providers/AppServiceProvider.php`, dentro de `register()`, añade al final (antes de cerrar el método):

```php
        $this->app->singleton(\App\Tenancy\TenantManager::class);
```

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --filter=TenantManagerResolveTest`
Expected: PASS (2 tests).

- [ ] **Step 7: Commit**

```bash
git add config/tenants.php app/Tenancy/TenantManager.php app/Providers/AppServiceProvider.php tests/Unit/Tenancy/TenantManagerResolveTest.php
git commit -m "feat: registro de tenants y resolucion por host"
```

---

### Task 2: Conmutación de conexión de BD en `configure()`

**Files:**
- Modify: `app/Tenancy/TenantManager.php`
- Test: `tests/Unit/Tenancy/TenantManagerConfigureDbTest.php`

**Interfaces:**
- Consumes: config `tenants`, `config('database.connections.mysql')` como plantilla.
- Produces: `TenantManager::configure(string $tenant): void` que deja
  `config('database.default') === 'tenant'` y define
  `config('database.connections.tenant')` con host/db/credenciales del tenant.

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=TenantManagerConfigureDbTest`
Expected: FAIL (`Call to undefined method ...::configure()`).

- [ ] **Step 3: Añade `configure()` (parte BD) a `TenantManager`**

Añade el `use` y el método a `app/Tenancy/TenantManager.php`:

```php
use Illuminate\Support\Facades\DB;
```

```php
    public function configure(string $tenant): void
    {
        $cfg = config("tenants.tenants.$tenant");
        if ($cfg === null) {
            $tenant = config('tenants.default');
            $cfg = config("tenants.tenants.$tenant");
        }

        $this->configureDatabase($cfg['db']);

        $this->current = $tenant;
    }

    private function configureDatabase(array $db): void
    {
        $conn = array_merge(config('database.connections.mysql'), [
            'host'     => $db['host'] ?? '127.0.0.1',
            'port'     => $db['port'] ?? '3306',
            'database' => $db['database'] ?? null,
            'username' => $db['username'] ?? null,
            'password' => $db['password'] ?? null,
        ]);

        config(['database.connections.tenant' => $conn]);
        config(['database.default' => 'tenant']);
        DB::purge('tenant');
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=TenantManagerConfigureDbTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Tenancy/TenantManager.php tests/Unit/Tenancy/TenantManagerConfigureDbTest.php
git commit -m "feat: conmutar conexion de BD por tenant en configure()"
```

---

### Task 3: Aislamiento del disco `public` por tenant

**Files:**
- Modify: `app/Tenancy/TenantManager.php`
- Test: `tests/Unit/Tenancy/TenantManagerStorageTest.php`

**Interfaces:**
- Produces: `configure()` reapunta `filesystems.disks.public` (`root` y `url`)
  según el prefijo `storage` del tenant. Prefijo `''` = ubicación actual.

- [ ] **Step 1: Write the failing test**

```php
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
        // URL retrocompatible: igual que asset('storage/...') de hoy
        $this->assertSame('https://admin.example.com/storage/banners/x.jpg',
            Storage::disk('public')->url('banners/x.jpg'));
    }

    public function test_new_tenant_is_isolated_in_subfolder(): void
    {
        $this->fixture();
        (new TenantManager())->configure('nuevo');

        $this->assertSame(storage_path('app/public/tenants/nuevo'), config('filesystems.disks.public.root'));
        $this->assertSame('https://admin.example.com/storage/tenants/nuevo/banners/x.jpg',
            Storage::disk('public')->url('banners/x.jpg'));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=TenantManagerStorageTest`
Expected: FAIL (root/url sin prefijo del tenant).

- [ ] **Step 3: Añade la reconfiguración de storage a `configure()`**

Añade el `use`:

```php
use Illuminate\Support\Facades\Storage;
```

Dentro de `configure()`, después de `configureDatabase(...)` y antes de `$this->current = $tenant;`:

```php
        $this->configureStorage($cfg['storage'] ?? '');
```

Y añade el método:

```php
    private function configureStorage(string $prefix): void
    {
        $suffix = $prefix !== '' ? '/'.trim($prefix, '/') : '';

        config(['filesystems.disks.public.root' => storage_path('app/public'.$suffix)]);
        config(['filesystems.disks.public.url'  => rtrim(config('app.url'), '/').'/storage'.$suffix]);

        Storage::forgetDisk('public'); // fuerza recrear el disco con la nueva config
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=TenantManagerStorageTest`
Expected: PASS (2 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Tenancy/TenantManager.php tests/Unit/Tenancy/TenantManagerStorageTest.php
git commit -m "feat: aislar disco public por tenant sin mover el tenant actual"
```

---

### Task 4: Branding (value object `Tenant`) + sesión + caché

**Files:**
- Create: `app/Tenancy/Tenant.php`
- Modify: `app/Tenancy/TenantManager.php`
- Test: `tests/Unit/Tenancy/TenantLogoTest.php`
- Test: `tests/Unit/Tenancy/TenantManagerSessionCacheTest.php`

**Interfaces:**
- Produces:
  - `App\Tenancy\Tenant` con `key(): string`, `name(): string`, `logo(string $file): string`.
  - `configure()` fija `session.cookie` = `"{tenant}_session"`, `cache.prefix` = tenant,
    y comparte a las vistas la variable `tenant` (instancia de `Tenant`).
  - `TenantManager::tenant(): ?Tenant`.

- [ ] **Step 1: Write the failing tests**

```php
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
```

```php
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
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=TenantLogoTest`
Run: `php artisan test --filter=TenantManagerSessionCacheTest`
Expected: FAIL (`App\Tenancy\Tenant` no existe / método `tenant()` indefinido).

- [ ] **Step 3: Crea `app/Tenancy/Tenant.php`**

```php
<?php
// app/Tenancy/Tenant.php
namespace App\Tenancy;

class Tenant
{
    public function __construct(
        private string $key,
        private array $config,
    ) {}

    public function key(): string
    {
        return $this->key;
    }

    public function name(): string
    {
        return $this->config['name'] ?? $this->key;
    }

    /**
     * URL del logo del tenant (p. ej. 'logo-dark.png', 'logo.svg').
     * Si el tenant no tiene su fichero propio, cae en los assets por defecto.
     */
    public function logo(string $file): string
    {
        $slug = $this->config['logo'] ?? $this->key;
        $relative = "images/tenants/{$slug}/{$file}";

        if (is_file(public_path($relative))) {
            return asset($relative);
        }
        return asset("build/images/{$file}");
    }
}
```

- [ ] **Step 4: Extiende `configure()` con branding + sesión + caché**

Añade el `use`:

```php
use Illuminate\Support\Facades\View;
```

Añade una propiedad y el getter en `TenantManager`:

```php
    private ?Tenant $tenant = null;

    public function tenant(): ?Tenant
    {
        return $this->tenant;
    }
```

En `configure()`, después de `configureStorage(...)` y antes de `$this->current = $tenant;`:

```php
        config(['session.cookie' => $tenant.'_session']);
        config(['cache.prefix' => $tenant]);

        $this->tenant = new Tenant($tenant, $cfg);
        View::share('tenant', $this->tenant);
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --filter=TenantLogoTest`
Run: `php artisan test --filter=TenantManagerSessionCacheTest`
Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Tenancy/Tenant.php app/Tenancy/TenantManager.php tests/Unit/Tenancy/TenantLogoTest.php tests/Unit/Tenancy/TenantManagerSessionCacheTest.php
git commit -m "feat: branding por tenant (logo/nombre) y aislar sesion/cache"
```

---

### Task 5: Middleware `IdentifyTenant` (global, primero)

**Files:**
- Create: `app/Http/Middleware/IdentifyTenant.php`
- Modify: `app/Http/Kernel.php` (añadir al principio de `$middleware`)
- Test: `tests/Feature/Tenancy/IdentifyTenantMiddlewareTest.php`

**Interfaces:**
- Consumes: `TenantManager::resolveFromHost`, `TenantManager::configure`.
- Produces: middleware que, dado el `Host` de la request, deja el tenant configurado.

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=IdentifyTenantMiddlewareTest`
Expected: FAIL (`App\Http\Middleware\IdentifyTenant` no existe).

- [ ] **Step 3: Crea el middleware**

```php
<?php
// app/Http/Middleware/IdentifyTenant.php
namespace App\Http\Middleware;

use App\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;

class IdentifyTenant
{
    public function __construct(private TenantManager $tenants) {}

    public function handle(Request $request, Closure $next)
    {
        $tenant = $this->tenants->resolveFromHost($request->getHost());
        $this->tenants->configure($tenant);

        return $next($request);
    }
}
```

- [ ] **Step 4: Regístralo el primero en `app/Http/Kernel.php`**

En el array `$middleware`, añade la línea como **primer** elemento:

```php
    protected $middleware = [
        \App\Http\Middleware\IdentifyTenant::class,
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=IdentifyTenantMiddlewareTest`
Expected: PASS (2 tests).

- [ ] **Step 6: Commit**

```bash
git add app/Http/Middleware/IdentifyTenant.php app/Http/Kernel.php tests/Feature/Tenancy/IdentifyTenantMiddlewareTest.php
git commit -m "feat: middleware IdentifyTenant que enruta por host"
```

---

### Task 6: URLs de storage tenant-aware en controladores

**Files:**
- Modify: `app/Http/Controllers/UserController.php`
- Modify: `app/Http/Controllers/BannerController.php`
- Test: (cubierto por Task 3; añadir verificación de regresión de rutas restantes)

**Interfaces:**
- Consumes: disco `public` reconfigurado por tenant (Task 3).

- [ ] **Step 1: Localiza todos los usos de `asset('storage/...')`**

Run: `grep -rn "asset('storage/" app/Http/Controllers resources/views`
Anota cada resultado; todos deben pasar a `Storage::disk('public')->url(...)`. Los conocidos hoy: `UserController` (avatares) y `BannerController` (banners).

- [ ] **Step 2: `UserController` — import de Storage**

Comprueba que exista `use Illuminate\Support\Facades\Storage;` al principio de `app/Http/Controllers/UserController.php`; si no está, añádelo junto a los otros `use`.

- [ ] **Step 3: `UserController` — URL del avatar en el listado**

Reemplaza (aprox. línea 34):

```php
                    return $row->avatar ? '<img src="' . asset('storage/avatares/' . $row->avatar) . '" width="40" height="40" class="rounded-circle"/>' : '';
```

por:

```php
                    return $row->avatar ? '<img src="' . Storage::disk('public')->url('avatares/' . $row->avatar) . '" width="40" height="40" class="rounded-circle"/>' : '';
```

- [ ] **Step 4: `UserController` — ruta física del avatar (store y update)**

Hay **dos** bloques idénticos (crear y actualizar). En ambos, reemplaza:

```php
            $directory = public_path('storage/avatares');
```

por:

```php
            $directory = Storage::disk('public')->path('avatares');
```

(El resto del bloque —`mkdir` y `$avatar->move($directory, $imageName)`— no cambia. Para el tenant actual, `path('avatares')` = `storage/app/public/avatares`, misma ubicación física de hoy vía symlink.)

- [ ] **Step 5: `BannerController` — URL del banner en el listado**

Comprueba el `use Illuminate\Support\Facades\Storage;` (ya se usa `Storage::disk` en el fichero, debería existir). Reemplaza (aprox. línea 72):

```php
                        return '<a href="' . $url . '"><img src="'.asset('storage/banners/'.$row->Banner).'" style="max-height:60px" /></a>';
```

por:

```php
                        return '<a href="' . $url . '"><img src="'.Storage::disk('public')->url('banners/'.$row->Banner).'" style="max-height:60px" /></a>';
```

- [ ] **Step 6: Convierte cualquier otro `asset('storage/...')` restante**

Para cada resultado del Step 1 no cubierto arriba (p. ej. en vistas), cámbialo a la forma equivalente `Storage::disk('public')->url('<ruta-sin-storage/>')`. Si el resultado es en una vista Blade: `{{ Storage::disk('public')->url('...') }}`.

- [ ] **Step 7: Ejecuta toda la suite (regresión)**

Run: `php artisan test`
Expected: PASS (sin regresiones). El test de Task 3 garantiza que para el tenant por defecto la URL generada es idéntica a la de hoy.

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/UserController.php app/Http/Controllers/BannerController.php
git commit -m "refactor: URLs de storage tenant-aware via disco public"
```

---

### Task 7: Branding en las vistas (logo + título)

**Files:**
- Modify: `resources/views/auth/login.blade.php`
- Modify: `resources/views/auth/register.blade.php`
- Modify: `resources/views/auth/verify.blade.php`
- Modify: `resources/views/auth/passwords/reset.blade.php`
- Modify: `resources/views/auth/passwords/confirm.blade.php`
- Modify: `resources/views/layouts/topbar.blade.php`
- Modify: `resources/views/index.blade.php`
- Modify: `resources/views/layouts/master.blade.php` (título)
- Test: `tests/Feature/Tenancy/BrandingViewTest.php`

**Interfaces:**
- Consumes: variable de vista `$tenant` (instancia de `Tenant`) compartida en `configure()`.

- [ ] **Step 1: Write the failing test**

```php
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

        $html = view('auth.login')->render();

        // Debe usar el helper del tenant (fallback a build/images si no hay logo propio)
        $this->assertStringContainsString('logo-dark.png', $html);
        $this->assertStringNotContainsString("URL::asset('build/images/logo-dark.png')", $html);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=BrandingViewTest`
Expected: FAIL (la vista aún usa `URL::asset('build/images/...')` literal; la 2ª aserción falla). Si `auth.login` requiere datos extra para render, ajusta el test para envolver en try/catch del render mínimo; el objetivo es verificar la fuente del logo.

- [ ] **Step 3: Sustituye los logos en las vistas de auth**

En `login`, `register`, `verify`, `passwords/reset`, `passwords/confirm`, reemplaza cada par:

```blade
<img src="{{ URL::asset('build/images/logo-dark.png') }}" ... class="auth-logo-dark">
<img src="{{ URL::asset('build/images/logo-light.png') }}" ... class="auth-logo-light">
```

por (manteniendo los atributos `height`/`class` existentes de cada fichero):

```blade
<img src="{{ $tenant->logo('logo-dark.png') }}" alt="" height="45" class="auth-logo-dark">
<img src="{{ $tenant->logo('logo-light.png') }}" alt="" height="45" class="auth-logo-light">
```

- [ ] **Step 4: Sustituye los logos del `topbar` (interior)**

En `resources/views/layouts/topbar.blade.php` reemplaza las 4 referencias:

```blade
{{ URL::asset('build/images/logo.svg') }}        → {{ $tenant->logo('logo.svg') }}
{{ URL::asset('build/images/logo-dark.png') }}   → {{ $tenant->logo('logo-dark.png') }}
{{ URL::asset('build/images/logo-light.svg') }}  → {{ $tenant->logo('logo-light.svg') }}
{{ URL::asset('build/images/logo-light.png') }}  → {{ $tenant->logo('logo-light.png') }}
```

- [ ] **Step 5: Sustituye el logo de `index.blade.php`**

Reemplaza (aprox. línea 82):

```blade
<img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" class="img-fluid" width="291" height="90">
```

por:

```blade
<img src="{{ $tenant->logo('logo-light.png') }}" alt="" class="img-fluid" width="291" height="90">
```

- [ ] **Step 6: Título por tenant en `master.blade.php`**

Reemplaza:

```blade
    <title> @yield('title') | GECOX  - Admin & Dashboard</title>
```

por:

```blade
    <title> @yield('title') | {{ $tenant->name() ?? 'GECOX' }}</title>
```

- [ ] **Step 7: Run test to verify it passes**

Run: `php artisan test --filter=BrandingViewTest`
Expected: PASS.

- [ ] **Step 8: Commit**

```bash
git add resources/views
git commit -m "feat: logo y titulo por tenant en login e interior"
```

---

### Task 8: Resolución de tenant en consola (artisan)

**Files:**
- Modify: `app/Tenancy/TenantManager.php`
- Modify: `app/Providers/AppServiceProvider.php` (boot, solo consola)
- Test: `tests/Unit/Tenancy/TenantManagerConsoleTest.php`

**Interfaces:**
- Produces: `TenantManager::resolveFromConsole(array $argv, ?string $envTenant): string`
  (prioridad: `--tenant=X` > `$envTenant` > default).

- [ ] **Step 1: Write the failing test**

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=TenantManagerConsoleTest`
Expected: FAIL (`resolveFromConsole` indefinido).

- [ ] **Step 3: Implementa `resolveFromConsole`**

Añade a `TenantManager`:

```php
    public function resolveFromConsole(array $argv, ?string $envTenant): string
    {
        $picked = null;

        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--tenant=')) {
                $picked = substr($arg, strlen('--tenant='));
                break;
            }
        }

        $picked = $picked ?? ($envTenant ?: null);

        if ($picked !== null && config("tenants.tenants.$picked") !== null) {
            return $picked;
        }
        return config('tenants.default');
    }
```

- [ ] **Step 4: Cablea la resolución en consola en `AppServiceProvider::boot()`**

Añade el `use` al principio de `app/Providers/AppServiceProvider.php`:

```php
use App\Tenancy\TenantManager;
```

Dentro de `boot()`, tras `Schema::defaultStringLength(191);`:

```php
        if ($this->app->runningInConsole()) {
            $manager = $this->app->make(TenantManager::class);
            $tenant = $manager->resolveFromConsole($_SERVER['argv'] ?? [], env('TENANT'));
            $manager->configure($tenant);
        }
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --filter=TenantManagerConsoleTest`
Expected: PASS (4 tests).

- [ ] **Step 6: Verifica que artisan sigue arrancando**

Run: `php artisan about` (o `php artisan route:list`)
Expected: sin errores; el comando corre bajo el tenant por defecto.

- [ ] **Step 7: Commit**

```bash
git add app/Tenancy/TenantManager.php app/Providers/AppServiceProvider.php tests/Unit/Tenancy/TenantManagerConsoleTest.php
git commit -m "feat: seleccion de tenant en consola via --tenant/TENANT"
```

---

### Task 9: `.env.example`, carpetas de logos y cierre de docs

**Files:**
- Modify: `.env.example`
- Create: `public/images/tenants/granadaesnoticia/.gitkeep`
- Create: `public/images/tenants/granadaenjuego/.gitkeep`
- Modify: `docs/superpowers/specs/2026-08-18-multitenancy-design.md` (estado)

- [ ] **Step 1: Documenta las variables en `.env.example`**

Añade al final de `.env.example`:

```
# --- Multitenancy ---
TENANT_DEFAULT=granadaesnoticia

TENANT_ESNOTICIA_DB_HOST=127.0.0.1
TENANT_ESNOTICIA_DB_PORT=3306
TENANT_ESNOTICIA_DB_DATABASE=granadaen
TENANT_ESNOTICIA_DB_USERNAME=
TENANT_ESNOTICIA_DB_PASSWORD=

TENANT_ENJUEGO_DB_HOST=127.0.0.1
TENANT_ENJUEGO_DB_PORT=3306
TENANT_ENJUEGO_DB_DATABASE=
TENANT_ENJUEGO_DB_USERNAME=
TENANT_ENJUEGO_DB_PASSWORD=
```

- [ ] **Step 2: Crea las carpetas de logos por tenant**

```bash
mkdir -p public/images/tenants/granadaesnoticia public/images/tenants/granadaenjuego
touch public/images/tenants/granadaesnoticia/.gitkeep public/images/tenants/granadaenjuego/.gitkeep
```

(Los ficheros reales `logo-dark.png`, `logo-light.png`, `logo.svg`, `logo-light.svg` se añaden cuando Curro los pase; hasta entonces el helper usa el fallback de `build/images/`.)

- [ ] **Step 3: Marca el spec como implementado**

En `docs/superpowers/specs/2026-08-18-multitenancy-design.md`, cambia la línea de estado:

```markdown
- **Estado**: Implementado (código en `feature/multitenant`)
```

- [ ] **Step 4: Ejecuta toda la suite**

Run: `php artisan test`
Expected: PASS (todos los tests de Tenancy + los de ejemplo).

- [ ] **Step 5: Commit**

```bash
git add .env.example public/images/tenants docs/superpowers/specs/2026-08-18-multitenancy-design.md
git commit -m "docs: variables .env de tenants y carpetas de logos"
```

---

## Notas de despliegue (no son tareas de código)

Recordatorio (detalle en `docs/despliegue.md`), a ejecutar en el servidor cuando se active el segundo tenant:

1. Plesk: `admin.granadaenjuego.com` como **alias del mismo vhost** + DNS.
2. `.env` de producción con `TENANT_ENJUEGO_DB_*`.
3. Crear `storage/app/public/tenants/granadaenjuego`.
4. Front de granadaenjuego (repo aparte): desplegarlo y crear su **symlink** hacia esa carpeta de storage.
5. Poner los logos en `resources/images/tenants/{tenant}/` y ejecutar `npm run build` (Vite los copia a `public/build/images/tenants/{tenant}/`).
