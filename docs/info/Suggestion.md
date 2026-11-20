# Módulo de Sugerencias (Suggestion)

## Qué hace y qué NO hace
- **Qué hace**: Recibe propuestas comunitarias de chistes, adivinanzas o quiz desde la web pública y la API REST, valida sus reglas de negocio e imágenes, las modera en `/admin` y las promueve atómicamente a `Content` publicado.
- **Qué NO hace**: No publica automáticamente ninguna sugerencia sin revisión previa en `/admin`, ni expone el listado completo de sugerencias pendientes al público.

## Modelo de datos
- **`App\Models\Suggestion`** (tabla `suggestions`, con `SoftDeletes`, `DeletesStoredImages`, `HasFactory`):
  - Campos clave: `type_id`, `group_id`, `nick`, `title`, `content`, `image_path`, `ip_address`, `user_agent`, `is_adult`, `is_ai`, `approved_at`.
  - Relaciones: `belongsTo(Type)`, `belongsTo(Group)`, `belongsToMany(Category)` (`suggestion_categories`), `hasMany(SuggestionOption)`.
- **`App\Models\SuggestionOption`** (tabla `suggestion_options`, con `SoftDeletes`, `DeletesStoredImages`, `HasFactory`):
  - Campos clave: `suggestion_id`, `value`, `is_correct`, `image_path`, `order`.
  - Relación: `belongsTo(Suggestion)`.

## Flujos principales
1. **Envío web (`IndexController::sendSuggestion`)**:
   - Valida con `SuggestionRequest` (aceptación de `terms`, coherencia `group_id` ↔ `type_id`, máximo 2 MB en `JPEG`/`PNG`/`WebP`, opciones de quiz entre 2 y 4 respuestas con exactamente una correcta).
   - Procesa y limpia EXIF de la imagen con `ConvertImageToWebp` antes de abrir `DB::transaction()`, borrando la imagen generada si la transacción falla.
   - Si existe usuario con el mismo `nick`, vincula autoría en la posterior aprobación.
2. **Envío API (`V1Controller::sendSuggestion`)**:
   - Usa `Api\SendSuggestionRequest` (`nick` opcional, `title` por defecto `'Sugerencia'`, existencia obligatoria de tipo `chistes` devolviendo `422` si faltara).
3. **Aprobación atómica en `/admin` (`Suggestion::approve()`)**:
   - Comprueba grupo y categorías obligatorios.
   - Dentro de `DB::transaction()`, crea `Content`, sincroniza categorías con `syncWithoutDetaching()` (evitando duplicados con categoría `1`), copia opciones e imágenes a `content-images`/`content-option-images`, marca `approved_at` y hace soft delete de la sugerencia.
   - Si algo falla dentro de la transacción, borra las copias nuevas y conserva los archivos originales. Solo tras confirmar el commit elimina las imágenes origen de sugerencias.

## Puntos de entrada
- **Web pública**: `POST /suggestion/send` (`recaptcha:0.7,suggestion_send`, `throttle.real.ip:10,1`).
- **API v1**: `POST /api/v1/suggestion/send` (`auth:sanctum`, `throttle.real.ip:10,1`).
- **Filament (`/admin`)**: `Admin\SuggestionResource` (grupo `'Moderación'`) con `OptionsRelationManager` y acción confirmada `Aprobar` visible cuando `approved_at === null && deleted_at === null`.

## Dependencias
- **Depende de**: `Type`, `Group`, `Category`, `Content`, `ContentOption`, `User`, `ConvertImageToWebp`, `VerifyRecaptcha`, `IpHelper`, `ThrottleWithRealIp`, `CacheInvalidationService`.
- **Usado por**: `StatsHelper` (cómputo de sugerencias pendientes/aceptadas) y `Admin\ContentOverview` / dashboard.

## Configuración
- Límite de tasa web y API: 10 peticiones por minuto por IP real (`throttle.real.ip:10,1`).
- reCAPTCHA v3: acción `suggestion_send`, umbral `0.7` (omitido si `config('google.recaptcha.secret_key')` está vacío).

## Trampas conocidas
- Igual que `Content`, `Suggestion::boot()` adjunta categoría ID `1` tras crearse si no tiene categorías; `SuggestionFactory::create()` invoca `CategoryFactory::ensureGeneralCategoryExists()`.
- En el formulario web (`partials/forms/_form_suggestions.blade.php` + `resources/js/suggestions_forms.js`), `group_id` debe pertenecer al `type_id` elegido y los botones de respuesta correcta preservan `old()` tras errores de validación.
- Al aprobar sugerencias en BD con `deleted_at` posterior o borrado lógico, `Suggestion::boot()` en evento `deleting` borra `image_path` mediante `DeletesStoredImages` solamente en borrado definitivo/reemplazo; dentro de `approve()`, los archivos originales se eliminan explícitamente tras el commit exitoso.

## Tests que lo cubren
- `tests/Feature/DatabasePerformanceAndAtomicityTest.php`
- `tests/Feature/ApiAndValidationStabilityTest.php`
- `tests/Feature/ReliabilityAndArchitectureTest.php`
- `tests/Feature/AccessibilityAndPrivacyTest.php`
- `tests/Feature/ResponsiveUxAndW3cTest.php`
- `tests/Feature/FilamentPanelsUxTest.php`
- `tests/Feature/FactoriesAndCommandsTest.php`
- `tests/Unit/ImageSanitizationAndCleanupTest.php`

## Pendiente real
- Sin deuda técnica pendiente del alcance actual.

---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
