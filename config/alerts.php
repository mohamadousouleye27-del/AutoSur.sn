<?php

return [
    'channels' => [
        'mail' => env('ALERTS_MAIL', false),
        'sms'  => env('ALERTS_SMS', true),
    ],

    // 'log' = les SMS sont écrits dans storage/logs/laravel.log (développement)
    'sms_driver' => env('SMS_DRIVER', 'log'),
];