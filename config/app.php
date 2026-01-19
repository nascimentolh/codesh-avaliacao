<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Open Food Facts API',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'api_key' => $_ENV['API_KEY'] ?? '',
    'pagination' => [
        'default_page_size' => (int)($_ENV['DEFAULT_PAGE_SIZE'] ?? 20),
        'max_page_size' => (int)($_ENV['MAX_PAGE_SIZE'] ?? 100),
    ],
];
