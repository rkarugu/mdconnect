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

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Specific origins for development (required when using credentials)
    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:8080', 
        'http://localhost:8000',
        'http://127.0.0.1:8000',
        'http://localhost:64022', // Current Flutter web dev server
    ],

    'allowed_origins_patterns' => [
        '/^http:\/\/localhost(:\d+)?$/',
        '/^http:\/\/127\.0\.0\.1(:\d+)?$/',
        '/^http:\/\/\d+\.\d+\.\d+\.\d+(:\d+)?$/',
        '/^https?:\/\/.*\.ngrok\.io$/', // For ngrok tunnels
    ],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    
    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
    ],
    
    'exposed_headers' => [
        'Authorization',
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
    ],

    'max_age' => 86400, // 24 hours
    'supports_credentials' => true, // Enable credentials for session-based auth
];
