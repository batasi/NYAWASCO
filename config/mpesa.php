<?php

return [
    'consumer_key'        => env('MPESA_APP_CONSUMER_KEY'),
    'consumer_secret'     => env('MPESA_APP_CONSUMER_SECRET'),
    'business_shortcode'  => env('MPESA_BUSINESS_SHORTCODE'),
    'business_till'       => env('MPESA_BUSINESS_TILL'),
    'lnm_passkey'         => env('MPESA_LNM_PASSKEY'),
    'initiator_name'      => env('MPESA_INITIATOR_NAME'),
    'passkey'             => env('MPESA_PASSKEY'),
    'stk_callback_url'    => env('MPESA_STK_CALLBACK_URL'),
    'b2c_callback_url'    => env('MPESA_B2C_CALLBACK_URL'),
    'b2c_timeout_url'     => env('MPESA_B2C_TIMEOUT_URL'),
];
