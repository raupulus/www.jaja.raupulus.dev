# Arquitectura y Diseño de la API

## Qué hace y qué NO hace
- **Qué hace**: Expone los 15 endpoints REST v1 en JSON (`/api/v1/*`) para consumir contenidos aleatorios o paginados, consultar taxonomías (`Type`, `Group`, `Category`), autenticar bots/clientes con Sanctum y recibir sugerencias o reportes comunitarios. Genera documentación estática OpenAPI/Swagger mediante Scribe en `public/docs/`.
- **Qué NO hace**: No expone endpoints de borrado o administración destructiva vía API ni almacena tokens activos en texto plano fuera de `personal_access_tokens`. Tampoco publica directamente en redes sociales desde endpoints HTTP públicos (usa `PublishToSocialHelper`).

## Modelo de datos
- Emplea la tabla `personal_access_tokens` (`Laravel\Sanctum\PersonalAccessToken`) vinculada a `App\Models\User`.
- Serializa respuestas mediante `App\Http\Responses\ApiResponse`, `App\Http\Traits\ApiResponseTrait` y `API Resources`:
  - `ContentResource` y `ContentListResource` (`App\Models\Content`)
  - `ContentOptionResource` (`App\Models\ContentOption`, resuelve `urlImage` sobre `$this->image_path`)
  - `CategoryResource`, `GroupResource`, `TypeResource`, `UserResource`

## Flujos principales
1. **Autenticación (`AuthController`)**: `POST /api/v1/auth/login` emite un token Sanctum verificando credenciales válidas; `GET /api/v1/auth/user` y `POST /api/v1/auth/logout` consultan o revocan el token actual bajo `auth:sanctum`.
2. **Consulta pública (`/api/v1/random`, `/api/v1/random/{type:slug}`)**: selecciona aleatoriamente contenidos aplicando `Content::scopePublicSafe()` (excluyendo adultos y grupos internos `[4, 14]`), precarga `options` ordenadas por `order` y `user:id,nick` sin `N+1`.
3. **Consultas privadas (`/api/v1/types`, `/api/v1/groups`, `/api/v1/categories`, `/api/v1/contents/list`, endpoints `*/content/random`)**: validan paginación (`page`, `limit`, `after_id`) y arrays `exclude_groups` (máximo 50 IDs) mediante `FormRequest` (`PaginationRequest`, `ContentListRequest`, `ContentRandomRequest`, `ContentRandomFromUserRequest`), respondiendo `422` ante tipos inválidos.
4. **Ingesta (`sendSuggestion`, `sendReport`)**: `SendSuggestionRequest` valida contenido, tipo (`chistes` por defecto, devolviendo `422` si faltara en BD) y nick opcional; `SendReportRequest` normaliza `description` vacía/nula a `''`. Los errores inesperados se registran con `Log::error` en inglés.
5. **Documentación Scribe (`config/scribe.php`)**: compilada offline mediante `php artisan scribe:generate` a `public/docs/` (`index.html`, `openapi.yaml`, `collection.json`) usando anotaciones `@response` y parámetros de `FormRequest`.

## Puntos de entrada
- Prefijo `/api/v1` definido en `routes/api/v1.php`:
  - **Públicos**: `POST /api/v1/auth/login` (`throttle.real.ip:3,1`), `GET /api/v1/random` y `GET /api/v1/random/{type:slug}` (`throttle.real.ip:10,1`).
  - **Privados (`auth:sanctum`)**:
    - `POST /api/v1/auth/logout` (`throttle.real.ip:3,1`), `GET /api/v1/auth/user` (`throttle.real.ip:5,1`).
    - `POST /api/v1/suggestion/send` (`throttle.real.ip:10,1`), `POST /api/v1/report/send` (`throttle.real.ip:10,1`).
    - `GET /api/v1/types`, `/api/v1/groups`, `/api/v1/categories` (`throttle.real.ip:20,1`).
    - `GET /api/v1/type/{type:slug}/content/random`, `/api/v1/type/{type:slug}/category/{categorySlug}/content/random`, `/api/v1/group/{group:slug}/content/random`, `/api/v1/user/{nick}/content/random`, `/api/v1/contents/list` (`throttle.real.ip:50,1`).

## Dependencias
- **Depende de**: `App\Http\Middleware\ThrottleWithRealIp`, `App\Helpers\IpHelper`, `App\Http\Middleware\SecurityHeadersMiddleware`, modelos `Content`, `Suggestion`, `Report`, `Type`, `Group`, `Category`, `User`, y `knuckleswtf/scribe`.
- **Usado por**: Bots comunitarios, integraciones externas y la página pública `/pagina/api` (`resources/views/pages/show.blade.php` enlaza a anclas de Scribe `#autenticacion`, `#categorias-grupos-y-tipos`, `#contenidos`, `#sugerencias`, `#reportes`).

## Configuración
- `SANCTUM_TOKEN_EXPIRATION` (`config/sanctum.php`): `null` por defecto para compatibilidad retroactiva con tokens existentes; admite minutos enteros si se desea caducidad.
- `CONTENT_EXCLUDED_PUBLIC_GROUP_IDS` (`config/content.php`): IDs excluidos por defecto (`4,14`).
- `SOCIAL_PUBLISHER_URL`, `SOCIAL_PUBLISHER_CONNECT_TIMEOUT`, `SOCIAL_PUBLISHER_TIMEOUT` (`config/services.php`): por defecto `http://localhost:8080/publish`, `5s` y `15s`.
- `APP_DEBUG`: si es `true`, `ApiResponse::error()` expone `debug.request` filtrando campos sensibles (`password`, `password_confirmation`, `current_password`, `token`, `access_token`).

## Trampas conocidas
- Todas las rutas `api/*` fuerzan respuesta JSON incluso sin cabecera `Accept: application/json` (`bootstrap/app.php`).
- `/api/v1/groups` pagina con el parámetro estándar `page` (no cambiar por otro nombre ni exigir casting implícito `(int)` en validaciones).
- Tras cualquier cambio en `routes/api/v1.php`, `V1Controller`, `AuthController`, `FormRequest` o `JsonResource` de API debe ejecutarse `php artisan scribe:generate`.

## Tests que lo cubren
- `tests/Feature/ApiV1ContractTest.php`
- `tests/Feature/ApiAndValidationStabilityTest.php`
- `tests/Feature/DatabasePerformanceAndAtomicityTest.php`
- `tests/Feature/SecurityAndRoutingTest.php`
- `tests/Feature/FactoriesAndCommandsTest.php`

## Pendiente real
- Sin deuda técnica pendiente del alcance actual; valorar expiración/rotación de tokens únicamente si se planifica transición controlada con bots existentes.

---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
