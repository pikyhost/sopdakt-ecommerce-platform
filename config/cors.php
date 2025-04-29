<?php

return [

    'paths' => [
        'api/*',
        'docs',
        'docs/*',
        'docs.openapi',
        'docs.postman',
        '*', // allow all routes if needed
    ],

    'allowed_methods' => ['*'], // Allow all HTTP methods

    'allowed_origins' => [
        'http://localhost:5173',          // Local frontend
        'https://sopdakt.netlify.app',    // Live Netlify frontend
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
