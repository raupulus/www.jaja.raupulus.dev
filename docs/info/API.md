# Arquitectura y Diseño de la API

## Descripción General
La API de JaJa Project es el principal medio por el cual clientes externos (Bots de Discord, aplicaciones SPA, PWAs, etc.) consumen la base de datos comunitaria de chistes, preguntas y adivinanzas.

## Componentes Técnicos y Estándares
1. **Laravel Sanctum**:
   - Empleado para la autenticación basada en tokens.
   - Endpoints privados como obtener listas paginadas, filtrados exhaustivos, etc., requieren que el usuario esté registrado y pase un Bearer Token (`Authorization: Bearer {token}`).
2. **Scribe**:
   - Se utiliza para la generación automatizada de documentación para la API a partir de bloques de comentarios (PHPDoc) y código fuente.
   - La documentación estática compilada se visualiza en `/docs`.
   - **Comando clave**: `php artisan scribe:generate` tras cada modificación.
3. **Límites de Peticiones (Rate Limiting)**:
   - Los endpoints públicos están sujetos a límites de peticiones para mitigar ataques DoS o uso abusivo.
   - Existe un sistema de bloqueo por IPs excluidas.
   - Los endpoints privados (autenticados) tienen umbrales de limitación mayores.

## Principales Funcionalidades
1. **Endpoint Público `Random`**:
   - Devuelve un contenido aleatorio sin necesidad de autenticación.
   - Filtra internamente para excluir grupos como "Adultos" con el fin de proteger las integraciones públicas por defecto.
2. **Endpoints de Ingesta**:
   - Existen endpoints para el envío de sugerencias comunitarias (ej. subir nuevos chistes) y para el reporte de contenido inapropiado.
3. **Formatos**:
   - Toda la comunicación se debe realizar forzando los headers `Accept: application/json` y `Content-type: application/json`.
   - Las respuestas con colecciones múltiples o vacías siempre deben adaptar sus envoltorios JSON, devolviendo plural o singular según el caso.
