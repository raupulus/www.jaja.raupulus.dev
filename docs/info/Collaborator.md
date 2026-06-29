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
2. **Visibilidad Frontend**:
   - Se generan páginas dinámicas para los colaboradores con el listado de todos sus proyectos activos.
   - Cada proyecto posee su propia página individual, lo cual fomenta un mejor SEO al generar más contenido indexado internamente.
3. **Relaciones**:
   - `Collaborator` `hasMany` `CollaboratorProject`.
   - `CollaboratorProject` `belongsTo` `Collaborator`.
