# Despliegue a producción

## Resumen

El despliegue es **automático por Git + webhook**. El repositorio está en
**GitHub** (`curringas/_GECOX2025`) y **Plesk** (extensión Git) está suscrito a
él mediante un **webhook**.

> **Solo se despliega la rama `main`, y su destino es `admin.granadaesnoticia.com`.**
> Ninguna otra rama toca producción.

Flujo:

```
push/merge a main  ──▶  GitHub dispara webhook  ──▶  Plesk hace pull
                                                  └▶  despliega en el vhost
```

**Consecuencia práctica: hacer merge o push a `main` = publicar en producción.**
Por eso todo el trabajo se hace en **ramas de feature**, y `main` solo se
actualiza cuando se quiere desplegar.

## Datos del entorno

| Elemento | Valor |
|---|---|
| Remoto | GitHub — `curringas/_GECOX2025` |
| Rama que despliega | **`main`** (única) |
| Dominio de producción | `admin.granadaesnoticia.com` |
| Panel/servidor | Plesk |
| Ruta del código en el servidor | `/var/www/vhosts/granadaesnoticia.com/admin/` |
| `.env` de producción | Vive **solo en el servidor**, no en el repo |
| Ficheros subidos | `storage/app/public/...`, enlazados al front por symlink (ver `../CLAUDE.md`) |

## Vía manual (secundaria)

Existe configuración **SFTP** en `.vscode/sftp.json` con `uploadOnSave: false`
(subida manual, desactivada por defecto). Es un mecanismo **alternativo/de
emergencia**, no el flujo normal. La lista `ignore` de ese fichero excluye
`.git`, `node_modules`, `*.md`, etc.

## Acciones tras el pull

Plesk tiene configuradas como **acciones de despliegue adicionales** (además de
otras internas que no se ven en el panel):

```bash
rm -f public/hot        # descarta el hot-file de Vite -> usa assets compilados
npm run build           # compila assets y copia resources/{images,...} a public/build/
php artisan storage:link
```

Notas:

- **`public/build` está en `.gitignore`** → los assets se compilan **en el
  servidor** (`npm run build`). Por eso los logos por tenant
  (`resources/images/tenants/...`) llegan solos a `public/build/...` sin pasos
  manuales. (La feature no añadió dependencias npm nuevas; `npm run build` sin
  `npm ci` basta si `node_modules` ya está.)
- **No hay migraciones**: el esquema `P0114_*` no está en migraciones
  (`database-schema.md`). Si cambia el esquema, se aplica a mano (`../CAMBIOSDB.txt`).
- **No hay dependencias PHP nuevas** por multitenancy; las clases `app/Tenancy/*`
  cargan por PSR-4 aunque no corra `composer install`.

**Recomendado añadir** a las acciones (limpieza, por seguridad):

```bash
php artisan config:clear   # o config:cache si se cachea config (necesario al añadir vars TENANT_*)
php artisan view:clear     # evita servir vistas Blade obsoletas
php artisan cache:clear    # prefijo de caché por tenant (limpia caché de spatie)
```

> **Config cacheada**: si en producción se usa `php artisan config:cache`, hay
> que **reconstruir la caché** tras cambiar el `.env` (nuevas vars `TENANT_*`);
> si no se cachea, `config/tenants.php` se lee en cada request.

## Checklist antes de mergear a `main` (= desplegar)

1. Cambios probados en la rama de feature (local/Herd).
2. Assets compilados si aplica (`npm run build`), según cómo esté el punto
   anterior.
3. Migraciones/DDL revisadas: si hay cambios de esquema, aplicarlos según el
   flujo del proyecto (a mano / `../CAMBIOSDB.txt`), no se asume `migrate`.
4. `.env` de producción actualizado en el servidor si la feature añade
   variables nuevas (p. ej. las de multitenancy).

## Modo mantenimiento

Hay **dos** mantenimientos distintos, no los confundas:

- **Toggle de la pestaña Laravel de Plesk** = `php artisan down` nativo →
  bloquea a **todos** (incluido tú). Laravel 11 ya no tiene `--allow=IP`; su
  equivalente es `--secret` (enlace de acceso). **No** uses este toggle si
  quieres poder entrar tú.
- **Modo mantenimiento propio por IP** (recomendado para ventanas de deploy) —
  implementado en `app/Http/Middleware/CheckMaintenanceMode.php` +
  `config/mantenimiento.php`. Se controla desde el `.env`:

  ```dotenv
  MODO_MANTENIMIENTO=true
  IPS_PERMITIDAS_EN_MANTENIMIENTO=TU.IP.PUBLICA,otra.ip   # sin puerto
  ```

  Con eso el público ve `mantenimiento.maintenance` (503, **con el branding del
  tenant** —logo y nombre según el dominio—) y solo pasan esas IPs. Aplica a
  **todas las rutas** (incluidas las protegidas por `auth`): `CheckMaintenanceMode`
  está priorizado en `Kernel` para ganar antes que el middleware de autenticación.
  Para levantar: `MODO_MANTENIMIENTO=false`. Si cacheas config, reconstruye la
  caché tras editar el `.env`.

  El middleware lee la **IP real** del cliente de la cabecera `X-Forwarded-For`
  (la que pone el proxy de Plesk) y, si no hay proxy, de `REMOTE_ADDR`. **No**
  cambia la config global de `TrustProxies`, así que el resto de producción se
  comporta igual que hoy. Averigua tu IP pública en `https://ifconfig.me`. Si te
  equivocas de IP, recuperas el acceso poniendo `MODO_MANTENIMIENTO=false`.

  > Nota: al leer `X-Forwarded-For` directamente, esta lista de IPs es una
  > cortesía para la ventana de despliegue, no un control de seguridad (la
  > cabecera es teóricamente falsificable). Suficiente para su propósito.

## Multitenancy (implementado)

Detalle completo en [`multitenancy.md`](multitenancy.md). El despliegue sigue
siendo **uno solo**: `admin.granadaenjuego.com` se sirve desde el **mismo
vhost/código** (alias del mismo docroot). Un push a `main` actualiza ambos
dominios. Lo que cambia entre tenants es **configuración de servidor**, no el
mecanismo de deploy.

### Fase A — subir el código (no-op para granadaesnoticia)

El cambio está diseñado para **no alterar** granadaesnoticia: misma BD
(`granadaen`, heredada de `DB_*`), mismo storage físico (prefijo `''`), mismas
URLs. Único efecto visible: la cookie de sesión pasa a `granadaesnoticia_session`
→ los admins **re-login una vez**.

1. En el `.env` del servidor (opcional por el fallback, recomendado):
   ```dotenv
   TENANT_DEFAULT=granadaesnoticia
   TENANT_ESNOTICIA_HOSTS=admin.granadaesnoticia.com
   ```
2. (Opcional) **modo mantenimiento propio por IP** para verificar en prod sin
   exponer (ver "Modo mantenimiento" más abajo).
3. Merge `feature/multitenant` → `main` → deploy.
4. Post-deploy: las acciones de Plesk + los `artisan *:clear` recomendados arriba.
5. Verificar: login, menú, publicaciones y **que el front sigue mostrando
   imágenes** (symlink intacto).
6. **Rollback**: `git revert` del merge en `main` → el webhook redespliega la
   versión anterior. El cambio no toca datos ni ficheros de esnoticia.

### Fase B — activar granadaenjuego (después, sin re-desplegar código)

1. Crear/importar la BD `granadaenjuego` (ajustes de `../CAMBIOSDB.txt`).
2. `.env` del servidor:
   ```dotenv
   TENANT_ENJUEGO_HOSTS=admin.granadaenjuego.com
   TENANT_ENJUEGO_DB_DATABASE=granadaenjuego
   TENANT_ENJUEGO_DB_USERNAME=...
   TENANT_ENJUEGO_DB_PASSWORD=...
   ```
   y `php artisan config:clear` (o `config:cache`).
3. Plesk: `admin.granadaenjuego.com` como alias del mismo vhost + DNS + **SSL**.
4. Crear `storage/app/public/tenants/granadaenjuego/` (`banners/`, `ficheros/`,
   `avatares/`) y el **symlink del front** de granadaenjuego hacia esa carpeta.
5. Asegurar que `npm run build` corrió (logos del tenant en `public/build/`).
6. Verificar `admin.granadaenjuego.com`.
