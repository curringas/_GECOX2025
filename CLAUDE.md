# CLAUDE.md — Panel de administración GECOX 2025

Guía para trabajar en este proyecto. Léela antes de tocar código.

## Qué es

Panel de administración / CMS (**"Palabrea CMS"**) del periódico digital
**Granada Es Noticia**. Gestiona noticias, banners, categorías/secciones, la
maquetación de la portada y los usuarios. Está en producción.

Es **multitenant** (implementado): el mismo código y vhost sirve también a
**Granada En Juego**, conectando a una BD, storage y branding distintos según el
dominio de acceso (`admin.granadaesnoticia.com` / `admin.granadaenjuego.com`).
**Lee [`docs/multitenancy.md`](docs/multitenancy.md)** antes de tocar BD, storage,
logos o el arranque de la app. Resumen: un middleware global (`IdentifyTenant`)
resuelve el tenant por `Host` y `TenantManager` reconfigura en runtime la conexión
por defecto, el disco `public`, la cookie de sesión, el prefijo de caché y el
branding. Hosts y credenciales salen del `.env` (`TENANT_*`).

El **front público** es un proyecto aparte (PHP plano), no este repo. Ver
"Acoplamiento con el front" más abajo.

## Stack

- **PHP 8.2+ / Laravel 11**, MySQL.
- **Auth**: Laravel Auth con verificación de email; roles y permisos con
  **spatie/laravel-permission**. Middleware `active` (CheckActive) para
  bloquear usuarios. Modo mantenimiento propio con lista de IPs permitidas.
- **Tablas de datos**: **yajra/laravel-datatables** (server-side).
- **Imágenes**: **intervention/image** (redimensionado de publicaciones).
- **Exportación**: **maatwebsite/excel**.
- **Frontend**: plantilla **Skote** (Themesbrand) sobre **Bootstrap 5**,
  **Vite**, **SASS**, jQuery y DataTables.

## Comandos

```bash
composer install
npm install

npm run dev            # Vite en desarrollo (assets)
npm run build          # Compilar assets para producción

php artisan serve      # o usar Herd con dominios .test
php artisan storage:link   # necesario para servir imágenes subidas

php artisan test       # PHPUnit. Hay suite de multitenancy en
                       # tests/{Unit,Feature}/Tenancy (no tocan las BD reales).
                       # El Feature\ExampleTest de scaffold falla (asume / -> 200,
                       # pero / está tras auth): preexistente, no es regresión.
```

**Local con Herd**: el proyecto se sirve por el nombre de carpeta. Para
probar multitenancy se enlazan varios alias a la misma carpeta:

```bash
herd link granadaesnoticia   # -> granadaesnoticia.test
herd link granadaenjuego     # -> granadaenjuego.test
```

## Base de datos — IMPORTANTE

- Las tablas de negocio usan el prefijo heredado **`P0114_`** y collation
  **latin1** (no utf8mb4). Ejemplos: `P0114_publicacion`, `P0114_banner`,
  `P0114_pagina`, `P0114_portada`.
- **El esquema de negocio NO está en migraciones.** Solo existen migraciones
  para las tablas propias de Laravel (users, password_resets, jobs, sanctum,
  permisos, customers). `php artisan migrate` **no** construye el dominio: la
  BD se importa desde el sistema antiguo.
- **`CAMBIOSDB.txt`** es el checklist de **modificaciones que hay que aplicar a
  la BD antigua** (DDL: `ALTER TABLE`, cambios de collation, ajustes de PK…)
  para adaptarla y que funcione con este proyecto nuevo. Es la referencia al
  migrar/importar una base heredada. Es un fichero del proyecto (no versionado
  como código de migración); mantenlo al día si aparecen nuevos ajustes
  necesarios para la conversión.
- Los modelos mapean las tablas explícitamente. No asumas convenciones
  Eloquent por defecto:

  | Modelo | Tabla | PK |
  |---|---|---|
  | `Publicacion` | `P0114_publicacion` | `Identificador` |
  | `Categoria` | `P0114_pagina` | `Identificador` |
  | `Banner` | `P0114_banner` | (ver modelo) |
  | `Portada` | `P0114_portada` | `Ticker` |
  | `PortadaCentral` / `Izquierda` / `Derecha` / `Slider` | `P0114_portada_*` | `Identificador` |
  | `ImagenEnPublicacion` | `P0114_imagenenpublicacion` | (sin PK) |
  | `DocumentoEnPublicacion` | `P0114_documentoenpublicacion` | (sin PK) |
  | `Indexado` | `P0114_indexado` | `Nombre` |

- Documentación de esquema en `docs/database-schema.md`.

## Convenciones

- **Idioma español** en todo: nombres de columnas (`Titulo`, `Contenido`,
  `Identificador`, `Activa`…), UI, comentarios y **mensajes de commit**.
  Mantén ese estilo.
- Columnas en **PascalCase español** (no snake_case).
- Rutas en `routes/web.php`: en su mayoría explícitas (no `Route::resource`
  completo) y con muchos endpoints AJAX para el constructor de portada.

## Ficheros e imágenes — cuidado al mover

- Las subidas van al disco `public`
  (`storage/app/public/{avatares,banners,ficheros}`) y se sirven por el
  symlink `public/storage`.
- **El front público lee estas imágenes por ruta física**, no por la URL
  `/storage/` del admin: en el servidor hay un **symlink** desde la carpeta
  del front (p. ej. `ficheros/`) hacia el storage real del admin. El admin es
  la fuente de la verdad de los ficheros.
- Consecuencia: **cambiar dónde escribe físicamente el admin puede romper el
  front**. No cambies las rutas de escritura del tenant en producción sin
  actualizar el symlink correspondiente.

## Assets, vistas y logos (Vite)

- **Vite es dueño de `public/build/`** (`outDir`): `npm run build` **vacía** esa
  carpeta y `viteStaticCopy` copia `resources/{images,fonts,js,libs,json}` a
  `public/build/`. No pongas nada a mano en `public/build/` (se borra). Las
  imágenes fuente van en **`resources/images/`**. `public/build/` está en
  `.gitignore` (se compila en el servidor: la acción de deploy corre `npm run build`).
- **Logos por tenant**: `resources/images/tenants/{slug}/` → Vite los copia a
  `public/build/images/tenants/{slug}/`. Ver [`docs/multitenancy.md`](docs/multitenancy.md).
- **Vistas Blade compiladas NO se versionan** (`storage/framework/views/*.php`,
  ignoradas). Si aparecen trackeadas, es un bug: sirven HTML obsoleto tras editar
  Blade. En caso de duda: `php artisan view:clear`.
- Ojo: algunos logos de `resources/images/logo-*.png` **son JPEG** pese a la
  extensión `.png` (sin transparencia).

## Acoplamiento con el front

- Front público: proyecto PHP plano independiente
  (`front.granadaesnoticia.com`), fuera de este repo.
- Comparte la **misma base de datos** (`granadaen`) y las **mismas imágenes**
  (vía symlink). Guarda en la tabla solo el nombre/ruta del fichero y el front
  construye la URL con su propia convención (`ficheros/…`, `imagenes/…`).
- Al tocar esquema o ubicación de ficheros, ten presente el impacto en el
  front.

## Despliegue

Detalle completo en [`docs/despliegue.md`](docs/despliegue.md). Resumen:

- **Método principal: Git + webhook.** El repo está en **GitHub**
  (`curringas/_GECOX2025`). Un **push a `main`** dispara un **webhook** que
  Plesk recibe para hacer `pull` y desplegar automáticamente en producción.
  Es decir: **mergear/pushear a `main` = desplegar**. Trabaja las features en
  ramas y no toques `main` hasta querer publicar.
- **Destino**: Plesk, vhost `/var/www/vhosts/granadaesnoticia.com/admin/`.
- **SFTP** (`.vscode/sftp.json`, `uploadOnSave: false`) es una vía **manual y
  secundaria**, no el flujo normal.
- Modo mantenimiento por IP: variable `IPS_PERMITIDAS_EN_MANTENIMIENTO` en
  `.env`.
- El `.env` de producción vive **solo en el servidor** (no en el repo).

## Git

- Rama principal: `main`. Trabaja las features en ramas aparte.
- Commits en español, siguiendo el estilo del historial (`fix:`, `feat:`,
  o descripción directa).
