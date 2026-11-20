# Módulo de Acciones (Actions)

## Descripción General
El módulo `Actions` almacena clases de única responsabilidad utilizadas a lo largo de la aplicación para procesar lógicas de negocio concretas, favoreciendo la reutilización de código y manteniendo los controladores limpios.

## Componentes
1. **ConvertImageToWebp (`app/Actions/ConvertImageToWebp.php`)**:
   - Encargado de procesar las imágenes subidas por usuarios o administradores (JPG, PNG o WebP).
   - Aplica orientación automática según EXIF (`orient()`), limita la resolución máxima a `2560×2560px` manteniendo proporción (`scaleDown()`), elimina explícitamente metadatos EXIF/IPTC (`WebpEncoder(quality: 85, strip: true)`) y guarda el resultado en `Storage::disk('public')`.
   - Se utiliza tanto en la subida pública del frontend como en las operaciones internas desde los paneles de Filament (`/admin` y `/panel`).
2. **WebpImageUpload (`app/Filament/Support/WebpImageUpload.php`)**:
   - Factoría reutilizable para todos los formularios Filament de `/admin` y `/panel` (13 campos `FileUpload` unificados) que estandariza disco público, límite de `2048 KB`, MIME (`image/jpeg`, `image/png`, `image/webp`), editor integrado, conversión mediante `ConvertImageToWebp` y logging de errores en inglés.
3. **IpHelper (`app/Helpers/IpHelper.php`)**:
   - Resuelve de forma centralizada la IP real del cliente (`getClientIp(Request $request)`) para `ThrottleWithRealIp` y los `FormRequest` web y API, consultando `CF-Connecting-IP` únicamente cuando `TrustProxies` (`config/trustedproxy.php`) valida que la petición proviene de un proxy de confianza.

## Funcionalidad Técnica
- Las acciones actúan como servicios invocables (a menudo usando el método mágico `__invoke`).
- Permiten centralizar reglas complejas (como manejo de Intervention Image o compresión), reduciendo la duplicación de código en todo el proyecto.


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
