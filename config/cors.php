<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'], // Allow all paths
    
    'allowed_methods' => ['*'], // Allow all HTTP methods
    
    'allowed_origins' => [
        'https://samdigitalphotography.com',
        'http://samdigitalphotography.com',
    ],
    
    'allowed_origins_patterns' => [],
    
    'allowed_headers' => ['*'],
    
    'exposed_headers' => [],
    
    'max_age' => 0,
    
    'supports_credentials' => true, // Important for authenticated requests
];