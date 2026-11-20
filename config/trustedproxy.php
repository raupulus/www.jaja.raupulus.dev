<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Proxies de confianza
    |--------------------------------------------------------------------------
    |
    | Lista de direcciones IP o rangos CIDR separados por comas de los proxies
    | inversos confiables (Apache, Nginx, Cloudflare), o '*' únicamente bajo
    | red aislada. Con valor null no se confía en cabeceras reenviadas.
    |
    */
    'proxies' => env('TRUSTED_PROXIES') ?: null,
];
