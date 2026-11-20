# Módulo de Colaboradores (Collaborator)

## Qué hace y qué NO hace
- **Qué hace**: Gestiona perfiles de colaboradores de software open source y sus proyectos vinculados, permitiéndoles mantenerlos desde `/panel` con moderación en `/admin` y exposición SEO/JSON-LD pública cuando están publicados.
- **Qué NO hace**: No permite que un usuario publique directamente un proyecto sin aprobación de administrador ni que edite perfiles o proyectos de otro usuario.

## Modelo de datos
- **`App\Models\Collaborator`** (tabla `collaborators`, con `DeletesStoredImages`, `HasFactory`):
  - Campos clave: `user_id`, `name`, `nick`, `website`, `url_repositories`, `image`, `description`.
  - Relaciones: `belongsTo(User)`, `hasMany(CollaboratorProject)`.
- **`App\Models\CollaboratorProject`** (tabla `collaborator_projects`, con `DeletesStoredImages`, `HasFactory`):
  - Campos clave: `collaborator_id`, `title`, `slug`, `url`, `image`, `excerpt`, `content`, `type` (`web`, `mobile`, `desktop`, `bot`, `marketing`, `other`), `repository_type` (`github`, `gitlab`, `bitbucket`, `other`), `keywords`, `status` (`draft`, `published`).
  - Relación: `belongsTo(Collaborator)`.
  - Contenido renderizado con `\Parsedown` en modo seguro (`setSafeMode(true)`).

## Flujos principales
1. **Directorio y fichas públicas (`CollaboratorController`)**:
   - `/colaboradores` lista únicamente colaboradores con al menos un proyecto publicado (`getCollaboratorsVerified()`), cacheado (`page_collaborators_index`).
   - `/colaborador/{collaborator:nick}` muestra perfil y proyectos publicados (`404` si no tiene proyectos publicados).
   - `/colaborador/{collaborator:nick}/project/{project:slug}` exige que `$project->collaborator_id === $collaborator->id` y `$project->status === 'published'`, devolviendo `404` si no se cumplen ambas condiciones.
   - Las vistas emiten JSON-LD (`CollectionPage`, `ProfilePage`/`Person`, `SoftwareApplication`, `BreadcrumbList`).
2. **Autogestión en `/panel` con protección anti-IDOR y anti-BOPLA**:
   - `Panel\CollaboratorResource` y `EditCollaborator` verifican `$record->user_id === auth()->id()` en `authorizeAccess()`, políticas (`CollaboratorPolicy`, `CollaboratorProjectPolicy`) y eliminan `user_id` en `mutateFormDataBeforeSave()`.
   - Los enlaces externos (`website`, `url_repositories`, `url`) validan esquema estricto `url:http,https`.
3. **Limpieza automática de archivos**: `DeletesStoredImages` y el hook `deleting` en `Collaborator::booted()` borran en `Storage::disk('public')` tanto la imagen del colaborador como las imágenes de sus proyectos al eliminar o reemplazar.

## Puntos de entrada
- **Web pública**: `/colaboradores`, `/colaborador/{collaborator:nick}`, `/colaborador/{collaborator:nick}/project/{project:slug}`.
- **Filament**: `/admin/collaborators` (+ `ProjectsRelationManager`, búsqueda global por `$recordTitleAttribute = 'name'`) y `/panel/collaborators` (+ `ProjectsRelationManager`).

## Dependencias
- **Depende de**: `User`, `DeletesStoredImages`, `WebpImageUpload`, `CollaboratorPolicy`, `CollaboratorProjectPolicy`, `CacheInvalidationService`, `erusev/parsedown`.
- **Usado por**: `SitemapGeneratorCommand` (`addCollaboratorsToSitemap()`).

## Configuración
- Subida de imágenes estandarizada mediante `WebpImageUpload` (2048 KB, `JPEG`/`PNG`/`WebP`, conversión a `.webp`).

## Trampas conocidas
- El segmento de ruta del detalle de proyecto utiliza `/project/` en inglés (`/colaborador/{nick}/project/{slug}`), no `/proyecto/`.
- Un colaborador sin al menos un proyecto con `status = 'published'` devuelve `404` en `/colaborador/{nick}` y no aparece en `/colaboradores` ni en `sitemap.xml`.
- En `Panel\CollaboratorResource\RelationManagers\ProjectsRelationManager`, los `match ($state)` de badges cubren explícitamente `default => 'gray'`.

## Tests que lo cubren
- `tests/Feature/AccessControlAndSecurityTest.php`
- `tests/Feature/FilamentPanelsUxTest.php`
- `tests/Feature/SeoMetadataAndSitemapTest.php`
- `tests/Feature/ResponsiveUxAndW3cTest.php`
- `tests/Feature/FactoriesAndCommandsTest.php`
- `tests/Unit/ImageSanitizationAndCleanupTest.php`

## Pendiente real
- Sin deuda técnica pendiente del alcance actual.

---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
