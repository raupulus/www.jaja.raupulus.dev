# Módulo de Usuarios (User)

## Descripción General
El módulo `User` gestiona la autenticación, roles básicos y perfiles públicos (uploaders) dentro de la aplicación. Es la base para acceder a los paneles privados (tanto admin como colaboradores/usuarios) y provee autenticación para los endpoints protegidos de la API.

## Componentes y Modelos
- **User**: Representa a un individuo registrado en el sistema.

## Funcionalidad Técnica
1. **Autenticación y Validación en Panel**:
   - Gestionada a través de Laravel Sanctum para la emisión y verificación de tokens en la API.
   - Existen dos puntos de entrada web: `/admin/login` para administradores y `/panel/login` (o `/login`) para usuarios generales.
   - En `Admin/UserResource`, el campo `email` valida unicidad ignorando el registro actual (`unique(ignoreRecord: true)`) y el avatar admite `JPEG`, `PNG` o `WebP` hasta `2048 KB`.
   - `User` utiliza `DeletesStoredImages` para eliminar automáticamente avatares anteriores o al ejecutar `forceDelete()`, preservando `images/default/avatar.webp`.
2. **Sistema de Roles y Control de Acceso**:
   - El modelo carece de un paquete complejo de roles y permisos (como Spatie) por simplicidad de negocio.
   - Se gestiona mediante el campo `role_id` (excluido explícitamente de `$fillable` en `User`). Por convención, el `role_id = 1` otorga privilegios totales y acceso al panel de administración `/admin`, mientras `role_id = 2` restringe el acceso a `/panel`. Desde `Admin/UserResource` (`/admin/users`), un administrador puede consultar, filtrar y asignar `role_id` mediante `CreateUser`/`EditUser`, con protección que impide degradar al último administrador activo.
   - `User::canAccessPanel(Panel $panel)` exige `$this->isAdmin` para el panel `admin` y permite usuarios autenticados en `panel`.
   - `AdminMiddleware` e `IsAdminMiddleware` operan con política *fail-closed* (`abort(403)` para cualquier usuario no administrador o no autenticado).
   - Todos los modelos con recurso en Filament disponen de su correspondiente Policy en `app/Policies/` (`AdminOnlyPolicy` y políticas de propiedad por `user_id`).
3. **Perfiles y Estadísticas (Uploaders)**:
   - Los usuarios que aportan contenido se vinculan mediante el campo "nick" de las sugerencias.
   - Tienen estadísticas asociadas, las cuales son calculadas periódicamente por comandos en el cron. Estos cálculos excluyen el contenido eliminado y el asociado al modelo `Report`.
   - Aparecen listados dinámicamente en la página de agradecimientos si han aportado gran cantidad de contenido.
   - `UserContributorsChart` utiliza una paleta fija accesible (`PALETTE`) con `$columnSpan = 'full'` y `$sort = 5`, y `UserOverview` etiqueta en español (`Aportantes`) con ancho completo y `$sort = 4`.
4. **Relaciones**:
   - Tienen relación con las tablas de tokens de acceso personal de Sanctum.
   - Poseen relación implícita con `Content` para calcular las estadísticas y aportes.


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
