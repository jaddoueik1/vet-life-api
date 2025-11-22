<?php

return [
    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'base_url' => env('WHATSAPP_API_BASE', 'https://graph.facebook.com/v19.0'),
    ],
];
