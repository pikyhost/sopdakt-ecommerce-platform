<?php

return [
    'paths' => [
        'api/*',
        'login',
        'logout',
        'forgot-password',
        'reset-password',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',          // Local dev
        'https://redaa.store/', 
                   // Production (HTTPS only)
    ],

    'allowed_origins_patterns' => [],     // Optional for subdomains

    'allowed_headers' => ['*'],           // Allows Authorization, Content-Type, etc.

    'exposed_headers' => [],              // Add custom headers if needed

    'max_age' => 0,                       // Preflight cache duration (0 = no cache)

    'supports_credentials' => true,       // Needed for cookies/auth headers
];
