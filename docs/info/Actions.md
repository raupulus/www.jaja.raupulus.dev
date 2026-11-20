# Módulo de Acciones (Actions)

## Descripción General
El módulo `Actions` almacena clases de única responsabilidad utilizadas a lo largo de la aplicación para procesar lógicas de negocio concretas, favoreciendo la reutilización de código y manteniendo los controladores limpios.

## Componentes
1. **ConvertImageToWebp (`app/Actions/ConvertImageToWebp.php`)**:
   - Encargado de procesar las imágenes subidas por usuarios o administradores (JPG, PNG o WebP).
   - Aplica orientación automática según EXIF (`orient()`), limita la resolución máxima a `2560×2560px` manteniendo proporción (`scaleDown()`), elimina explícitamente metadatos EXIF/IPTC (`WebpEncoder(quality: 85, strip: true)`) y guarda el resultado en `Storage::disk('public')`.
   - Se utiliza tanto en la subida pública del frontend como en las operaciones internas desde los paneles de Filament (`/admin` y `/panel`, limitados a `maxSize(2048)`).

## Funcionalidad Técnica
- Las acciones actúan como servicios invocables (a menudo usando el método mágico `__invoke`).
- Permiten centralizar reglas complejas (como manejo de Intervention Image o compresión), reduciendo la duplicación de código en todo el proyecto.


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
