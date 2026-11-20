# Módulo de Comandos (Console/Commands)

## Descripción General
Este módulo contiene los comandos de consola (Artisan) personalizados que se ejecutan en segundo plano o mediante tareas programadas (cron) para el mantenimiento de la aplicación.

## Componentes
1. **SitemapGeneratorCommand (`app/Console/Commands/SitemapGeneratorCommand.php`)**:
   - Genera dinámicamente el `sitemap.xml` para la web pública (acepta `--output=` para escribir en ruta alternativa durante pruebas o validaciones).
   - Itera a través de páginas estáticas publicadas, colaboradores con proyectos publicados, `Type::all()` para construir las rutas `/{type}/grupos`, grupos y categorías.
   - Excluye artefactos técnicos no indexables (`docs/collection.json`, `docs/openapi.yaml`) y utiliza fechas `lastmod` reales derivadas de los registros en base de datos y fecha de compilación de `docs`.
   - **Optimización SEO**: Filtra grupos y categorías utilizando el scope `hasAvailableContent()`, asegurando que no se indexen ni se añadan al sitemap URLs vacías que puedan provocar errores de tipo *Soft 404* en Google Search Console.
   - Previene errores de permisos en servidor y asegura la visibilidad SEO.

2. **StatsCommand (`app/Console/Commands/StatsCommand.php`)**:
   - Calcula asincrónicamente las estadísticas globales de la aplicación y las estadísticas de cada usuario/uploader.
   - Refresca los contadores de aportaciones para mostrarlos en el frontend.
   - Excluye del cómputo los contenidos eliminados o reportados.

3. **PublishContentToSocialCommand (`app/Console/Commands/PublishContentToSocialCommand.php`)**:
   - Permite la publicación automatizada de contenidos (chistes, adivinanzas) en las redes sociales conectadas al proyecto (Bluesky, Telegram, Mastodon, X/Twitter).
   - Puede ser invocado de forma manual desde el panel de administración o programado.

## Funcionalidad Técnica
- Estos comandos están registrados en `routes/console.php` usando la fachada explícita `Illuminate\Support\Facades\Schedule` y protegidos con `->withoutOverlapping()` (`stats:update`, `sitemap:generate` y las tres ventanas diarias de `content:publish-social`), además de poder lanzarse bajo confirmación (`POST` / modal) desde `App\Filament\Admin\Pages\Dashboard`.
- Los logs estructurados emitidos vía `Log::warning`/`Log::error` por todos los comandos se registran en **inglés** manteniendo la salida de consola para operadores en español.
- Son esenciales para separar la carga de cálculo y peticiones externas del ciclo de vida HTTP normal.
- Verificados en `tests/Feature/FactoriesAndCommandsTest.php` (`stats:update`, `content:publish-social` con `Http::fake()`, `sitemap:generate --output=...`), `tests/Feature/SeoMetadataAndSitemapTest.php` y `tests/Feature/ReliabilityAndArchitectureTest.php` (`schedule:list`).


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
