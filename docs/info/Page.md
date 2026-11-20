# Módulo de Páginas Estáticas (Page)

## Descripción General
El módulo `Page` es un mini-CMS integrado destinado a gestionar páginas informativas estáticas dentro de la plataforma. Esto evita tener que modificar vistas (Blade templates) por cada cambio de textos legales o informativos.

## Componentes y Modelos
- **Page**: Almacena el título, slug, contenido enriquecido (HTML/Markdown) y metadatos SEO.

## Funcionalidad Técnica
1. **Gestión de Páginas Legales e Informativas**:
   - Usado intensamente para páginas requeridas como: Normas de Uso, Política de Privacidad, Política de Cookies y Condiciones de Cesión de Contenido.
2. **Sistema de Enrutado**:
   - El enrutamiento capta el `slug` guardado en la base de datos (ej. `/pagina/normas`) y renderiza la vista pública.
   - Existe una ruta índice para listar todas las páginas creadas (`/paginas`).
3. **SEO, Metadatos y Datos Estructurados**:
   - Las páginas generadas insertan automáticamente metadatos personalizados en el layout `head.blade.php` (`og_type="article"`, `description` recortada a 155 caracteres), optimizando la previsualización en redes sociales y la indexación por parte de motores de búsqueda.
   - `pages/index.blade.php` emite JSON-LD `CollectionPage` + `BreadcrumbList` (con jerarquía `h1 → h2` y anchor texts descriptivos `Leer {título}`), y `pages/show.blade.php` emite `WebPage` + `BreadcrumbList`.
4. **Filament Resource e Imágenes**:
   - Son gestionadas desde el panel de administrador empleando editores de texto enriquecido provistos por Filament (como Trix o RichEditor) y subidas de imagen limitadas a `2048 KB` (`JPEG`, `PNG`, `WebP`).
   - Al actualizar la imagen o eliminar una página, `DeletesStoredImages` borra el fichero previo de `Storage::disk('public')`.


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
