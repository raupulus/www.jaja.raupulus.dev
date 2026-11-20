# Módulo de Acciones, Helpers y Servicios de Soporte

## Qué hace y qué NO hace
- **Qué hace**: Centraliza operaciones transversales reutilizables: optimización y sanitización de imágenes (`ConvertImageToWebp`), estandarización de subidas en Filament (`WebpImageUpload`), limpieza de archivos (`DeletesStoredImages`), resolución segura de IP (`IpHelper`), publicación social (`PublishToSocialHelper`), invalidación de cachés (`CacheInvalidationService`) y cálculo de estadísticas (`StatsHelper`).
- **Qué NO hace**: No duplica lógica de negocio en controladores o formularios individuales ni confía automáticamente en cabeceras `X-Forwarded-For` / `CF-Connecting-IP` sin validar proxies de confianza.

## Modelo de datos
- No define tablas propias; opera sobre `Storage::disk('public')`, caché Laravel y modelos Eloquent (`Content`, `Suggestion`, `Category`, `Type`, `Group`, `User`, `Collaborator`, `CollaboratorProject`, `Page`).

## Flujos principales
1. **Sanitización WebP (`App\Actions\ConvertImageToWebp`)**:
   - Valida entrada de archivo subido o ruta temporal, aplica autoorientación EXIF (`orient()`), escala proporcionalmente hasta `2560×2560px` (`scaleDown()`), elimina metadatos EXIF/IPTC mediante `WebpEncoder(quality: 85, strip: true)` y almacena el resultado en `Storage::disk('public')`.
2. **Subidas Filament unificadas (`App\Filament\Support\WebpImageUpload`)**:
   - Unifica los 13 `FileUpload` de `/admin` y `/panel` aplicando disco `public`, límite `2048 KB`, MIME (`image/jpeg`, `image/png`, `image/webp`), editor de imagen, conversión WebP y registro de errores vía `Log::error` en inglés.
3. **Limpieza de ficheros (`App\Models\Concerns\DeletesStoredImages`)**:
   - En `updating`, elimina la imagen sustituida si cambió; en `deleting` (para modelos sin `SoftDeletes`) o evento `forceDeleting` registrado con `static::registerModelEvent('forceDeleting', ...)` (para modelos con `SoftDeletes`), elimina la imagen física conservando siempre las rutas por defecto (`images/default/*.webp`).
4. **Resolución de IP real (`App\Helpers\IpHelper`)**:
   - Consulta `CF-Connecting-IP` únicamente cuando `$request->isFromTrustedProxy()` resulta verdadero según `config/trustedproxy.php` (`TRUSTED_PROXIES`); en caso contrario devuelve `$request->ip()`.
5. **Publicación social (`App\Helpers\PublishToSocialHelper`)**:
   - Rechaza cualquier contenido con `is_adult = true`, arma tags excluyendo `'general'`, envía `POST` JSON con `connectTimeout` y `timeout` configurados, exige `success: true` en respuesta y registra fallos HTTP/conexión en inglés vía `Log::error`.
6. **Invalidación de caché (`App\Services\CacheInvalidationService`)**:
   - Registrada en `AppServiceProvider::boot()` sobre eventos de modelo para limpiar cachés públicas, de navegación y contadores de `StatsHelper`.

## Puntos de entrada
- Invocados internamente desde `IndexController`, formularios Filament (`Admin/*` y `Panel/*`), middleware `ThrottleWithRealIp`, `Api\V1Controller`, `PublishContentToSocialCommand` y `StatsCommand`.

## Dependencias
- **Depende de**: `intervention/image`, `Illuminate\Support\Facades\Http`, `Illuminate\Support\Facades\Storage`, `Illuminate\Support\Facades\Cache`, `config/services.php`, `config/trustedproxy.php`.
- **Usado por**: Todos los módulos con subida de imagen, control de tasa, publicación externa o cachés públicas.

## Configuración
- `TRUSTED_PROXIES` (`config/trustedproxy.php`): lista separada por comas de proxies confiables o `null` por defecto.
- `SOCIAL_PUBLISHER_URL`, `SOCIAL_PUBLISHER_CONNECT_TIMEOUT`, `SOCIAL_PUBLISHER_TIMEOUT` (`config/services.php`).

## Trampas conocidas
- `DeletesStoredImages` usa `static::registerModelEvent('forceDeleting', ...)` en lugar de `static::forceDeleting(...)` para mantener compatibilidad estática en PHPStan con modelos sin `SoftDeletes`.
- `PublishToSocialHelper` rechaza `is_adult = true` y apunta por defecto a `http://localhost:8080/publish`.
- Todos los mensajes de `Log::*` dentro de `app/` deben mantenerse en inglés (comprobado por test).

## Tests que lo cubren
- `tests/Unit/ImageSanitizationAndCleanupTest.php`
- `tests/Feature/ReliabilityAndArchitectureTest.php`
- `tests/Feature/ApiV1ContractTest.php`
- `tests/Feature/DatabasePerformanceAndAtomicityTest.php`
- `tests/Feature/FactoriesAndCommandsTest.php`

## Pendiente real
- Sin deuda técnica pendiente del alcance actual.

---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
