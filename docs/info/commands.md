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
- Estos comandos están registrados y configurados para ejecutarse periódicamente a través del programador de tareas de Laravel (`app/Console/Kernel.php` o `routes/console.php`), así como desde acciones confirmadas (`POST` / modal de confirmación con control `try/catch` y `Notification`) en `App\Filament\Admin\Pages\Dashboard`.
- Son esenciales para separar la carga de cálculo y peticiones externas del ciclo de vida HTTP normal.


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
