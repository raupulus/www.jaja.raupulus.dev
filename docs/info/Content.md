# Módulo de Contenido (Content)

## Qué hace y qué NO hace
- **Qué hace**: Almacena, filtra, serializa y presenta los contenidos publicados (chistes, adivinanzas y preguntas quiz) con sus categorías, opciones de respuesta e imágenes optimizadas en WebP, tanto en la web pública como en la API REST y paneles Filament (`/admin` y `/panel`).
- **Qué NO hace**: No gestiona envíos públicos sin moderación ni votos directos de usuarios; las aportaciones sin revisar entran primero por el módulo `Suggestion`.

## Modelo de datos
- **`App\Models\Content`** (tabla `contents`, con `SoftDeletes`, `DeletesStoredImages`, `HasFactory`):
  - Campos clave: `user_id`, `group_id`, `title`, `content`, `image`, `uploaded_by`, `is_adult`, `is_ai`, `last_social_published`.
  - Relaciones reales: `belongsTo(User)`, `belongsTo(Group)` (el tipo `Type` se obtiene mediante `$content->group->type`), `belongsToMany(Category)` (`content_categories`), `hasMany(ContentOption)` y `morphMany(Report, 'reportable')`.
  - Scopes: `scopePublicSafe()`, `scopeRandom()`, `scopeByGroup()`, `scopeByType()`, `scopeByCategory()`, `scopeByGroupAndCategory()`, `scopeByTypeAndCategory()`, `scopeByGroupTypeAndCategory()`.
- **`App\Models\ContentOption`** (tabla `content_options`, con `SoftDeletes`, `DeletesStoredImages`, `HasFactory`):
  - Campos clave: `content_id`, `value`, `is_correct`, `image_path`, `order`.
  - Relación: `belongsTo(Content)`.
- **Índice adicional**: `contents_is_adult_group_id_index` y `content_options_content_id_order_index` + índices compuestos en `content_categories`.

## Flujos principales
1. **Listado y visualización pública (`IndexController`, `ContentController`, `V1Controller`)**: aplican `Content::scopePublicSafe()` y precargan `group.type`, `options` ordenadas por `order` y `user:id,nick` para cumplir `Model::preventLazyLoading(! app()->isProduction())` con cero consultas `N+1`.
2. **Creación y categoría por defecto**: en `Content::boot()`, al crear o actualizar un registro sin categorías se asocia automáticamente la categoría `id = 1` (`General`). En test/factory se garantiza mediante `CategoryFactory::ensureGeneralCategoryExists()`.
3. **Gestión en Filament (`Admin/ContentResource` y `Panel/ContentResource`)**: formularios organizados en secciones (`Contenido`, `Clasificación`, `Moderación`), validación anti-IDOR por `user_id` en `/panel`, gestión de opciones de quiz mediante `OptionsRelationManager` (con acceso tipado seguro `ownerContent()`) y limpieza física de imágenes en `Storage::disk('public')` vía `DeletesStoredImages`.
4. **Invalidación automática de caché**: `AppServiceProvider` delega en `App\Services\CacheInvalidationService` al guardar, borrar o restaurar contenidos, limpiando claves públicas y contadores.

## Puntos de entrada
- **Web pública**: `/` (`index`), vistas aleatorias y listados de grupo/categoría descritos en [Taxonomy.md](Taxonomy.md).
- **API v1**: endpoints públicos `/api/v1/random`, `/api/v1/random/{type:slug}` y endpoints privados de consulta descritos en [API.md](API.md).
- **Filament**: `/admin/contents` (requiere `role_id === 1`) y `/panel/contents` (filtrado y restringido al propietario `user_id === auth()->id()`).

## Dependencias
- **Depende de**: `Group`, `Type`, `Category`, `User`, `DeletesStoredImages`, `App\Services\CacheInvalidationService`, `App\Filament\Support\WebpImageUpload`, `App\Policies\ContentPolicy`, `App\Policies\ContentOptionPolicy`.
- **Usado por**: `Suggestion::approve()`, `Report` (`reportable`), `PublishToSocialHelper`, `SitemapGeneratorCommand`, `StatsHelper`.

## Configuración
- `CONTENT_EXCLUDED_PUBLIC_GROUP_IDS` (`config/content.php`): lista separada por comas de grupos excluidos de listados públicos y difusión social (por defecto `'4,14'`, respaldado por `Content::EXCLUDED_PUBLIC_GROUP_IDS`).

## Trampas conocidas
- El atributo `uploader` devuelve `@` + `$this->uploaded_by ?? $this->user->nick ?? 'SinAsignar'`.
- `ContentOptionResource` serializa `'urlImage'` comprobando `$this->image_path` (no `$this->image`).
- `Content::boot()` adjunta categoría ID `1` en `created`/`updated`; cualquier entorno o test que cree un `Content` sin categoría ID `1` en BD fallará por clave foránea si no la garantiza previamente.

## Tests que lo cubren
- `tests/Feature/DatabasePerformanceAndAtomicityTest.php`
- `tests/Feature/ReliabilityAndArchitectureTest.php`
- `tests/Feature/ApiV1ContractTest.php`
- `tests/Feature/FilamentPanelsUxTest.php`
- `tests/Feature/AccessControlAndSecurityTest.php`
- `tests/Feature/FactoriesAndCommandsTest.php`
- `tests/Unit/ImageSanitizationAndCleanupTest.php`

## Pendiente real
- Implementar paginación o vista dedicada si en el futuro se incorporan listados HTML extensos de contenidos individuales fuera de las rutas aleatorias actuales.

---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
