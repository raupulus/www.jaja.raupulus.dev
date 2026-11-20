# Arquitectura y Diseño de la API

## Descripción General
La API de JaJa Project es el principal medio por el cual clientes externos (Bots de Discord, aplicaciones SPA, PWAs, etc.) consumen la base de datos comunitaria de chistes, preguntas y adivinanzas.

## Componentes Técnicos y Estándares
1. **Laravel Sanctum (Compatibilidad y Política de Tokens)**:
   - Empleado para la autenticación basada en tokens Bearer (`Authorization: Bearer {token}`).
   - Endpoints públicos (`/api/v1/random`, `/api/v1/random/{type_slug}`) y autenticación (`POST /api/v1/auth/login`) operan sin token previo; los restantes 12 endpoints privados de consulta e ingesta requieren middleware `auth:sanctum`.
   - `config/sanctum.php` lee `SANCTUM_TOKEN_EXPIRATION` desde el entorno, manteniendo valor `null` por defecto para **garantizar 100% de retrocompatibilidad con bots comunitarios existentes** mientras permite activar expiración en minutos por entorno si se desea política de rotación periódica (los tokens pueden revocarse/regenerarse manualmente desde `/panel` o mediante `POST /api/v1/auth/logout`).
2. **Scribe OpenAPI y Especificación de los 15 Endpoints**:
   - Se utiliza Scribe (`config/scribe.php`) para generar la documentación estática compilada en `public/docs/` (`index.html`, `openapi.yaml` con los 15 paths reales de `routes/api/v1.php` y `collection.json`), evitando consultas reales durante la generación (`ResponseCalls` con `only: []` sobre anotaciones `@response` y `FormRequest::queryParameters()` / `bodyParameters()`).
   - Las anclas de navegación rápida en `/pagina/api` (`resources/views/pages/show.blade.php`) enlazan directamente a los bloques generados (`autenticacion`, `categorias-grupos-y-tipos`, `contenidos`, `sugerencias`, `reportes`).
   - **Comando clave**: `php artisan scribe:generate` tras cada modificación de rutas, requests o recursos API.
3. **Publicación Social Desacoplada (`PublishToSocialHelper`)**:
   - La URL destino y los timeouts se configuran en `config/services.php` bajo `services.social_publisher` (`SOCIAL_PUBLISHER_URL` con valor retrocompatible `http://localhost:8080/publish`, `connectTimeout` de 5s y `timeout` de 15s por defecto), registrando fallos de red o HTTP en inglés vía `Log::error`.
4. **Límites de Peticiones (Rate Limiting)**:
   - Todos los endpoints públicos y privados están sujetos a `throttle.real.ip` (`ThrottleWithRealIp`) con exclusión configurable por IP.

## Principales Funcionalidades
1. **Endpoint Público `Random`**:
   - Devuelve un contenido aleatorio sin necesidad de autenticación.
   - Filtra internamente para excluir grupos como "Adultos" con el fin de proteger las integraciones públicas por defecto.
2. **Endpoints de Ingesta**:
   - Existen endpoints para el envío de sugerencias comunitarias (ej. subir nuevos chistes) y para el reporte de contenido inapropiado.
3. **Formatos, Errores, Validación y Seguridad HTTP**:
   - Toda la comunicación se debe realizar forzando los headers `Accept: application/json` y `Content-type: application/json`. Aun sin `Accept: application/json`, cualquier ruta bajo `api/*` fuerza respuestas JSON en el manejador de excepciones de `bootstrap/app.php`.
   - En respuestas `ApiResponse::error()` con `APP_DEBUG=true`, el bloque `debug.request` excluye automáticamente campos sensibles (`password`, `password_confirmation`, `current_password`, `token`, `access_token`).
   - Las respuestas de la API y del sitio web incluyen cabeceras de seguridad estándar mediante `SecurityHeadersMiddleware` (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy` y `Strict-Transport-Security` en HTTPS).
   - Los `FormRequest` de consulta (`PaginationRequest`, `ContentListRequest`, `ContentRandom*Request`) validan los parámetros (`page`, `limit`, `after_id`) sin conversión `(int)` previa, devolviendo `422` ante tipos inválidos; `ContentListRequest` limita `exclude_groups` a 50 elementos y `/api/v1/groups` pagina con el parámetro estándar `page`.
   - Las respuestas con colecciones múltiples o vacías siempre deben adaptar sus envoltorios JSON, devolviendo plural o singular según el caso.


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
