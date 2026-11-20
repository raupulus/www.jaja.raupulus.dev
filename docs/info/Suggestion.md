# Módulo de Sugerencias (Suggestion)

## Descripción General
El módulo `Suggestion` es el puente de entrada para las aportaciones de la comunidad. Permite a los usuarios (tanto anónimos como registrados) proponer chistes, preguntas y adivinanzas a la plataforma.

## Componentes y Modelos
- **Suggestion**: Almacena el contenido propuesto, manteniendo campos similares al modelo `Content` (texto, imágenes, nick del creador).
- **SuggestionCategory**: Tabla pivot para relacionar la sugerencia con una o múltiples categorías antes de ser aprobada.
- **SuggestionOption**: Almacena las posibles respuestas para las sugerencias que son de tipo **Quiz**, para su posterior revisión.

## Funcionalidad Técnica
1. **Flujo de Moderación y Formulario Web**:
   - Las sugerencias ingresan mediante el formulario en el frontend (`POST /suggestion/send`, exigiendo `terms` aceptado, reCAPTCHA v3 con acción `suggestion_send` cuando está configurado y límite por IP) o mediante la API (`POST /api/v1/suggestion/send` con `auth:sanctum`), quedando con estado "pendiente".
   - El formulario web (`partials/forms/_form_suggestions.blade.php` y `resources/js/suggestions_forms.js`) incluye `<label for="...">` explícitos en todos los campos, `<fieldset><legend>` para respuestas de quiz, botones nativos `<button type="button" aria-pressed>` operables con teclado para marcar la respuesta correcta, `<label for="content-image">` para vista previa, `aria-invalid` + `aria-describedby` + `role="alert"` / `role="status"` / `aria-live`, preserva `old('group_id')` (cuando coincide con `type_id`), `old('answer*_correct')` y `old('terms')` tras errores de validación e impide dobles envíos bloqueando el botón en `submit` («Enviando...»).
   - En la API (`Api\SendSuggestionRequest`), `nick` es opcional (`null` si viene vacío), `title` toma `'Sugerencia'` como valor por defecto cuando se omite y el tipo `chistes` se valida con existencia obligatoria (`422` si faltara en base de datos).
   - Utilizan protecciones como ReCaptcha v3 y límites de peticiones (rate limiting) por IP para evitar spam.
   - Un administrador evalúa la sugerencia en el panel de Filament.
   - Al aprobarse, un evento o acción en el panel toma los datos de la sugerencia (incluyendo imágenes cargadas localmente y opciones del quiz) y los clona en la tabla `Content`.
   - Tras ser aprobada, el botón de aprobación se oculta para prevenir duplicidad.
2. **Manejo de Imágenes**: Las imágenes asociadas se restringen a 2MB. Si una sugerencia es rechazada y borrada (Force Delete), la imagen subida es eliminada físicamente del almacenamiento.
3. **Asignación de Usuarios**: Si el "nick" proporcionado en la sugerencia coincide con un usuario registrado, la plataforma vincula automáticamente el contenido a ese usuario para sumar estadísticas a su perfil (uploader).

## Relaciones Eloquent
   - `belongsTo`: Group, Type.
   - `belongsToMany`: Category (a través de SuggestionCategory).
   - `hasMany`: SuggestionOption.


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
