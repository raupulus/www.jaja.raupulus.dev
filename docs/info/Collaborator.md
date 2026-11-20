# Módulo de Colaboradores (Collaborator)

## Descripción General
Este módulo gestiona el directorio de colaboradores de código o software abierto que contribuyen a la plataforma. Les permite tener presencia pública en la web, mostrar los proyectos en los que trabajan y visibilizar su labor técnica y comunitaria.

## Componentes y Modelos
1. **Collaborator**: Representa a un colaborador de software/comunidad. Posee datos del perfil público (redes, alias, biografía).
2. **CollaboratorProject**: Representa los proyectos de software que han sido desarrollados o aportados por un colaborador en específico.

## Funcionalidad Técnica
1. **Gestión desde Panel / Intranet**:
   - Los colaboradores registrados pueden acceder a un panel privado para editar sus propios proyectos o añadir nuevos.
   - Todos los proyectos nuevos o editados por colaboradores entran en un estado de borrador ("draft") por seguridad, hasta que un administrador revise y apruebe la visibilidad.
   - El formulario de `Panel/CollaboratorResource` omite `user_id` del esquema del formulario y `EditCollaborator` descarta cualquier posible envío de `user_id` en `mutateFormDataBeforeSave()`, protegiendo frente a asignación indebida de propiedad.
   - En `Admin/CollaboratorResource`, `$recordTitleAttribute` apunta a `name` para habilitar la búsqueda global en `/admin`.
   - Los enlaces externos (`website`, `url_repositories` y `url` de proyecto) se validan exclusivamente con esquema `http`/`https` (`url:http,https`) y las subidas de imagen en Filament aceptan `JPEG`, `PNG` o `WebP` con un máximo de `2048 KB`.
   - Al reemplazar o eliminar un perfil de colaborador o cualquiera de sus proyectos, el trait `DeletesStoredImages` y el hook `deleting` de `Collaborator` borran físicamente las imágenes asociadas en `Storage::disk('public')`.
2. **Visibilidad Frontend y Anti-IDOR**:
   - Se generan páginas dinámicas para los colaboradores con el listado de todos sus proyectos activos.
   - En `CollaboratorController::showProject()`, además de resolver las rutas enlazadas, se verifica que el proyecto pertenezca al colaborador de la URL y tenga `status === 'published'` (devolviendo `404` en caso contrario).
   - Cada proyecto posee su propia página individual, lo cual fomenta un mejor SEO al generar más contenido indexado internamente.
   - Los listados, perfiles y fichas de proyecto emiten datos estructurados JSON-LD (`CollectionPage`, `ProfilePage`/`Person`, `SoftwareApplication` y `BreadcrumbList`).
3. **Relaciones**:
   - `Collaborator` `hasMany` `CollaboratorProject`.
   - `CollaboratorProject` `belongsTo` `Collaborator`.


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
