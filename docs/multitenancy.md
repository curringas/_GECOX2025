# Multitenancy por dominio

El panel es **multitenant**: el mismo código y vhost sirve a varios periódicos
(`granadaesnoticia`, `granadaenjuego`), conectando a una **BD**, **storage** y
**branding** distintos según el `Host` de la petición.

- Spec de diseño: [`superpowers/specs/2026-08-18-multitenancy-design.md`](superpowers/specs/2026-08-18-multitenancy-design.md)
- Plan de implementación (TDD): [`superpowers/plans/2026-08-18-multitenancy.md`](superpowers/plans/2026-08-18-multitenancy.md)

## Cómo funciona (resumen)

1. **Registro de tenants**: `config/tenants.php` define cada tenant (`hosts`,
   `db`, `storage`, `logo`, `name`). Los secretos y los hosts salen del `.env`.
2. **Resolución por host**: el middleware **global** `App\Http\Middleware\IdentifyTenant`
   (registrado **el primero** en `app/Http/Kernel.php`, antes de `StartSession`)
   toma `request->getHost()`, resuelve el tenant y llama a `TenantManager::configure()`.
3. **`App\Tenancy\TenantManager::configure($tenant)`** reconfigura en runtime:
   - **BD**: crea la conexión `tenant` (a partir de la plantilla `mysql`) y pone
     `database.default = 'tenant'`. **Todos los modelos usan la conexión por
     defecto**, así que no se tocan.
   - **Storage**: reapunta el disco `public` (`root` y `url`) según el prefijo
     `storage` del tenant.
   - **Sesión**: `session.cookie = "{tenant}_session"` (aislamiento de login).
   - **Caché**: `cache.prefix = {tenant}` (aísla, entre otras, la caché de spatie).
   - **Branding**: comparte a las vistas la variable `$tenant`
     (`App\Tenancy\Tenant`, con `name()` y `logo()`).
4. **Consola**: en `AppServiceProvider::boot()`, si `runningInConsole()`, se
   resuelve el tenant por `--tenant=X` > env `TENANT` > default
   (`TenantManager::resolveFromConsole`). Ej.: `php artisan xxx --tenant=granadaenjuego`.

Host desconocido → **tenant por defecto** (`TENANT_DEFAULT`, = `granadaesnoticia`).

## Configuración por `.env`

Los **hosts** y las **BD** son env-driven para que **local ≠ producción** sin
tocar código:

```dotenv
TENANT_DEFAULT=granadaesnoticia

# Hosts por tenant (coma-separados). Producción: dominios reales.
# En LOCAL se añaden los .test de Herd.
TENANT_ESNOTICIA_HOSTS=admin.granadaesnoticia.com          # local: ...com.test,granadaesnoticia.test
TENANT_ENJUEGO_HOSTS=admin.granadaenjuego.com              # local: ...com.test,granadaenjuego.test

# BD por tenant. esnoticia hereda de DB_* si no se define (host/port/db/user/pass).
TENANT_ESNOTICIA_DB_DATABASE=granadaen
TENANT_ESNOTICIA_DB_USERNAME=
TENANT_ESNOTICIA_DB_PASSWORD=

TENANT_ENJUEGO_DB_DATABASE=granadaenjuego
TENANT_ENJUEGO_DB_USERNAME=
TENANT_ENJUEGO_DB_PASSWORD=
# TENANT_*_DB_HOST / _PORT: si no se definen, heredan de DB_HOST / DB_PORT.
```

> **Importante**: si el tenant por defecto (`esnoticia`) no define sus
> `TENANT_ESNOTICIA_DB_*`, hereda `DB_*` (incluido `DB_HOST`/`DB_PORT`). Por eso
> desplegar la feature es un **no-op** para granadaesnoticia: sigue en `granadaen`.

## Storage aislado — CUIDADO con el front

- Prefijo `storage` por tenant:
  - `granadaesnoticia` → **`''`** (raíz `storage/app/public`, **igual que
    siempre**). El **front** lee estas imágenes por **symlink** a esa carpeta,
    así que **NO** debe cambiar. Ver `../CLAUDE.md` → "Ficheros e imágenes".
  - `granadaenjuego` → **`tenants/granadaenjuego`** (subcarpeta aislada; su front
    tendrá su propio symlink a `storage/app/public/tenants/granadaenjuego`).
- Un único symlink `public/storage` sirve a ambos (esnoticia en raíz, enjuego en
  la subcarpeta). `php artisan storage:link` basta.
- Las subidas usan `Storage::disk('public')->url(...)` / `->path(...)` (no
  `asset('storage/...')`), para que respeten el prefijo del tenant activo.

## Logos y branding

- El helper `Tenant::logo('fichero')` busca en
  **`public/build/images/tenants/{slug}/{fichero}`** y, si no existe, cae en el
  logo por defecto `public/build/images/{fichero}`.
- Los **fuente** viven en **`resources/images/tenants/{slug}/`** y **Vite**
  (`viteStaticCopy`) los copia a `public/build/images/tenants/{slug}/` en
  `npm run build` (mismo pipeline que el resto de imágenes). El slug es el valor
  `logo` de cada tenant en `config/tenants.php`.
- Cada tenant usa 4 ficheros (nombres exactos, los referencian las vistas):
  `logo-dark.png` (fondos claros: auth, topbar plegado), `logo-light.png`
  (fondos oscuros: sidebar, portada), `logo.svg` (topbar expandido claro),
  `logo-light.svg` (topbar expandido oscuro).
- **Variante para el sidebar oscuro**: `logo-light.*` debe tener **fondo
  transparente** y el texto en **blanco** (el color de acento —p. ej. el naranja—
  se mantiene). Los logos de marca suelen venir con fondo blanco (incluso JPEG);
  para generarlos se hace *unpremultiply* del blanco → alpha y se recolorea lo
  oscuro a blanco (se usó GD; el naranja se detecta por tono cálido y se conserva).
- El **título** de la pestaña sale de `$tenant->name()` (en `master.blade.php` y
  `master-without-nav.blade.php`).

Para añadir/cambiar un logo: deja el fichero en `resources/images/tenants/{slug}/`
y ejecuta `npm run build`. En el servidor lo hace la acción de deploy de Plesk.

## Añadir un tenant nuevo

1. Nueva entrada en `config/tenants.php` (`name`, `hosts` vía env, `db`,
   `storage` con su subcarpeta, `logo`).
2. Variables `TENANT_<X>_HOSTS` y `TENANT_<X>_DB_*` en el `.env` del servidor.
3. Crear/importar su BD (ajustes de `../CAMBIOSDB.txt` si viene de una heredada).
4. Alias del dominio en Plesk (mismo vhost/docroot) + DNS + SSL.
5. Carpeta `storage/app/public/tenants/<x>/` (con `banners/`, `ficheros/`,
   `avatares/`) y symlink del front correspondiente.
6. Logos en `resources/images/tenants/<x>/` + `npm run build`.

## Tests

`tests/Unit/Tenancy/` y `tests/Feature/Tenancy/`. **No** dependen de las BD
reales: fijan `config('tenants')` con un fixture y verifican config / Storage /
branding (sin ejecutar queries). Ejecutar con `php artisan test`.

## Aislamiento verificado (sesión de 2026-08)

Probado end-to-end (Playwright + diff de conteos en ambas BDs): crear/editar en
Perfiles, Permisos, Usuarios, Categorías, Banners, Publicaciones e Indexado
**solo** modifica la BD del tenant en cuestión (y el storage del banner va a la
subcarpeta del tenant). Verificado también en sentido inverso. Garantía a nivel
de código: **ningún modelo fija `protected $connection`** ni hay
`DB::connection('mysql')`/`setConnection` en `app/`.

## Gotchas aprendidos

- **Vistas Blade compiladas**: NO deben versionarse. Faltaba el `.gitignore` de
  `storage/framework/views/` (y la ruta del `.gitignore` raíz era errónea);
  había 60 vistas compiladas commiteadas que servían HTML obsoleto tras cambios.
  Corregido. En deploy, el `git pull` las borra y Laravel recompila; aun así
  conviene `php artisan view:clear`.
- **Menú lateral por rol, no por tenant**: las secciones Backend/Administración/
  Configuración se gatean con `@role('Super-admin|Admin')` /
  `@role('Super-admin')` en `resources/views/layouts/sidebar.blade.php`. Si a un
  usuario "le faltan secciones" es por su rol, no por multitenancy.
- **Cookie de sesión por tenant**: al desplegar el cambio, los admins logueados
  tendrán que **volver a entrar una vez** (cambia el nombre de la cookie).
- **Logos fuente pueden ser JPEG** aunque tengan extensión `.png` (los de
  `resources/images/logo-*.png` lo son). JPEG no tiene transparencia.

## Local con Herd

Alias del mismo directorio a varios dominios:

```bash
herd link granadaesnoticia   # + en .env: TENANT_ESNOTICIA_HOSTS con el .test
herd link granadaenjuego
```

En local se usó `admin.granadaesnoticia.com.test` / `admin.granadaenjuego.com.test`
(añadidos a `TENANT_*_HOSTS` del `.env` local). Las dos BDs locales son clones
para pruebas.
