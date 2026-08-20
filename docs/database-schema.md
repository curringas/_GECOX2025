# Esquema de base de datos

> Estructura completa (sin datos) de la BD `granadaen`, extraída del dump del
> 2026-08-18. Solo columnas, PKs e índices; sin filas.

## Puntos clave

- **Prefijo heredado**: las tablas de negocio se llaman `P0114_*` (de un CMS
  anterior). Muchas de ellas **no las usa este proyecto** (ver "Tablas
  heredadas sin uso").
- **Motor y collation**: casi todas las `P0114_*` son **MyISAM** con collation
  **latin1** (`latin1_swedish_ci` / `latin1_spanish_ci`). Implicaciones:
  - Sin claves foráneas ni transacciones: las relaciones son **por
    convención**, no forzadas por la BD.
  - Cuidado al comparar/insertar texto acentuado (latin1 vs utf8mb4).
- **Excepción**: `P0114_publicacion` está en **utf8mb4** (`utf8mb4_unicode_ci`)
  y es InnoDB implícito por el charset — se migró su collation recientemente.
- **Sin migraciones de negocio**: el esquema `P0114_*` se importa; solo las
  tablas propias de Laravel están en `database/migrations`. Los ajustes para
  adaptar la BD antigua están en `../CAMBIOSDB.txt`.
- **PKs no estándar**: `Identificador`, `Ticker`, `Nombre`, o claves
  compuestas. Los modelos lo declaran explícitamente.

---

## Tablas activas (usadas por los modelos)

### `P0114_publicacion` — Noticias/artículos
Modelo `Publicacion`. PK `Identificador` (AUTO_INCREMENT). **utf8mb4**. Tiene
`created_at`/`updated_at` (timestamps activos).

Columnas: `Identificador`, `Creador`, `Fecha` (datetime), `Privacidad`,
`Activa`, `Pendiente`, `Url`, `Email` (para formularios), `Titulo`,
`Subtitulo`, `Contenido` (mediumtext), `TextoApoyo` (mediumtext),
`ContenidoCompleto`, `IconoImprimir`, `IconoEnviar`, `IconoRelacionados`,
`IconoValorar`, `Activacion` (date), `Desactivacion` (date), `Notas`,
`Introduccion`, `FechaInicio`, `FechaFin`, `FechaSalida`, `Autor`, `Lugar`,
`Logotipo`, `LugarTipo`, `Video`, `LlevaComentarios`, `GaleriaURL` (longtext),
`Keywords`, `Visitas` (bigint), `AutorTwitter`, `AutorEmail`, `MetaTitle`,
`MetaDescription`, `created_at`, `updated_at`.

### `P0114_pagina` — Categorías / secciones
Modelo `Categoria`. PK `Identificador`. Jerárquica vía `Padre`.

Columnas: `Identificador`, `Etiqueta`, `Titulo`, `Explicativo`, `Boliche`,
`Menu`, `Privacidad`, `Cabecera`, `Estatico`, `Externo`, `SoloEtiqueta`,
`Target`, `Creador`, `Fecha`, `Orden`, `Padre`, `Bloques`, `Visitas`,
`ExplicativoProductos`, `Url`, `MetaTitle`, `MetaDescription`.

### `P0114_banner` — Banners
Modelo `Banner`. PK `Identificador` (AUTO_INCREMENT).

Columnas: `Identificador`, `Banner` (imagen), `Titulo`, `URL`, `Tipo`
(0 pequeño / 1 mediano / 2 grande), `Creador`, `Fecha`, `Target`, `Codigo`
(text), `BannerMovil` (imagen móvil).

### `P0114_portada` — Configuración global de portada
Modelo `Portada`. PK declarada en el modelo: `Ticker`. Contiene ticker,
título/contenido destacado, foto, intersticial y **banners de zonas**
(cabecera, izquierda, derecha, pie), cada uno con Titulo/Imagen/Url/Destino/
CodigoFuente y su variante `...ImagenMovil`.

### `P0114_portada_central` / `_izquierda` / `_derecha` / `_slider` — Columnas de portada
Modelos `PortadaCentral`, `PortadaIzquierda`, `PortadaDerecha`,
`PortadaSlider`. Misma estructura, PK `Identificador` (AUTO_INCREMENT).

Columnas: `Identificador`, `Publicacion`, `Imagen`, `Automatico`,
`BannerImagen`, `BannerTitulo`, `BannerUrl`, `BannerDestino`,
`BannerCodigoFuente`, `Orden`, `BannerImagenMovil`.

### `P0114_imagenenpublicacion` — Imágenes de una publicación
Modelo `ImagenEnPublicacion` (sin PK en el modelo; PK real compuesta
`Publicacion,Orden`).

Columnas: `Imagen`, `Descripcion`, `Ancho`, `Publicacion`, `Orden`,
`Repositorio`.

### `P0114_documentoenpublicacion` — Documentos adjuntos
Modelo `DocumentoEnPublicacion` (sin PK en el modelo; PK real compuesta
`Publicacion,Orden`).

Columnas: `Documento`, `Tamano`, `Nombre`, `Publicacion`, `Icono`, `Orden`.

### `P0114_indexado` — Config de indexación/SEO y redes
Modelo `Indexado`. PK declarada en el modelo: `Nombre`.

Columnas: `Nombre`, `Descripcion`, `Keywords`, `Facebook`, `Twitter`,
`Google`, `Youtube`, `Instagram`, `ContadorFacebook`, `ContadorTwitter`,
`ContadorInstagram`, `ContadorTelegram`.

---

## Tablas pivote / relaciones (por convención, sin FK)

| Tabla | Relación | PK / claves |
|---|---|---|
| `P0114_publicacionpagina` | Publicación ↔ Página (categoría) | PK `Ultima` (auto); UNIQUE(`Publicacion`,`Pagina`); `Orden` |
| `P0114_bannerenpagina` | Banner ↔ Página | PK(`Banner`,`Pagina`,`Posicion`); `Orden` |
| `P0114_bannerenportada` | Banner ↔ Portada | PK(`Banner`,`Posicion`); `Orden` |
| `P0114_relacionadaenpublicacion` | Publicación ↔ Publicación relacionada | PK(`Origen`,`Relacionada`) |
| `P0114_preguntaenpublicacion` | Preguntas/encuesta de una publicación | PK `Identificador`; `Opcion1..8` |

> Nota: `Publicacion` ↔ `Categoria` (`P0114_pagina`) es muchos-a-muchos vía
> `P0114_publicacionpagina` (`belongsToMany` en el modelo `Publicacion`).

---

## Tablas de Laravel (en migraciones)

- **Autenticación**: `users`, `password_resets`, `personal_access_tokens`
  (Sanctum), `failed_jobs`, `migrations`.
- **users** tiene columnas propias del proyecto: `dob`, `avatar` (text),
  **`activo`** (tinyint, default 1 → usado por el middleware `CheckActive`),
  además de las estándar (`name`, `email`, `email_verified_at`, `password`,
  `remember_token`, timestamps).
- **Permisos (spatie/laravel-permission)**: `roles`, `permissions`,
  `model_has_roles`, `model_has_permissions`, `role_has_permissions`. Estas sí
  tienen FKs (InnoDB) con `ON DELETE CASCADE`.

---

## Tablas heredadas sin uso (del CMS antiguo)

No tienen modelo en este proyecto; provienen del sistema anterior. Se listan
para referencia (candidatas a limpieza si se confirma que no se usan ni en el
front):

`P0114_empresa`, `P0114_persona`, `P0114_comentario`, `P0114_busquedas`,
`P0114_general`, `P0114_interes`, `P0114_interespersona`, `P0114_newsletter`,
`P0114_newsletter_interes`, `P0114_newsletter_usuario`,
`P0114_newsletter_usuariointeres`.

> Algunas podrían seguir usándose desde el **front público** (p. ej.
> comentarios o newsletter). Verificar antes de eliminar.

---

## Publicaciones — versiones de imagen

`PublicacionController` genera con intervention/image varias versiones y las
guarda en el disco `public`: `_original` (1200px), `_ppal` (800px), `_portada`
(400px) y `_thumb`. El front reconstruye la URL a partir del nombre guardado y
su convención de carpetas (`ficheros/{carpeta}/{fichero}_{tipo}.{ext}`).
