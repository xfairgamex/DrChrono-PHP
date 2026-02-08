<?php

declare(strict_types=1);

return [
    'base_uri' => env('DRCHRONO_BASE_URI', 'https://app.drchrono.com'),
    'client_id' => env('DRCHRONO_CLIENT_ID'),
    'client_secret' => env('DRCHRONO_CLIENT_SECRET'),
    'redirect_uri' => env('DRCHRONO_REDIRECT_URI'),
    'access_token' => env('DRCHRONO_ACCESS_TOKEN'),
    'refresh_token' => env('DRCHRONO_REFRESH_TOKEN'),
    'token_expires_at' => env('DRCHRONO_TOKEN_EXPIRES_AT'),
    'timeout' => env('DRCHRONO_TIMEOUT', 30),
    'connect_timeout' => env('DRCHRONO_CONNECT_TIMEOUT', 10),
    'user_agent' => env('DRCHRONO_USER_AGENT', 'DrChrono-PHP-SDK/1.0'),
    'debug' => env('DRCHRONO_DEBUG', false),
    'max_retries' => env('DRCHRONO_MAX_RETRIES', 3),
    'retry_delay' => env('DRCHRONO_RETRY_DELAY', 1000),
    'api_version' => env('DRCHRONO_API_VERSION'),
    'http_client' => env('DRCHRONO_HTTP_CLIENT', 'laravel'),
    'use_laravel_collections' => env('DRCHRONO_USE_LARAVEL_COLLECTIONS', true),
    'auto_refresh_token' => env('DRCHRONO_AUTO_REFRESH_TOKEN', true),
    'rate_limit_throttle_enabled' => env('DRCHRONO_RATE_LIMIT_THROTTLE_ENABLED', true),
    'laravel_middleware' => [],
];
