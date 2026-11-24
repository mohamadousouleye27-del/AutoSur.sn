<?php

return [
    'orange' => [
        // En-tête d'autorisation affiché dans « MyApps » sur developer.orange.com
        'authorization' => env('ORANGE_SMS_AUTHORIZATION'),
        // Numéro expéditeur du Sénégal indiqué dans la documentation : tel:+2210000
        'sender_number' => env('ORANGE_SMS_SENDER_NUMBER', '2210000'),
        // Nom personnalisé approuvé par Orange (11 caractères maximum), ex : AutoSur
        'sender_name'   => env('ORANGE_SMS_SENDER_NAME'),
    ],
];