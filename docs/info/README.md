# Documentación Técnica (`docs/info/`)

Índice maestro de la documentación viva de **JaJa Project**.
Jerarquía de verdad sobre el estado actual: **código > `docs/info/` > `AGENTS.md` > el resto**.

## Módulos de dominio

| Módulo | Archivo | Descripción |
|---|---|---|
| API REST | [API.md](API.md) | Arquitectura general de la API v1 (Sanctum, rate limits, formatos JSON, Scribe). |
| Contenidos | [Content.md](Content.md) | Módulo base de contenidos (chistes, adivinanzas, quiz) y sus opciones. |
| Sugerencias | [Suggestion.md](Suggestion.md) | Sistema de sugerencias y aportaciones comunitarias. |
| Taxonomías | [Taxonomy.md](Taxonomy.md) | Clasificación de contenidos (`Type`, `Group`, `Category`). |
| Usuarios | [User.md](User.md) | Usuarios, autenticación, roles y estadísticas de aportantes. |
| Colaboradores | [Collaborator.md](Collaborator.md) | Directorio de colaboradores open source y sus proyectos. |
| Reportes | [Report.md](Report.md) | Sistema de reportes y moderación de contenido. |
| Páginas CMS | [Page.md](Page.md) | Gestión de páginas estáticas, legales y metadatos SEO. |
| Acciones | [Actions.md](Actions.md) | Acciones reutilizables y clases de negocio (ej. conversión WebP). |
| Comandos y cron | [commands.md](commands.md) | Comandos Artisan, tareas programadas y scripts de soporte. |

## Frontend y diseño

- [DESIGN.md](DESIGN.md) — Sistema visual, estilos CSS modulares, Tailwind CSS v4 y tipografía.
- [COMPONENTS.md](COMPONENTS.md) — Layouts, parciales Blade y scripts JS del frontend público.

## Integraciones externas

- [apis/recaptcha.md](apis/recaptcha.md) — Integración de Google reCAPTCHA v3 mediante `VerifyRecaptcha`.

## Referencia y mantenimiento

- [decisiones-tecnicas.md](decisiones-tecnicas.md) — Decisiones deliberadas de arquitectura y comportamiento.
- [_MODULE_TEMPLATE.md](_MODULE_TEMPLATE.md) — Plantilla obligatoria al documentar un módulo nuevo.

## Quality Gate y verificación

Comandos obligatorios de calidad y comprobación antes de cada commit o paso a producción:

```bash
./vendor/bin/pint --test
composer phpstan
php artisan test --compact
composer test:pgsql
composer audit --no-interaction
npm audit
npm run build
php artisan route:cache && php artisan config:cache && php artisan view:cache && php artisan optimize:clear
```

- **Estilo (`pint`)**: formateo Laravel Pint (`composer pint` en modo `--test` o `./vendor/bin/pint` para corregir).
- **Análisis estático (`phpstan.neon`)**: Larastan nivel 5 sobre `app/`, `config/` y `routes/` ejecutado vía `composer phpstan`.
- **Tests en SQLite (`phpunit.xml`)**: suite rápida en memoria con `php artisan test --compact`.
- **Paridad en PostgreSQL (`phpunit.pgsql.xml`)**: ejecuta toda la suite contra PostgreSQL real vía `composer test:pgsql` (equivalente a `php artisan test -c phpunit.pgsql.xml`).
  - Requisitos locales o en CI: base de datos `jaja_raupulus_test` existente y variables `DB_CONNECTION=pgsql`, `DB_HOST=127.0.0.1`, `DB_PORT=5432`, `DB_DATABASE=jaja_raupulus_test`, `DB_USERNAME=dev`, `DB_PASSWORD=""` (sobreescribibles por entorno sin `force="true"` en `phpunit.pgsql.xml`).
  - Creación local rápida: `createdb -U dev jaja_raupulus_test` (o `CREATE DATABASE jaja_raupulus_test OWNER dev;`).
  - Todas las 12 tablas principales disponen de su correspondiente `Model Factory` bajo `database/factories/`.

---
> Creado: 2026-11-20 · Última revisión: 2026-11-20
