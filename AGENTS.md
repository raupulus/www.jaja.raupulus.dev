# Reglas y Guías para Agentes en JaJa Project

Este archivo define las reglas específicas para agentes de inteligencia artificial cuando trabajen en este proyecto.

## Contexto del Proyecto

- **Framework**: Laravel (PHP)
- **Backend Admin/Panel**: Filament PHP
- **Autenticación API**: Laravel Sanctum
- **Propósito**: Plataforma comunitaria para gestionar y consumir chistes, preguntas (quiz) y adivinanzas mediante API y web.

## Reglas de Arquitectura y Código

1. **Patrones de Diseño**: Sigue siempre las convenciones de Laravel. Usa Eloquent ORM para base de datos y relaciones.
2. **Filament**: Para el panel de administración (`/admin`) y panel de usuario (`/panel`), utiliza los Recursos (Resources) de Filament. Configura correctamente los formularios y tablas en Filament en lugar de crear vistas Blade manuales cuando sea posible.
3. **API REST**:
   - Devuelve siempre respuestas JSON con los headers `Accept: application/json` y `Content-type: application/json`.
   - Utiliza `Scribe` para documentar los endpoints. Tras realizar cambios, ejecuta `php artisan scribe:generate`.
   - Considera el uso de API Resources para transformar las colecciones y modelos Eloquent en JSON de manera limpia.
4. **Validaciones**: Utiliza *Form Requests* para validaciones de entrada, especialmente en la API y envíos (como crear sugerencias).
5. **Taxonomías y Clasificación**:
   - El contenido se clasifica en **Grupos** (ej. Chistes, Adivinanzas), **Tipos** (ej. Normal, Quiz) y **Categorías**.
   - Los contenidos tipo **Quiz** requieren opciones asociadas (`ContentOption`, `SuggestionOption`).
6. **Seguridad**:
   - Endpoints privados deben estar protegidos con middleware `auth:sanctum`.
   - Los endpoints públicos están sujetos a rate limiting.
   - Las validaciones de rol deben ser comprobadas en la base de datos (actualmente simplificado: admin si `role_id=1`).

## Gestión de Documentación

- Cualquier módulo o funcionalidad nueva debe documentarse de forma técnica en la carpeta `docs/info/`.
- **REGLA OBLIGATORIA**: Cada vez que se modifique un módulo, su documentación correspondiente en `docs/info/` debe ser actualizada obligatoriamente para reflejar los cambios.
- Mantén el código limpio, comentado y auto-documentado con PHPDoc cuando sea necesario para `Scribe`.

### Referencias a la Documentación Técnica
- **[API.md](file:///Users/fryntiz/git/3-Raupulus/www.jaja.raupulus.dev/docs/info/API.md)**: Arquitectura general de la API (Sanctum, rate limits).
- **[Content.md](file:///Users/fryntiz/git/3-Raupulus/www.jaja.raupulus.dev/docs/info/Content.md)**: Módulo base de contenidos (chistes, adivinanzas, quiz).
- **[Suggestion.md](file:///Users/fryntiz/git/3-Raupulus/www.jaja.raupulus.dev/docs/info/Suggestion.md)**: Sistema de sugerencias y aportaciones comunitarias.
- **[Taxonomy.md](file:///Users/fryntiz/git/3-Raupulus/www.jaja.raupulus.dev/docs/info/Taxonomy.md)**: Clasificación de los contenidos (Type, Group, Category).
- **[User.md](file:///Users/fryntiz/git/3-Raupulus/www.jaja.raupulus.dev/docs/info/User.md)**: Usuarios, autenticación, roles y estadísticas de uploaders.
- **[Collaborator.md](file:///Users/fryntiz/git/3-Raupulus/www.jaja.raupulus.dev/docs/info/Collaborator.md)**: Directorio de colaboradores open source y sus proyectos.
- **[Report.md](file:///Users/fryntiz/git/3-Raupulus/www.jaja.raupulus.dev/docs/info/Report.md)**: Sistema de reportes de contenido.
- **[Page.md](file:///Users/fryntiz/git/3-Raupulus/www.jaja.raupulus.dev/docs/info/Page.md)**: Gestión CMS de páginas estáticas y legales.
- **[Commands.md](file:///Users/fryntiz/git/3-Raupulus/www.jaja.raupulus.dev/docs/info/Commands.md)**: Tareas asíncronas y cron (Sitemap, Stats, Redes Sociales).
- **[Actions.md](file:///Users/fryntiz/git/3-Raupulus/www.jaja.raupulus.dev/docs/info/Actions.md)**: Acciones reutilizables y clases de negocio.
