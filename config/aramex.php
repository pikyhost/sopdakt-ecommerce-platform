<?php

return [
    'api_url' => env('ARAMEX_API_URL', 'https://ws.dev.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json/'),
    'client_info' => [
        'UserName' => env('ARAMEX_USERNAME', 'testingapi@aramex.com'),
        'Password' => env('ARAMEX_PASSWORD', 'R123456789$r'),
        'Version' => env('ARAMEX_VERSION', 'v1'),
        'AccountNumber' => env('ARAMEX_ACCOUNT_NUMBER', '987654'),
        'AccountPin' => env('ARAMEX_ACCOUNT_PIN', '226321'),
        'AccountEntity' => env('ARAMEX_ACCOUNT_ENTITY', 'CAI'),
        'AccountCountryCode' => env('ARAMEX_ACCOUNT_COUNTRY_CODE', 'EG'),
        'Source' => 24,
    ],
];
