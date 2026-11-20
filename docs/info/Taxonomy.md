# Módulo de Taxonomía (Type, Group, Category)

## Qué hace y qué NO hace
- **Qué hace**: Organiza todo el contenido y sugerencias en tres ejes (`Type`, `Group`, `Category`), filtra taxonomías vacías en los listados públicos, optimiza consultas SEO y mantiene limpieza de imágenes e índices únicos parciales compatibles con `SoftDeletes`.
- **Qué NO hace**: No devuelve páginas `200` vacías en rutas aleatorias sin contenido ni mezcla contenidos adultos dentro de las taxonomías públicas generales.

## Modelo de datos
- **`App\Models\Type`** (tabla `types`, con `SoftDeletes`, `DeletesStoredImages`, `HasFactory`):
  - Campos: `name`, `slug`, `description`, `image`.
  - Relaciones: `hasMany(Group)`, `hasManyThrough(Content, Group)`.
- **`App\Models\Group`** (tabla `groups`, con `SoftDeletes`, `DeletesStoredImages`, `HasFactory`):
  - Campos: `type_id`, `title`, `slug`, `image`, `description`.
  - Relaciones: `belongsTo(Type)`, `hasMany(Content)`.
  - Scopes: `scopePublicSafe()`, `scopeHasAvailableContent()`.
- **`App\Models\Category`** (tabla `categories`, con `SoftDeletes`, `DeletesStoredImages`, `HasFactory`):
  - Campos: `title`, `slug`, `description`, `image`.
  - Relaciones: `belongsToMany(Content)`, `belongsToMany(Suggestion)`.
  - Scope: `scopeHasAvailableContent()`.
- **Índices únicos parciales**: `types_slug_unique_active`, `groups_slug_unique_active` y `categories_slug_unique_active` (`WHERE deleted_at IS NULL` en PostgreSQL y SQLite).

## Flujos principales
1. **Listados web (`ContentController`)**: cachea `types_index`, `groups_index`, `groups_index_by_type_{id}` y `categories_index` (60 minutos), calculando `contents_count` y `last_content_date` en subconsultas (`withCount`, `withMax`) sin `N+1` y excluyendo grupos/categorías sin contenido público seguro.
2. **Rutas aleatorias (`/grupo/{group:slug}/random` y `/categoria/{category:slug}/random`)**: obtienen hasta 3 elementos mediante `publicSafe()` y devuelven `404` intencionadamente si la colección está vacía.
3. **Diferenciación SEO por tipo (`/{type:slug}/grupos`)**: inyecta `$type` en la vista para emitir `<title>`, `<h1>`, `<meta name="description">` y JSON-LD (`CollectionPage` + `BreadcrumbList`) diferenciados respecto a `/grupos`.
4. **Cálculo legible de última actividad**: `Type::getLastUpdateMessageAttribute()` y `Group::getLastUpdateMessageAttribute()` usan `CarbonInterface::DIFF_ABSOLUTE`.

## Puntos de entrada
- **Web pública**: `/tipos`, `/grupos`, `/{type:slug}/grupos`, `/grupo/{group:slug}/random`, `/categorias`, `/categoria/{category:slug}/random`.
- **API v1 (`auth:sanctum`)**: `/api/v1/types`, `/api/v1/groups`, `/api/v1/categories`, más filtros `*/content/random` en `routes/api/v1.php`.
- **Filament (`/admin`)**: `Admin\TypeResource`, `Admin\GroupResource` (+ `ContentsRelationManager`), `Admin\CategoryResource`, protegidos por `AdminOnlyPolicy`.

## Dependencias
- **Depende de**: `Content::scopePublicSafe()`, `DeletesStoredImages`, `CacheInvalidationService`, `App\Filament\Support\WebpImageUpload`.
- **Usado por**: `Content`, `Suggestion`, `SitemapGeneratorCommand`, `StatsHelper`.

## Configuración
- `CONTENT_EXCLUDED_PUBLIC_GROUP_IDS` (`config/content.php`): excluye automáticamente grupos internos/sensibles (`[4, 14]` por defecto) en `Group::scopePublicSafe()` y `scopeHasAvailableContent()`.

## Trampas conocidas
- Las rutas aleatorias `/categoria/{slug}/random` y `/grupo/{slug}/random` devuelven `404` cuando no tienen contenido público apto (aunque tengan contenido marcado `is_adult = true`).
- La categoría ID `1` (`General`) es requerida por el `boot()` de `Content` y `Suggestion`.
- En tests bajo PostgreSQL con `RefreshDatabase`, las secuencias no retroceden con `ROLLBACK`; `tests/TestCase.php` adelanta `groups_id_seq` a `>= 100` para no colisionar accidentalmente con los grupos excluidos `[4, 14]`.

## Tests que lo cubren
- `tests/Feature/CategoryEmptyContentTest.php`
- `tests/Feature/DatabaseIndexesAndSoftDeletesTest.php`
- `tests/Feature/SeoMetadataAndSitemapTest.php`
- `tests/Feature/DatabasePerformanceAndAtomicityTest.php`
- `tests/Feature/ReliabilityAndArchitectureTest.php`
- `tests/Unit/ImageSanitizationAndCleanupTest.php`

## Pendiente real
- Sin deuda técnica pendiente del alcance actual.

---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
