# Módulo de Reportes (Report)

## Descripción General
El módulo `Report` provee a los consumidores de la API y visitantes de la web un mecanismo para notificar sobre contenido inapropiado, ofensivo o erróneo dentro de la plataforma.

## Componentes y Modelos
- **Report**: Almacena los reportes enviados por los usuarios. Contiene la información de qué contenido está siendo reportado, el motivo (razón) y cualquier comentario adicional provisto por el usuario.

## Funcionalidad Técnica
1. **Puntos de Entrada**:
   - Endpoint protegido de la API para generar reportes (`POST /api/v1/report/send` con `auth:sanctum`).
   - El campo `description` es opcional en el payload de entrada (`nullable|string|max:1024`) y se normaliza a cadena vacía (`''`) al persistir para respetar la columna `NOT NULL` en base de datos sin romper clientes existentes.
   - Integración potencial desde la web pública (TODO list).
2. **Moderación**:
   - Los reportes son visibles en el backoffice (`/admin`, grupo `'Moderación'`) a través de `Admin/ReportResource` (con `$recordTitleAttribute = 'title'`).
   - El formulario y la tabla muestran todas las etiquetas en español («Informante») y permiten filtrar por `status`, `type`, `priority`, `reportable_type` y resolución (`resolved_at`), así como marcar reportes como resueltos o rechazados.
3. **Relaciones**:
   - `belongsTo`: Content. Cada reporte está ligado intrínsecamente a un contenido particular.


---
> Creado: 2026-06-29 · Última revisión: 2026-11-20
