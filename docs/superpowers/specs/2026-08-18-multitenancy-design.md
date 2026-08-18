# Diseño: Multitenancy por dominio

- **Fecha**: 2026-08-18
- **Rama**: `feature/multitenant`
- **Estado**: Diseño aprobado (pendiente de plan de implementación)

## Objetivo

Que el mismo código del panel sirva a **dos sitios** conectando a **una BD u
otra según el dominio** desde el que se accede:

- `admin.granadaesnoticia.com` → BD/entorno de **granadaesnoticia** (el actual).
- `admin.granadaenjuego.com` → BD/entorno de **granadaenjuego** (nuevo).

Un solo vhost/despliegue resuelve el tenant en cada petición por el `Host`.
Debe distinguirse visualmente en qué sitio estás (logo y nombre).

## Decisiones tomadas (contexto)

- **Topología**: un único vhost sirve ambos dominios; el tenant se decide en
  **tiempo de ejecución por el `Host`**.
- **Bases de datos**: **ambas ya existen** con el **mismo esquema**
  (`P0114_*`). Solo hay que enrutar; no se crea/copia esquema.
- **Host no mapeado**: se usa un **tenant por defecto** (`granadaesnoticia`).
  Los hosts locales de Herd (`*.test`) se mapean a su tenant en la config.
- **Ficheros**: aislados por tenant, pero **sin mover nada del tenant actual**
  (ver "Aislamiento de ficheros — seguro").
- **Branding**: logo (login e interior) y nombre por tenant.
- **Configuración**: registro de tenants versionado + secretos en `.env`
  (petición explícita: "un archivo donde declaremos las bd y accesos").

## Restricción crítica de seguridad (producción)

`granadaesnoticia` **ya está en producción** y su **front público** (proyecto
PHP aparte) lee las imágenes por **ruta física**, a través de un **symlink**
en el servidor que apunta al storage real del admin. El acoplamiento admin↔front
es a nivel de **fichero físico**, no de URL.

**Regla de oro: no cambiar dónde escribe físicamente el tenant actual.**
El aislamiento se consigue **hacia adelante** (el tenant nuevo estrena carpeta
propia), no reubicando lo existente. Así el front actual no se entera de nada.

## Arquitectura

### 1. Registro de tenants y configuración

**`config/tenants.php`** (versionado) declara los tenants. Los **secretos** van
en `.env` y se leen con `env()`.

```php
return [
    'default' => 'granadaesnoticia',

    'tenants' => [
        'granadaesnoticia' => [
            'name'    => 'Granada Es Noticia',
            'hosts'   => ['admin.granadaesnoticia.com', 'granadaesnoticia.test'],
            'db'      => [
                'host'     => env('TENANT_ESNOTICIA_DB_HOST', '127.0.0.1'),
                'port'     => env('TENANT_ESNOTICIA_DB_PORT', '3306'),
                'database' => env('TENANT_ESNOTICIA_DB_DATABASE'),
                'username' => env('TENANT_ESNOTICIA_DB_USERNAME'),
                'password' => env('TENANT_ESNOTICIA_DB_PASSWORD'),
            ],
            // Prefijo de storage VACÍO = ubicación actual, no se mueve nada.
            'storage' => '',
            'logo'    => 'granadaesnoticia',
        ],

        'granadaenjuego' => [
            'name'    => 'Granada En Juego',
            'hosts'   => ['admin.granadaenjuego.com', 'granadaenjuego.test'],
            'db'      => [
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

`.env` (y `.env.example` documentado, sin valores reales):

```
TENANT_ESNOTICIA_DB_HOST=...
TENANT_ESNOTICIA_DB_DATABASE=granadaen
TENANT_ESNOTICIA_DB_USERNAME=...
TENANT_ESNOTICIA_DB_PASSWORD=...

TENANT_ENJUEGO_DB_HOST=...
TENANT_ENJUEGO_DB_DATABASE=...
TENANT_ENJUEGO_DB_USERNAME=...
TENANT_ENJUEGO_DB_PASSWORD=...
```

### 2. Resolución del tenant y conmutación de BD

**`App\Tenancy\TenantManager`** (singleton). Responsabilidades:

- `resolveFromHost(string $host): string` — devuelve la clave del tenant según
  `config('tenants')`, con fallback al `default`. Lógica pura y testeable.
- `configure(string $tenant): void` — aplica el tenant a la petición actual:
  - **BD**: `config(['database.connections.tenant' => [...datos mysql del
    tenant...]])`, `config(['database.default' => 'tenant'])`,
    `DB::purge('tenant')`. Se reutiliza la plantilla de la conexión `mysql`
    existente (charset/collation/opciones) cambiando host/db/credenciales.
  - **Storage** (ver sección 3).
  - **Sesión/caché** (ver sección 5).
  - **Branding**: `View::share('tenant', ...)` con nombre y helper de logo.
  - Guarda el tenant actual para poder consultarlo (`current()`).

**Middleware `App\Http\Middleware\IdentifyTenant`**: se registra **el primero**
de la pila global (antes de `StartSession`, auth, etc.). Lee
`$request->getHost()`, llama a `resolveFromHost` + `configure`.

Los **modelos usan la conexión por defecto** (ya declaran `$table`
explícitamente y ninguno fija `$connection`), así que **no se tocan modelos ni
queries**.

### 3. Aislamiento de ficheros — seguro

En `configure()` se **reapunta el disco `public`** al espacio del tenant:

- `root => storage_path('app/public'.($prefix ? '/'.$prefix : ''))`
- `url  => env('APP_URL').'/storage'.($prefix ? '/'.$prefix : '')`

| Tenant | `storage` (prefijo) | Carpeta física | URL (solo panel) |
|---|---|---|---|
| granadaesnoticia | `''` | `storage/app/public` *(actual)* | `/storage/...` *(actual)* |
| granadaenjuego | `tenants/granadaenjuego` | `storage/app/public/tenants/granadaenjuego` | `/storage/tenants/granadaenjuego/...` |

- Con prefijo vacío, el tenant actual mantiene **exactamente** su ubicación y
  URLs de hoy → symlink del front intacto, cero migración de imágenes.
- Los controladores que usan `Storage::disk('public')` (banners, publicaciones,
  documentos) escriben ya en la carpeta del tenant, **sin cambios**.
- Los **~4 puntos** que construyen URL con `asset('storage/...')` (avatares en
  `UserController`, banners en `BannerController`) pasan a
  `Storage::disk('public')->url('...')` para que lleven el prefijo del tenant.
  Estas URLs son **solo del panel admin** (miniaturas en listados), **no** del
  front público. Para el tenant actual devuelven la misma URL de hoy.
- `UserController` usa además `public_path('storage/avatares')` (ruta física
  para mover el avatar): se hace tenant-aware anteponiendo el prefijo; con
  prefijo vacío = ruta actual.

**Symlink**: el `public/storage` existente sigue sirviendo las subcarpetas de
tenant. El front de `granadaenjuego` (nuevo) tendrá su **propio symlink** hacia
`storage/app/public/tenants/granadaenjuego` (paso de despliegue, fuera de este
repo).

### 4. Branding por tenant (logo + nombre)

- Logos por tenant en `public/images/tenants/{tenant}/` con el juego que usa la
  plantilla: `logo-dark.png`, `logo-light.png`, `logo.svg`, `logo-light.svg`.
  Fuera de Vite → se añade un tenant sin recompilar assets.
- `TenantManager` comparte a las vistas el tenant (nombre + helper de logo).
  Se sustituyen las referencias `build/images/logo-*` por el helper del tenant
  en: `auth/login`, `auth/register`, `auth/verify`, `auth/passwords/reset`,
  `auth/passwords/confirm`, `layouts/topbar` (logo interior) e `index`.
- El `<title>`/nombre visible se toma de `tenants[x].name`.
- **Fallback**: si un tenant no tiene su carpeta de logos, se usa el juego por
  defecto de `build/images/` (comportamiento actual).

### 5. Sesión, caché y consola

- **Sesión**: `config(['session.cookie' => $tenant.'_session'])` en
  `configure()`. En producción los dominios ya separan cookies; esto blinda el
  caso de hosts `*.test` locales sobre la misma carpeta.
- **Caché** (driver file): `config(['cache.prefix' => $tenant])` para no
  mezclar claves entre tenants.
- **Consola/artisan** (el middleware no corre en CLI): resolución del tenant
  por opción `--tenant=` o variable `TENANT`. Nota: el **esquema de negocio no
  está en migraciones**, así que un `tenants:migrate` masivo es de utilidad
  limitada; se prioriza poder **seleccionar tenant** para comandos puntuales
  (tinker, tareas de mantenimiento). Se implementa un resolutor de tenant en el
  arranque de consola; el comando envoltorio queda como opcional/YAGNI.

## Aislamiento — resumen

| Recurso | Mecanismo | Aislado por |
|---|---|---|
| Base de datos | conexión `tenant` reconfigurada + `default` | tenant |
| Usuarios/auth | tabla `users` propia de cada BD | BD (natural) |
| Ficheros subidos | `root`/`url` del disco `public` por prefijo | tenant (nuevo estrena carpeta) |
| Sesión | `session.cookie` por tenant + dominio | tenant/dominio |
| Caché | `cache.prefix` por tenant | tenant |
| Branding | carpeta de logos + `name` por tenant | tenant |

## Componentes nuevos / tocados

**Nuevos**
- `config/tenants.php`
- `app/Tenancy/TenantManager.php`
- `app/Http/Middleware/IdentifyTenant.php`
- `public/images/tenants/{granadaesnoticia,granadaenjuego}/...` (logos)
- Tests: unit de `resolveFromHost`; feature de conmutación por `Host`.

**Tocados**
- `app/Http/Kernel.php` — registrar `IdentifyTenant` el primero (global).
- `bootstrap`/console — resolución de tenant para CLI (`--tenant`/`TENANT`).
- `UserController`, `BannerController` — `asset('storage/...')` →
  `Storage::disk('public')->url(...)` y ruta física de avatar tenant-aware.
- Vistas de branding listadas en la sección 4.
- `.env` / `.env.example` — variables de los tenants.

## Pruebas

- **Unit** `TenantManager::resolveFromHost`: mapa de hosts conocidos + fallback
  al default para host desconocido.
- **Feature**: petición con `Host` A vs B →
  - `DB::connection()->getDatabaseName()` distinto por tenant,
  - prefijo de `Storage::disk('public')->url('x')` distinto,
  - URL/nombre de logo distinto en la vista.
- Config de test con dos conexiones de prueba (o sqlite) para no depender de
  las BD reales.

## Despliegue (resumen; detalle en `../../despliegue.md`)

Solo `main` despliega (a `admin.granadaesnoticia.com` hoy). Para activar el
segundo tenant, en el servidor:

1. **Plesk**: añadir `admin.granadaenjuego.com` como **alias del mismo vhost**
   (mismo docroot) + DNS.
2. **`.env`** de producción: variables `TENANT_ENJUEGO_DB_*` (y confirmar las
   `TENANT_ESNOTICIA_DB_*`).
3. **Storage**: crear `storage/app/public/tenants/granadaenjuego` (y
   `storage:link` sigue válido para las subcarpetas).
4. **Front de granadaenjuego** (repo aparte): desplegar y crear su **symlink**
   hacia la carpeta de storage del nuevo tenant, igual que el actual.

Nada de esto cambia el **mecanismo** de despliegue: sigue siendo un único push
a `main`.

## Fuera de alcance

- Migrar/mover ficheros del tenant actual (explícitamente evitado).
- Cambios en el proyecto del **front público** (repo aparte); solo se anota su
  dependencia de despliegue (symlink del segundo sitio).
- Panel de gestión de tenants en UI (YAGNI: se declaran en `config/tenants.php`).
- Herramienta de migraciones multi-tenant (el esquema no vive en migraciones).
