# Módulo de Taxonomía (Type, Group, Category)

## Descripción General
Este módulo engloba la forma en la que se clasifica todo el contenido y sugerencias de la aplicación. Se compone de tres pilares fundamentales que permiten organizar, filtrar y presentar el contenido tanto en la API como en la interfaz web.

## Componentes y Modelos
1. **Type (Tipo)**:
   - Define el formato o naturaleza del contenido. (Ej: Chiste, Adivinanza, Quiz).
   - Posee campos de imagen y descripción para representarlos visualmente en la plataforma.
   - Afecta la lógica de la aplicación (por ejemplo, los de tipo "Quiz" obligan a manejar y mostrar el modelo `ContentOption`).

2. **Group (Grupo)**:
   - Define el segmento demográfico o tono del contenido. (Ej: Adultos, Infantil, General).
   - Contiene imágenes y descripciones.
   - Tiene lógica de negocio importante ligada a la moderación. Por ejemplo, en los endpoints públicos y redes sociales, el contenido del grupo para "adultos" está excluido por defecto.

3. **Category (Categoría)**:
   - Define etiquetas de clasificación temática más específicas. (Ej: Informáticos, Cortos, Absurdos).
   - Las categorías pueden ser asociadas múltiplemente a un solo contenido mediante la tabla pivot (muchos a muchos).
   - Si no se especifica ninguna en una sugerencia, por defecto se asigna la categoría "General".

## Funcionalidad Técnica
- **Caché**: Se plantea un uso intensivo de caché al cargar listados de categorías, tipos y grupos en las vistas frontend para minimizar las consultas a la base de datos, dado que raramente cambian.
- **Rutas y SEO**: Existen rutas específicas en el sitemap para listados y cruces (por ejemplo, contenido aleatorio de un grupo o categoría).
- **Relaciones**: Son modelos maestros utilizados extensamente en `Content` y `Suggestion` bajo relaciones `belongsTo` (para Type y Group) y `belongsToMany` (para Category).
