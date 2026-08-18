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

## Acciones tras el pull — POR CONFIRMAR

Falta documentar qué ejecuta Plesk **después** del `pull` (en Plesk: "Acciones
de despliegue adicionales"). Conviene dejar claro si en cada despliegue se
corre o no:

- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `npm ci && npm run build` (o si los assets de `public/build` se
      commitean ya compilados)
- [ ] `php artisan migrate --force` (recordar: el esquema de negocio `P0114_*`
      **no** está en migraciones; ver `database-schema.md`)
- [ ] `php artisan storage:link` (idempotente)
- [ ] `php artisan config:cache` / `route:cache` / `view:cache`

> Curro: rellena esta lista con lo que realmente tienes configurado en Plesk.

## Checklist antes de mergear a `main` (= desplegar)

1. Cambios probados en la rama de feature (local/Herd).
2. Assets compilados si aplica (`npm run build`), según cómo esté el punto
   anterior.
3. Migraciones/DDL revisadas: si hay cambios de esquema, aplicarlos según el
   flujo del proyecto (a mano / `../CAMBIOSDB.txt`), no se asume `migrate`.
4. `.env` de producción actualizado en el servidor si la feature añade
   variables nuevas (p. ej. las de multitenancy).

## Nota multitenancy (feature en curso)

Cuando entre el multitenant, `admin.granadaenjuego.com` se servirá desde el
**mismo vhost/código** (como `ServerAlias`, mismo docroot). Por tanto el
despliegue sigue siendo **uno solo** (un push a `main` actualiza ambos
dominios). Lo que cambia es la configuración del servidor (alias de dominio,
variables `.env` de la segunda BD, carpeta de storage y symlink del segundo
front), no el mecanismo de despliegue.
