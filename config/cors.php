<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". Simply set the paths you wish to allow, along with any
    | origins, headers, or methods. You may also enable credentials.
    |
    */

    'paths' => ['api/*', 'api/worker/*', 'api/medical-worker/*', 'worker/*', 'medical-worker/*', 'storage/*'],

    // During local development you can leave "*". In production replace with
    // your actual frontend domain(s).
    'allowed_origins' => ['*'], // Allow all origins for development

    'allowed_origins_patterns' => [
        '/^http:\/\/localhost(:\d+)?$/',
        '/^http:\/\/127\.0\.0\.1(:\d+)?$/',
        '/^http:\/\/\d+\.\d+\.\d+\.\d+(:\d+)?$/',
    ],

    'allowed_methods'   => ['*'],
    'allowed_headers'   => ['*'],
    'exposed_headers'   => ['Authorization'],

    'max_age' => 0,
    'supports_credentials' => true,
];
