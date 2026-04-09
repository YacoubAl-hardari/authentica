<?php

return [
    'api_key' => env('AUTHENTICA_API_KEY'),
    'base_url' => env('AUTHENTICA_BASE_URL', 'https://api.authentica.sa/api/v2'),
    'timeout' => 30,
    'default_channel' => env('AUTHENTICA_DEFAULT_CHANNEL', 'sms'), // sms, whatsapp, email
    'fallback_channel' => env('AUTHENTICA_FALLBACK_CHANNEL'),
    'templates' => [
        'otp' => env('AUTHENTICA_DEFAULT_TEMPLATE_ID', 1),
        'fallback' => env('AUTHENTICA_FALLBACK_TEMPLATE_ID', 2),
    ],
    'sender' => [
        'name' => env('AUTHENTICA_DEFAULT_SENDER_NAME'),
        'email' => env('AUTHENTICA_DEFAULT_EMAIL'),
    ],
];