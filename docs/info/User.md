# Módulo de Usuarios (User)

## Qué hace y qué NO hace
- **Qué hace**: Gestiona usuarios registrados, autenticación web (paneles Filament `/admin` y `/panel`), emisión/revocación de tokens API con Sanctum, control de roles basado en base de datos y cálculo de estadísticas de aportantes.
- **Qué NO hace**: No delega permisos mediante paquetes externos tipo Spatie ni expone `role_id` como campo asignable masivamente en `$fillable`.

## Modelo de datos
- **`App\Models\User`** (tabla `users`, con `SoftDeletes`, `DeletesStoredImages`, `HasApiTokens`, `HasFactory`, `Notifiable`, e implementa `FilamentUser` y `HasAvatar`):
  - Campos clave: `name`, `nick`, `email`, `password` (cast `hashed`), `avatar`, `role_id` (`1` = administrador, `2` = usuario estándar, fuera de `$fillable`).
  - Relación: `hasOne(Collaborator)`.
  - Accessors: `urlImage` (`images/default/avatar.webp` por defecto) e `isAdmin` (`$this->role_id === 1`).
  - Índice único parcial: `users_email_unique_active` (`WHERE deleted_at IS NULL`).

## Flujos principales
1. **Acceso a paneles (`User::canAccessPanel`)**: `/admin` exige `$this->isAdmin` (`role_id === 1`), reforzado además por `AdminMiddleware` / `IsAdminMiddleware` en modo *fail-closed* (`403` para no administradores). `/panel` permite usuarios autenticados con aislamiento por propiedad.
2. **Gestión de roles en `/admin/users` (`CreateUser`, `EditUser`)**: solo administradores autenticados pueden establecer `role_id` (`1` o `2`), y `EditUser::handleRecordUpdate()` bloquea degradar al último administrador activo en base de datos.
3. **Autenticación API (`AuthController`)**: login con emisión de token Sanctum (`POST /api/v1/auth/login`), perfil del usuario autenticado (`GET /api/v1/auth/user`) y cierre de sesión revocando únicamente el token vigente (`POST /api/v1/auth/logout`).
4. **Estadísticas de aportantes (`StatsHelper`)**: combina usuarios registrados (`user_id` cuando `uploaded_by` es `null`) y colaboradores anónimos (`uploaded_by`) sobre contenidos activos (`whereNull('deleted_at')`).

## Puntos de entrada
- **Web**: `/login` (redirige al login del panel), `/admin/login`, `/panel/login`.
- **Filament (`/admin/users`)**: `Admin\UserResource` + widgets `UserOverview` (`Aportantes`) y `UserContributorsChart`.
- **API v1**: `/api/v1/auth/login`, `/api/v1/auth/logout`, `/api/v1/auth/user`, `/api/v1/user/{nick}/content/random`.

## Dependencias
- **Depende de**: Laravel Sanctum, Filament 3, `DeletesStoredImages`, `AdminOnlyPolicy` / `UserPolicy` (vía Filament).
- **Usado por**: `Content`, `Collaborator`, `Report`, `Api\AuthController`, `StatsHelper`.

## Configuración
- `SANCTUM_TOKEN_EXPIRATION` (`config/sanctum.php`): opcional en minutos (`null` por defecto para compatibilidad con integraciones en producción).
- Sesiones web con cookies seguras en producción (`APP_ENV=production` / `.env.example.production`).

## Trampas conocidas
- `role_id` no está en `User::$fillable` para evitar BOPLA (*Mass Assignment*); `CreateUser` y `EditUser` lo asignan explícitamente tras validar al administrador en sesión.
- `UserFactory` fija por defecto `role_id => 2` y un `nick` válido; usar `User::factory()->admin()` en tests cuando se necesite rol administrador.
- En consultas estadísticas (`StatsHelper`), los conteos filtran contenidos activos (`whereNull('deleted_at')`) y formatos `['chistes', 'quiz', 'adivinanzas']` (no excluyen por tabla `reports`).

## Tests que lo cubren
- `tests/Feature/AccessControlAndSecurityTest.php`
- `tests/Feature/FilamentPanelsUxTest.php`
- `tests/Feature/DatabaseIndexesAndSoftDeletesTest.php`
- `tests/Feature/ApiV1ContractTest.php`
- `tests/Feature/FactoriesAndCommandsTest.php`
- `tests/Feature/SecurityAndRoutingTest.php`

## Pendiente real
- Sin deuda técnica pendiente del alcance actual.

---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
