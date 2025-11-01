<?php

return [
    'environment' => env('MPESA_ENV', 'production'),

    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),

    'business_shortcode' => env('MPESA_BUSINESS_SHORTCODE'),
    'business_till' => env('MPESA_BUSINESS_TILL'),
    'lnm_passkey' => env('MPESA_LNM_PASSKEY'),
    'passkey' => env('MPESA_PASSKEY'),

    'initiator_name' => env('MPESA_INITIATOR_NAME'),

    'stk_callback_url' => env('MPESA_STK_CALLBACK_URL'),
    'b2c_callback_url' => env('MPESA_B2C_CALLBACK_URL'),
    'b2c_timeout_url' => env('MPESA_B2C_TIMEOUT_URL'),

    'queue_connection' => env('MPESA_QUEUE_CONNECTION', 'database'),
];
