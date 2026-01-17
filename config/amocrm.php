<?php

return [
    // Публичная интеграция (OAuth 2.0)
    'client_id'     => env('AMO_CLIENT_ID'),
    'client_secret' => env('AMO_CLIENT_SECRET'),
    'redirect_uri'  => env('AMO_REDIRECT_URI'),
    'subdomain'     => env('AMOCRM_SUBDOMAIN'),
    
    // Приватная интеграция (для обратной совместимости, если нужно)
    'private_token' => env('AMOCRM_PRIVATE_TOKEN'),
];
