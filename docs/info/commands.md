# Módulo de Comandos Artisan y Tareas Programadas

## Qué hace y qué NO hace
- **Qué hace**: Automatiza mediante Artisan y el planificador de Laravel (`routes/console.php`) la regeneración del `sitemap.xml`, el recálculo cacheado de estadísticas y la publicación programada en redes sociales, exponiendo también disparadores confirmados en el dashboard de `/admin`.
- **Qué NO hace**: No publica contenido marcado `is_adult = true` ni de grupos excluidos (`[4, 14]`), ni ejecuta tareas concurrentes solapadas (`withoutOverlapping()`).

## Modelo de datos
- No crea tablas propias; lee `Type`, `Group`, `Category`, `Content`, `Suggestion`, `Page`, `Collaborator`, `CollaboratorProject` y `User`, actualizando `contents.last_social_published` al publicar en redes y claves de caché en `StatsHelper`.

## Flujos principales
1. **`sitemap:generate` (`SitemapGeneratorCommand`)**:
   - Acepta `{--force}`, `{--chunk=100}` y `{--output=}` (útil para validaciones en ruta temporal).
   - Incluye rutas estáticas públicas, páginas publicadas, colaboradores con proyectos publicados, `Type::all()` y grupos/categorías filtrados con `publicSafe()` y `hasAvailableContent()`, excluyendo `docs/collection.json` y `docs/openapi.yaml`.
   - Realiza copia temporal `.bak` y la elimina al verificar escritura válida.
2. **`stats:update` (`StatsCommand`)**:
   - Refresca las claves `suggestions_pending_count`, `suggestions_accepted_count`, `contents_count`, `users_active_total`, `users_more_active_10`, `users_more_active_20` y `types_and_groups_and_categories_count` vía `StatsHelper`.
3. **`content:publish-social` (`PublishContentToSocialCommand`)**:
   - Selecciona el primer `Content::publicSafe()` priorizando `last_social_published IS NULL` y después los más antiguos, y lo envía mediante `PublishToSocialHelper::publishContent()`.

## Puntos de entrada
- **CLI Artisan**: `php artisan sitemap:generate`, `php artisan stats:update`, `php artisan content:publish-social`.
- **Scheduler (`routes/console.php`)**:
  - `stats:update` cada 30 minutos (`->withoutOverlapping()`).
  - `sitemap:generate` diario a las `02:00` (`->withoutOverlapping()`).
  - `content:publish-social` diario a las `07:00`, `12:00` y `18:00` (`->withoutOverlapping()`).
- **Web Admin (`AdminController` / `Admin\Pages\Dashboard`)**: acciones `POST /admin/action/generate/sitemap` y `POST /admin/action/generate/stats` protegidas por `['auth', 'isAdmin']` y modal de confirmación.

## Dependencias
- **Depende de**: `spatie/laravel-sitemap`, `PublishToSocialHelper`, `StatsHelper`, `Content::scopePublicSafe()`, `Group`/`Category::scopeHasAvailableContent()`.
- **Usado por**: Cron de producción, dashboard de administración y pruebas de comandos.

## Configuración
- `SOCIAL_PUBLISHER_URL`, `SOCIAL_PUBLISHER_CONNECT_TIMEOUT`, `SOCIAL_PUBLISHER_TIMEOUT` (`config/services.php`).
- `CONTENT_EXCLUDED_PUBLIC_GROUP_IDS` (`config/content.php`).

## Trampas conocidas
- `sitemap:generate` mantiene un lock de caché (`sitemap_generation_lock`) durante la ejecución; en ejecuciones de prueba repetidas conviene pasar `--force` y siempre `--output=<ruta_temporal>` para no sobrescribir `public/sitemap.xml`.
- En `PublishContentToSocialCommand`, se debe mantener `orderByRaw('last_social_published IS NULL DESC')`, sintaxis compatible tanto con PostgreSQL como con SQLite.
- Los logs de error en comandos van en inglés (`Log::error` / `Log::warning`) aunque los mensajes de consola para el operador estén en español.

## Tests que lo cubren
- `tests/Feature/FactoriesAndCommandsTest.php`
- `tests/Feature/SeoMetadataAndSitemapTest.php`
- `tests/Feature/ReliabilityAndArchitectureTest.php`
- `tests/Feature/FilamentPanelsUxTest.php`

## Pendiente real
- Sin deuda técnica pendiente del alcance actual.

---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
