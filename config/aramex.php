<?php

return [
    'sandbox' => true,
    'base_url' => env('ARAMEX_BASE_URL', 'https://ws.dev.aramex.net/ShippingAPI.V2/'),

    'auth' => [
        'UserName' => env('ARAMEX_USERNAME'),
        'Password' => env('ARAMEX_PASSWORD'),
        'Version' => 'v1',
        'AccountNumber' => env('ARAMEX_ACCOUNT_NUMBER'),
        'AccountPin' => env('ARAMEX_ACCOUNT_PIN'),
        'AccountEntity' => env('ARAMEX_ACCOUNT_ENTITY'),
        'AccountCountryCode' => env('ARAMEX_ACCOUNT_COUNTRY_CODE'),
    ],
];
