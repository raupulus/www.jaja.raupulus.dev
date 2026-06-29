# Módulo de Comandos (Console/Commands)

## Descripción General
Este módulo contiene los comandos de consola (Artisan) personalizados que se ejecutan en segundo plano o mediante tareas programadas (cron) para el mantenimiento de la aplicación.

## Componentes
1. **SitemapGeneratorCommand (`app/Console/Commands/SitemapGeneratorCommand.php`)**:
   - Genera dinámicamente el `sitemap.xml` para la web pública.
   - Itera a través de páginas estáticas, grupos, tipos, categorías y cruces de taxonomías.
   - Previene errores de permisos en servidor y asegura la visibilidad SEO.

2. **StatsCommand (`app/Console/Commands/StatsCommand.php`)**:
   - Calcula asincrónicamente las estadísticas globales de la aplicación y las estadísticas de cada usuario/uploader.
   - Refresca los contadores de aportaciones para mostrarlos en el frontend.
   - Excluye del cómputo los contenidos eliminados o reportados.

3. **PublishContentToSocialCommand (`app/Console/Commands/PublishContentToSocialCommand.php`)**:
   - Permite la publicación automatizada de contenidos (chistes, adivinanzas) en las redes sociales conectadas al proyecto (Bluesky, Telegram, Mastodon, X/Twitter).
   - Puede ser invocado de forma manual desde el panel de administración o programado.

## Funcionalidad Técnica
- Estos comandos están registrados y configurados para ejecutarse periódicamente a través del programador de tareas de Laravel (`app/Console/Kernel.php` o `routes/console.php`).
- Son esenciales para separar la carga de cálculo y peticiones externas del ciclo de vida HTTP normal.
