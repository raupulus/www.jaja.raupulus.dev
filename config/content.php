<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Grupos excluidos de listados públicos y difusión automática
    |--------------------------------------------------------------------------
    |
    | IDs numéricos de grupos sensibles o internos (por ejemplo humor adulto /
    | humor negro) excluidos por defecto de la portada, API pública y cron social.
    |
    */
    'excluded_public_group_ids' => array_values(array_filter(
        array_map('intval', explode(',', (string) env('CONTENT_EXCLUDED_PUBLIC_GROUP_IDS', '4,14'))),
        static fn (int $id): bool => $id > 0
    )),
];
