<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'paymob' => [
        'iframe_id' => env('PAYMOB_IFRAME_ID'),
    ],

    'bosta' => [
        'api_key' => env('BOSTA_API_KEY'),
        'api_url' => env('BOSTA_API_URL'),
        'business_location_id' => env('BOSTA_BUSINESS_LOCATION_ID'),
        'webhook_url' => env('BOSTA_WEBHOOK_URL'),
        'webhook_secret' => env('BOSTA_WEBHOOK_SECRET'),
    ],

    'aramex' => [
        'url' => env('ARAMEX_API_URL', 'https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json'),
        'username' => env('ARAMEX_USERNAME', 'testingapi@aramex.com'),
        'password' => env('ARAMEX_PASSWORD', 'R123456789$r'),
        'account_number' => env('ARAMEX_ACCOUNT_NUMBER', '20016'),
        'account_pin' => env('ARAMEX_ACCOUNT_PIN', '331421'),
        'account_entity' => env('ARAMEX_ACCOUNT_ENTITY', 'AMM'),
        'account_country_code' => env('ARAMEX_ACCOUNT_COUNTRY_CODE', 'JO'),
    ],
];
