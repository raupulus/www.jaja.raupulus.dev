# Módulo de Contenido (Content)

## Descripción General
El módulo `Content` es el núcleo de la aplicación, responsable de almacenar y gestionar el contenido final visible en la plataforma, el cual incluye chistes, preguntas tipo quiz y adivinanzas.

## Componentes y Modelos
Este módulo se compone de tres entidades principales:
- **Content**: Almacena el cuerpo principal del contenido (texto, imágenes opcionales) y sus metadatos (estado, origen, votos, etc.).
- **ContentCategory**: Tabla pivot / Modelo intermedio que gestiona la relación muchos a muchos entre los contenidos (`Content`) y las categorías (`Category`).
- **ContentOption**: Específico para el contenido de tipo **Quiz**. Almacena las posibles respuestas para una pregunta, indicando cuál es la correcta.

## Funcionalidad Técnica
1. **Tipos de Contenido y Carga Ansiosa (Cero `N+1`)**: El modelo `Content` tiene relación con `Type` (vía `Group`), `Group` y `User`. Con `Model::preventLazyLoading(! app()->isProduction())` activo en `AppServiceProvider`, todas las consultas en `IndexController`, `ContentController` y `Api\V1Controller` precargan `group.type`, `options` ordenadas por `order` y `user:id,nick` para renderizar `partials/_content.blade.php` y `ContentResource` sin consultas `N+1`.
2. **Ciclo de Vida, Paneles, Caché e Imágenes**:
   - Los contenidos generalmente provienen del módulo `Suggestion`. Cuando una sugerencia es aprobada en el panel de Filament, se transforma o traslada a un `Content` definitivo dentro de una transacción atómica.
   - Mediante el trait `DeletesStoredImages`, `Content` y `ContentOption` eliminan automáticamente en `Storage::disk('public')` sus ficheros físicos al reemplazar la imagen o ejecutar `forceDelete()`, preservando la imagen por defecto `images/default/content.webp`.
   - `AppServiceProvider` + `App\Services\CacheInvalidationService` invalidan automáticamente las cachés públicas (`home_contents`, `types_index`, `groups_index`, `groups_index_by_type_{id}`, `categories_index`, contadores de `StatsHelper`) y del widget `ContentOverview` (cacheado 300s) al guardar, borrar o restaurar registros.
   - En Filament (`Admin/ContentResource` y `Panel/ContentResource`), los formularios organizan los campos en secciones (`Contenido`, `Clasificación`, `Moderación`) y las tablas agrupan acciones en `ActionGroup`, truncan textos largos (`limit`/`wrap`/`tooltip`) e incluyen filtros por `group_id`, `categories`, `is_adult`, `is_ai` y papelera (`TrashedFilter`). Los `StatsOverviewWidget` declaran `$columnSpan = 'full'` y `$sort` explícito. `Admin/ReportResource` precarga `user` y `assignedTo` en `getEloquentQuery()`.
3. **Estadísticas**: El contenido registra interacciones (risas/votos generados) que posteriormente son calculadas mediante comandos asíncronos y mostradas tanto en el panel de administrador como en resúmenes públicos.
4. **Relaciones Eloquent**:
   - `belongsTo`: Group, Type.
   - `belongsToMany`: Category (a través de ContentCategory).
   - `hasMany`: ContentOption.

## Endpoints Asociados
Los contenidos se exponen de forma pública a través de endpoints de la API (documentados en Scribe) para devolver elementos aleatorios, o filtrados por Grupo, Tipo y Categoría, excluyendo contenido explícito si es necesario.


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
