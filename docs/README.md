# Documentación — Panel GECOX 2025

Índice de la documentación del proyecto. La guía rápida para trabajar está en
`../CLAUDE.md`.

## Contenido

- [`database-schema.md`](database-schema.md) — Esquema de la base de datos:
  tablas de negocio (`P0114_*`), mapeo de modelos y notas de collation.
- [`despliegue.md`](despliegue.md) — Despliegue a producción: **solo `main`**
  despliega a `admin.granadaesnoticia.com` vía webhook de GitHub → Plesk.
- `superpowers/specs/` — Especificaciones de features (diseño previo a
  implementar). Aquí irá el spec de **multitenancy**.

## Notas transversales

- El esquema de negocio **no** se gestiona con migraciones de Laravel; se
  importa y se modifica a mano (ver `../CAMBIOSDB.txt`).
- El front público es un proyecto aparte que comparte BD e imágenes. Ver
  `../CLAUDE.md` → "Acoplamiento con el front".
