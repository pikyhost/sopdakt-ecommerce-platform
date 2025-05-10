<?php

return [
    'ClientInfo' => [
        'UserName' => env('ARAMEX_USERNAME'),
        'Password' => env('ARAMEX_PASSWORD'),
        'Version' => env('ARAMEX_VERSION', 'v1.0'),
        'AccountNumber' => env('ARAMEX_ACCOUNT_NUMBER'),
        'AccountPin' => env('ARAMEX_ACCOUNT_PIN'),
        'AccountEntity' => env('ARAMEX_ACCOUNT_ENTITY'),
        'AccountCountryCode' => env('ARAMEX_ACCOUNT_COUNTRY_CODE'),
    ],
    'product_group' => env('ARAMEX_PRODUCT_GROUP', 'EXP'),
    'product_type' => env('ARAMEX_PRODUCT_TYPE', 'PPX'),
    'payment_type' => env('ARAMEX_PAYMENT_TYPE', 'P'),
    'payment_option' => env('ARAMEX_PAYMENT_OPTION', null),
    'shipper' => [
        'name' => env('ARAMEX_SHIPPER_NAME', 'Your Company'),
        'company' => env('ARAMEX_SHIPPER_COMPANY', 'Your Company'),
        'phone' => env('ARAMEX_SHIPPER_PHONE', '1234567890'),
        'email' => env('ARAMEX_SHIPPER_EMAIL', 'shipper@example.com'),
        'address' => env('ARAMEX_SHIPPER_ADDRESS', '123 Shipper Street'),
        'city' => env('ARAMEX_SHIPPER_CITY', 'Riyadh'),
        'country_code' => env('ARAMEX_SHIPPER_COUNTRY_CODE', 'SA'),
        'zip_code' => env('ARAMEX_SHIPPER_ZIP_CODE', ''),
    ],
    'ENV' => 'TEST', // Use 'TEST' for testing, 'LIVE' for production
];
