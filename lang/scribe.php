<?php

return [
    "labels" => [
        "search" => "Buscar",
        "base_url" => "URL Base",
    ],

    "auth" => [
        "none" => "Esta API no requiere autenticación.",
        "instruction" => [
            "query" => <<<TEXT
                Para autenticar peticiones, incluye un parámetro de consulta **`:parameterName`** en la petición.
                TEXT,
            "body" => <<<TEXT
                Para autenticar peticiones, incluye un parámetro **`:parameterName`** en el cuerpo de la petición.
                TEXT,
            "query_or_body" => <<<TEXT
                Para autenticar peticiones, incluye un parámetro **`:parameterName`** ya sea en la cadena de consulta o en el cuerpo de la petición.
                TEXT,
            "bearer" => <<<TEXT
                Para autenticar peticiones, incluye una cabecera **`Authorization`** con el valor **`"Bearer :placeholder"`**.
                TEXT,
            "basic" => <<<TEXT
                Para autenticar peticiones, incluye una cabecera **`Authorization`** con el formato **`"Basic {credenciales}"`**.
                El valor de `{credenciales}` debe ser tu nombre de usuario/id y tu contraseña, unidos con dos puntos (:),
                y luego codificado en base64.
                TEXT,
            "header" => <<<TEXT
                Para autenticar peticiones, incluye una cabecera **`:parameterName`** con el valor **`":placeholder"`**.
                TEXT,
        ],
        "details" => <<<TEXT
            Todos los endpoints que requieren autenticación están marcados con una etiqueta `requiere autenticación` en la documentación a continuación.
            TEXT,
    ],

    "headings" => [
        "introduction" => "Introducción",
        "auth" => "Autenticando peticiones",
    ],

    "endpoint" => [
        "request" => "Petición",
        "headers" => "Cabeceras",
        "url_parameters" => "Parámetros de URL",
        "body_parameters" => "Parámetros del cuerpo",
        "query_parameters" => "Parámetros de consulta",
        "response" => "Respuesta",
        "response_fields" => "Campos de respuesta",
        "example_request" => "Ejemplo de petición",
        "example_response" => "Ejemplo de respuesta",
        "responses" => [
            "binary" => "Datos binarios",
            "empty" => "Respuesta vacía",
        ],
    ],

    "try_it_out" => [
        "open" => "Pruébalo ⚡",
        "cancel" => "Cancelar 🛑",
        "send" => "Enviar Petición 💥",
        "loading" => "⏱ Enviando...",
        "received_response" => "Respuesta recibida",
        "request_failed" => "La petición falló con error",
        "error_help" => <<<TEXT
            Consejo: Verifica que estés conectado correctamente a la red.
            Si eres el mantenedor de esta API, verifica que tu API esté funcionando y que hayas habilitado CORS.
            Puedes revisar la consola de Herramientas de Desarrollador para información de depuración.
            TEXT,
    ],

    "links" => [
        "postman" => "Ver colección de Postman",
        "openapi" => "Ver especificación OpenAPI",
    ],
];
