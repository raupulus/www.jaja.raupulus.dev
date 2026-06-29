# Módulo de Sugerencias (Suggestion)

## Descripción General
El módulo `Suggestion` es el puente de entrada para las aportaciones de la comunidad. Permite a los usuarios (tanto anónimos como registrados) proponer chistes, preguntas y adivinanzas a la plataforma.

## Componentes y Modelos
- **Suggestion**: Almacena el contenido propuesto, manteniendo campos similares al modelo `Content` (texto, imágenes, nick del creador).
- **SuggestionCategory**: Tabla pivot para relacionar la sugerencia con una o múltiples categorías antes de ser aprobada.
- **SuggestionOption**: Almacena las posibles respuestas para las sugerencias que son de tipo **Quiz**, para su posterior revisión.

## Funcionalidad Técnica
1. **Flujo de Moderación**:
   - Las sugerencias ingresan mediante un formulario en el frontend o endpoint de la API con estado "pendiente".
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
