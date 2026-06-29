# Módulo de Contenido (Content)

## Descripción General
El módulo `Content` es el núcleo de la aplicación, responsable de almacenar y gestionar el contenido final visible en la plataforma, el cual incluye chistes, preguntas tipo quiz y adivinanzas.

## Componentes y Modelos
Este módulo se compone de tres entidades principales:
- **Content**: Almacena el cuerpo principal del contenido (texto, imágenes opcionales) y sus metadatos (estado, origen, votos, etc.).
- **ContentCategory**: Tabla pivot / Modelo intermedio que gestiona la relación muchos a muchos entre los contenidos (`Content`) y las categorías (`Category`).
- **ContentOption**: Específico para el contenido de tipo **Quiz**. Almacena las posibles respuestas para una pregunta, indicando cuál es la correcta.

## Funcionalidad Técnica
1. **Tipos de Contenido**: El modelo `Content` tiene una relación con `Type` y `Group`. Dependiendo del tipo, el comportamiento en la API y el frontend varía (ej. si es quiz, se precargan las `ContentOption`).
2. **Ciclo de Vida**: Los contenidos generalmente provienen del módulo `Suggestion`. Cuando una sugerencia es aprobada en el panel de Filament, se transforma o traslada a un `Content` definitivo.
3. **Estadísticas**: El contenido registra interacciones (risas/votos generados) que posteriormente son calculadas mediante comandos asíncronos y mostradas tanto en el panel de administrador como en resúmenes públicos.
4. **Relaciones Eloquent**:
   - `belongsTo`: Group, Type.
   - `belongsToMany`: Category (a través de ContentCategory).
   - `hasMany`: ContentOption.

## Endpoints Asociados
Los contenidos se exponen de forma pública a través de endpoints de la API (documentados en Scribe) para devolver elementos aleatorios, o filtrados por Grupo, Tipo y Categoría, excluyendo contenido explícito si es necesario.
