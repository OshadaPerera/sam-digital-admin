<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'], // Allow all paths

    'allowed_methods' => ['*'], // Allow all HTTP methods

    'allowed_origins' => [
        'https://samdigitalphotography.com',
        'http://samdigitalphotography.com',
        'https://admin.samdigitalphotography.com',
        'http://admin.samdigitalphotography.com',
        // 'http://localhost:5174',
        // 'http://127.0.0.1:5174',
        // 'http://sam-digital-admin.test',
        // 'http://localhost:5173',
        // 'http://127.0.0.1:5173',
    ],

    // 'allowed_origins_patterns' => [
    //     '/^https?:\/\/(localhost|127\.0\.0\.1|sam-digital-admin\.test)(:\d+)?$/',
    // ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Important for authenticated requests
];