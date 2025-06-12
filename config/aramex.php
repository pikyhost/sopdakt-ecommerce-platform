<?php

return [
    'api_url' => env('ARAMEX_API_URL', 'https://ws.sbx.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc/json/'),
    'client_info' => [
        'UserName' => env('ARAMEX_USERNAME', 'testingapi@aramex.com'),
        'Password' => env('ARAMEX_PASSWORD', 'R123456789$r'),
        'Version' => 'v1',
        'AccountNumber' => env('ARAMEX_ACCOUNT_NUMBER', '987654'),
        'AccountPin' => env('ARAMEX_ACCOUNT_PIN', '226321'),
        'AccountEntity' => env('ARAMEX_ACCOUNT_ENTITY', 'CAI'),
        'AccountCountryCode' => env('ARAMEX_ACCOUNT_COUNTRY_CODE', 'EG'),
        'Source' => 24,
    ],
    'shipper' => [
        'address' => env('ARAMEX_SHIPPER_ADDRESS', '123 Main St, Cairo'),
        'name' => env('ARAMEX_SHIPPER_NAME', 'Your Company Name'),
        'company' => env('ARAMEX_SHIPPER_COMPANY', 'Your Company'),
        'phone' => env('ARAMEX_SHIPPER_PHONE', '1234567890'),
        'email' => env('ARAMEX_SHIPPER_EMAIL', 'info@yourcompany.com'),
    ],
];
