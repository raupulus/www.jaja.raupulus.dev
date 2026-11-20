# Módulo de Páginas Estáticas (Page)

## Qué hace y qué NO hace
- **Qué hace**: Actúa como mini-CMS integrado para publicar páginas legales e informativas (Normas de Uso, Privacidad, Cookies, Guía de la API) renderizando Markdown seguro, metadatos SEO, JSON-LD y enlaces públicos en `sitemap.xml`.
- **Qué NO hace**: No expone borradores (`status = 'draft'`) en la web pública ni en el sitemap, ni permite JavaScript sin sanear dentro del Markdown (`Parsedown::setSafeMode(true)`).

## Modelo de datos
- **`App\Models\Page`** (tabla `pages`, con `SoftDeletes`, `DeletesStoredImages`, `HasFactory`):
  - Campos clave: `title`, `slug`, `excerpt`, `content`, `image`, `keywords`, `status` (`draft`, `published`).
  - Accessors y helpers: `url` (`route('page.show', $this->slug)`), `urlImage`, `urlImageThumbnail`, `getHtmlContent()` (`\Parsedown` en modo seguro).
  - Índice único parcial: `pages_slug_unique_active` (`WHERE deleted_at IS NULL`).

## Flujos principales
1. **Índice y visualización pública (`PageController`)**:
   - `/paginas` lista páginas publicadas omitiendo el contenedor de imagen cuando una página no dispone de imagen y emitiendo JSON-LD `CollectionPage` + `BreadcrumbList` con jerarquía `h1 → h2` y textos descriptivos (`Leer {título}`).
   - `/pagina/{page:slug}` muestra el contenido Markdown convertido a HTML seguro (`og_type="article"`, `description` recortada a 155 caracteres y JSON-LD `WebPage` + `BreadcrumbList`). Si el registro está en `draft`, responde `404`.
2. **Gestión en Filament (`Admin\PageResource`)**:
   - Formulario responsive (`columns(['default' => 1, 'lg' => 3])`) con `MarkdownEditor`, `WebpImageUpload` (2048 KB, `JPEG`/`PNG`/`WebP`), validación de unicidad por `slug` ignorando `deleted_at IS NOT NULL`, filtro por `status`, `ActionGroup` en tabla e invalidación automática en `CacheInvalidationService`.

## Puntos de entrada
- **Web pública**: `/paginas`, `/pagina/{page:slug}`.
- **Filament (`/admin`)**: `Admin\PageResource` protegido por `PagePolicy` / `AdminOnlyPolicy`.

## Dependencias
- **Depende de**: `DeletesStoredImages`, `WebpImageUpload`, `CacheInvalidationService`, `erusev/parsedown`.
- **Usado por**: `SitemapGeneratorCommand` (`addPagesToSitemap()`), navegación principal y documentación `/pagina/api` enlazada con Scribe.

## Configuración
- Sin variables de entorno propias; hereda configuración general de caché (`CacheInvalidationService`) y disco público (`Storage::disk('public')`).

## Trampas conocidas
- En `/pagina/api`, las anclas rápidas en `resources/views/pages/show.blade.php` dependen de los IDs generados por Scribe (`#autenticacion`, `#categorias-grupos-y-tipos`, `#contenidos`, `#sugerencias`, `#reportes`).
- Al usar `Page::keywords` en `@section('keywords', ...)`, debe forzarse a cadena `(string) ($page->keywords ?? '')` cuando `keywords` es `null`.

## Tests que lo cubren
- `tests/Feature/ApiV1ContractTest.php`
- `tests/Feature/DesignSystemAndPerformanceTest.php`
- `tests/Feature/SeoMetadataAndSitemapTest.php`
- `tests/Feature/ResponsiveUxAndW3cTest.php`
- `tests/Feature/DatabaseIndexesAndSoftDeletesTest.php`
- `tests/Feature/FilamentPanelsUxTest.php`
- `tests/Feature/FactoriesAndCommandsTest.php`

## Pendiente real
- Implementar generación de miniaturas diferenciadas en `getUrlImageThumbnailAttribute()` cuando el diseño lo necesite (actualmente devuelve `urlImage`).

---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
