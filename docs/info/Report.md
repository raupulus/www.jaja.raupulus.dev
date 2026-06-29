# Módulo de Reportes (Report)

## Descripción General
El módulo `Report` provee a los consumidores de la API y visitantes de la web un mecanismo para notificar sobre contenido inapropiado, ofensivo o erróneo dentro de la plataforma.

## Componentes y Modelos
- **Report**: Almacena los reportes enviados por los usuarios. Contiene la información de qué contenido está siendo reportado, el motivo (razón) y cualquier comentario adicional provisto por el usuario.

## Funcionalidad Técnica
1. **Puntos de Entrada**:
   - Endpoint público de la API para generar reportes (`POST /report`).
   - Integración potencial desde la web pública (TODO list).
2. **Moderación**:
   - Los reportes son visibles en el backoffice (`/admin`) a través de un Recurso de Filament para `Report`.
   - Permite a los administradores evaluar el reporte, visualizar el contenido vinculado y tomar acciones correctivas (ej. forzar el borrado del contenido o simplemente descartar el reporte).
3. **Relaciones**:
   - `belongsTo`: Content. Cada reporte está ligado intrínsecamente a un contenido particular.
