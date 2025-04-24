<?php

/**
 * Config Array
 * Static and General configuration for the integration
 * Constant Parameters.
 */

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

	// 						DO NOT FORGET TO "php artisan config:cach" AFTER CHANGING


	/**
	 * Aramex Environment
	 *		For Development => 'TEST'
	 *  	For Staging => 'LIVE'
	 */
	'ENV' => 'TEST',



	/**  					Client Information
	 *	Test Credentials
	 * 	I recommend to take your own test account from aramex support or something, because those accounts
	 *  are not stable, Sometimes the request returns an error not defined in there documentation so it will
	 *  take a lot of time tracing the error to find that the error is from the account itself.
	 */
	// 'TEST' => [
	// 	'AccountNumber'		 	=> '102331',
	// 	'UserName'			 	=> 'testingapi@aramex.com',
	// 	'Password'			 	=> 'R123456789$r',
	// 	'AccountPin'		 	=> '321321',
	// 	'AccountEntity'		 	=> 'LON',
	// 	'AccountCountryCode'	=> 'GB',
	// 	'Version'			 	=> 'v1'
	// ],
	'TEST' => [
		'AccountNumber'		 	=> '20016',
		'UserName'			 	=> 'testingapi@aramex.com',
		'Password'			 	=> 'R123456789$r',
		'AccountPin'		 	=> '331421',
		'AccountEntity'		 	=> 'AMM',
		'AccountCountryCode'	=> 'JO',
		'Version'			 	=> 'v1'
	],

	/**  					Client Information
	 *	Live Credentials
	 */
	'LIVE' => [
		'AccountNumber'		 	=> '',
		'UserName'			 	=> '',
		'Password'			 	=> '',
		'AccountPin'		 	=> '',
		'AccountEntity'		 	=> '',
		'AccountCountryCode'	=> '',
		'Version'			 	=> ''
	],

	'CompanyName' => 'Moustafa Allahham',

	/**						Business Attributes
	 *  Usually there are attributes that never change in the projects (specially
	 *  for external integration) depending on business models.
	 *  for example: i dont allow COD (Cash on Delivery) on my e-commerce website
	 *  or my products are made from glass so they require special shipping terms.
	 */


 	/**
 	 * Product Group
 	 * 	Avaiable Values:
 	 *  	EXP = Express
	 *		DOM = Domestic
 	 */
	'ProductGroup' => 'EXP',


	/**
	 * Product Type
	 * Available Values:
	 * 		OND = only for Product Group DOM
	 *		PDX = Priority Document Express
	 *		PPX = Priority Parcel Express
	 *		PLX = Priority Letter Express
	 *		DDX = Deferred Document Express
	 *		DPX = Deferred Parcel Express
	 *		GDX = Ground Document Express
	 *		GPX = Ground Parcel Express
	 *		GPX = Ground Parcel Express
	 *		EPX = Economy Parcel Express
	 *	For more information naviagte to Appendix-A (Page: 51) in
	 *  https://www.aramex.com/docs/default-source/resourses/resourcesdata/shipping-services-api-manual.pdf
	 */
	'ProductType' => 'PPX',


	/**
	 * Payment Method
	 * Available Values:
	 * 		P = Prepaid
	 *		C = Collect
	 *		3 = Third Party
	 * 	For more information naviagte to Appendix-B (Page: 52) in
	 *  https://www.aramex.com/docs/default-source/resourses/resourcesdata/shipping-services-api-manual.pdf
	 */
	'Payment' => 'P',


	/**
	 * Payment Options
	 * Available Values:
	 * 		For PaymentType = C
	 *			ASCC = Needs Shipper Account Number to be filled.
	 *			ARCC = Needs Consignee Account Number to be filled.
	 *		For PaymentType = P (it's nullable here)
	 *			CASH
	 *			ACCT (Stands for Account)
	 *			PPST (Stands for Prepaid Stock)
	 *			CRDT (Stands for Credit)
	 *
	 *  Please note that no one on earth know any details about the above
	 *  Even though for more information navigate to 4.7 Shipment Details (Page: 42) in
	 *  https://www.aramex.com/docs/default-source/resourses/resourcesdata/shipping-services-api-manual.pdf
	 */

	'PaymentOptions' => null,

	/**
	 *	Service Code (Additional Services for the shipment)
	 *  Separate by comma when selecting multiple services
	 *  Available Values: (nullable)
	 * 		CODS = Cash on Delivery
	 * 		FIRST = First Delivery
	 * 		FRDM = Free Domicile
	 * 		HFPU = Hold for pick up
	 * 		NOON = Noon Delivery
	 * 		SIG = Signature Required
	 *  For more information navigate to Appendix-C (Page: 52) in
	 *  https://www.aramex.com/docs/default-source/resourses/resourcesdata/shipping-services-api-manual.pdf
	 */
	'Services' => null,


	/**
	 *	Default Currency Code
 	 *	if your project supports more than currency code, so you should send CurrencyCode parameter when shipment Creation (if needed)
	 *	but you can set the default currency code so you can just not pass it when you only support one currency or other purposes.
	 */
	'CurrencyCode' => 'USD',


	/**
	 *	 Label Information
	 *	 Available Values:
	 *      ReportID   => 9201, 9729 (9729 use it when COD to extract readable reports, 9201 with COD will not be accepted)
	 *		ReportType => “URL” to get report hosted on URL as PDF
	 *					  “RPT” to get a streamed file
	 */
	'LabelInfo' => [
		'ReportID' 		=> 9201,
		'ReportType'	=> 'URL',
	]
];
