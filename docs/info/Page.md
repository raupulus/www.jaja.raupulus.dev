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
   - Existe una ruta índice para listar todas las páginas creadas (`/pages/index`).
3. **SEO y Metadatos**:
   - Las páginas generadas insertan automáticamente metadatos personalizados en el layout `head.blade.php`, optimizando la previsualización en redes sociales y la indexación por parte de motores de búsqueda.
4. **Filament Resource**:
   - Son gestionadas desde el panel de administrador empleando editores de texto enriquecido provistos por Filament (como Trix o RichEditor).
