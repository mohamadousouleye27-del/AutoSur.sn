<?php

return [
    // simulation (développement) ou wave (production)
    'driver' => env('PAYMENT_DRIVER', 'simulation'),

    'wave' => [
        'api_key'  => env('WAVE_API_KEY'),
        'base_url' => 'https://api.wave.com',
    ],
];