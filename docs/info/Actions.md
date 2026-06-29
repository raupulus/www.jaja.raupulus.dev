# Módulo de Acciones (Actions)

## Descripción General
El módulo `Actions` almacena clases de única responsabilidad utilizadas a lo largo de la aplicación para procesar lógicas de negocio concretas, favoreciendo la reutilización de código y manteniendo los controladores limpios.

## Componentes
1. **ConvertImageToWebp (`app/Actions/ConvertImageToWebp.php`)**:
   - Encargado de procesar todas las imágenes subidas por los usuarios (al crear sugerencias o administrar contenido).
   - Escala, comprime y convierte dinámicamente cualquier formato de imagen (JPEG, PNG) al formato web moderno `WebP`.
   - Se utiliza tanto en la subida pública del frontend como en las operaciones internas desde el panel de Filament.

## Funcionalidad Técnica
- Las acciones actúan como servicios invocables (a menudo usando el método mágico `__invoke`).
- Permiten centralizar reglas complejas (como manejo de Intervention Image o compresión), reduciendo la duplicación de código en todo el proyecto.
