<?php

namespace App\Services;

use SoapClient;
use Exception;
use Illuminate\Support\Facades\Log;

class AramexService
{
    protected $clientInfo;
    protected $testMode;
    protected $urls;
    protected $soapOptions;

    public function __construct()
    {
        $this->testMode = config('services.aramex.test_mode');
        $this->urls = config('services.aramex.' . ($this->testMode ? 'test_urls' : 'live_urls'));

        $this->clientInfo = [
            'UserName' => config('services.aramex.username'),
            'Password' => config('services.aramex.password'),
            'Version' => config('services.aramex.version'),
            'AccountNumber' => config('services.aramex.account_number'),
            'AccountPin' => config('services.aramex.account_pin'),
            'AccountEntity' => config('services.aramex.account_entity'),
            'AccountCountryCode' => config('services.aramex.account_country_code'),
        ];

        $this->soapOptions = [
            'stream_context' => stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]),
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => 1,
            'exceptions' => true
        ];
    }

    protected function getSoapClient($service)
    {
        try {
            // Note: The WSDL URL is case-sensitive based on ARAMEX docs
            $wsdlUrl = $this->urls[$service] . '?wsdl';

            // For debugging - log the exact URL being used
            Log::debug('ARAMEX WSDL URL', ['url' => $wsdlUrl]);

            return new SoapClient($wsdlUrl, $this->soapOptions);
        } catch (Exception $e) {
            Log::error('SOAP Client Creation Failed', [
                'error' => $e->getMessage(),
                'service' => $service,
                'url' => $wsdlUrl ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception("Failed to initialize ARAMEX SOAP client: " . $e->getMessage());
        }
    }

    public function createShipment(array $shipmentData, array $shipperData, array $consigneeData, array $items)
    {
        try {
            $client = $this->getSoapClient('shipping');

            $params = [
                'ClientInfo' => $this->clientInfo,
                'Transaction' => [
                    'Reference1' => $shipmentData['reference'],
                    'Reference2' => '',
                    'Reference3' => '',
                    'Reference4' => '',
                    'Reference5' => '',
                ],
                'Shipments' => [
                    [
                        'Reference1' => $shipmentData['reference'],
                        'Reference2' => '',
                        'Reference3' => '',
                        'Shipper' => $shipperData,
                        'Consignee' => $consigneeData,
                        'ThirdParty' => null,
                        'ShippingDateTime' => time(),
                        'DueDate' => time() + (7 * 24 * 60 * 60),
                        'Comments' => $shipmentData['comments'] ?? '',
                        'PickupLocation' => 'Reception',
                        'Operations' => '',
                        'Details' => [
                            'Dimensions' => [
                                'Length' => $shipmentData['length'] ?? 10,
                                'Width' => $shipmentData['width'] ?? 10,
                                'Height' => $shipmentData['height'] ?? 10,
                                'Unit' => 'cm',
                            ],
                            'ActualWeight' => [
                                'Value' => $shipmentData['weight'],
                                'Unit' => 'kg',
                            ],
                            'ChargeableWeight' => null,
                            'DescriptionOfGoods' => $shipmentData['description'] ?? 'General Goods',
                            'GoodsOriginCountry' => $shipperData['CountryCode'],
                            'NumberOfPieces' => count($items),
                            'ProductGroup' => $shipmentData['product_group'] ?? 'DOM',
                            'ProductType' => $shipmentData['product_type'] ?? 'OND',
                            'PaymentType' => $shipmentData['payment_type'] ?? 'P',
                            'PaymentOptions' => $shipmentData['payment_options'] ?? '',
                            'CustomsValueAmount' => [
                                'Value' => $shipmentData['customs_value'] ?? 0,
                                'CurrencyCode' => $shipmentData['currency_code'] ?? 'USD',
                            ],
                            'CashOnDeliveryAmount' => null,
                            'InsuranceAmount' => null,
                            'CashAdditionalAmount' => null,
                            'CashAdditionalAmountDescription' => null,
                            'CollectAmount' => null,
                            'Services' => '',
                            'Items' => $items,
                        ],
                    ],
                ],
            ];

            Log::debug('ARAMEX CreateShipment Request', ['params' => $params]);

            $response = $client->CreateShipment($params);

            if ($response->HasErrors) {
                Log::error('ARAMEX CreateShipment Error', [
                    'errors' => $response->Notifications,
                    'request' => $params
                ]);
                throw new Exception('ARAMEX Error: ' . json_encode($response->Notifications));
            }

            Log::debug('ARAMEX CreateShipment Response', ['response' => $response]);

            return $response;

        } catch (Exception $e) {
            Log::error('ARAMEX CreateShipment Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Failed to create ARAMEX shipment: ' . $e->getMessage());
        }
    }

    /**
     * Track a shipment
     */
    public function trackShipment($shipmentId)
    {
        try {
            $client = new SoapClient($this->urls['tracking'] . '?wsdl');

            $params = [
                'ClientInfo' => $this->clientInfo,
                'Transaction' => [
                    'Reference1' => $shipmentId,
                ],
                'Shipments' => [
                    $shipmentId
                ],
            ];

            $response = $client->TrackShipments($params);

            if ($response->HasErrors) {
                Log::error('ARAMEX TrackShipment Error', [
                    'errors' => $response->Notifications,
                    'request' => $params
                ]);
                throw new Exception('ARAMEX Error: ' . json_encode($response->Notifications));
            }

            return $response;

        } catch (Exception $e) {
            Log::error('ARAMEX TrackShipment Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get shipping rates
     */
    public function calculateRate(array $origin, array $destination, array $shipmentDetails)
    {
        try {
            $client = new SoapClient($this->urls['shipping'] . '?wsdl');

            $params = [
                'ClientInfo' => $this->clientInfo,
                'Transaction' => [
                    'Reference1' => 'RateCalc-' . time(),
                ],
                'OriginAddress' => $origin,
                'DestinationAddress' => $destination,
                'ShipmentDetails' => $shipmentDetails,
            ];

            $response = $client->CalculateRate($params);

            if ($response->HasErrors) {
                Log::error('ARAMEX CalculateRate Error', [
                    'errors' => $response->Notifications,
                    'request' => $params
                ]);
                throw new Exception('ARAMEX Error: ' . json_encode($response->Notifications));
            }

            return $response;

        } catch (Exception $e) {
            Log::error('ARAMEX CalculateRate Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Schedule a pickup
     */
    public function schedulePickup(array $pickupDetails, array $pickupAddress, array $shipments)
    {
        try {
            $client = new SoapClient($this->urls['shipping'] . '?wsdl');

            $params = [
                'ClientInfo' => $this->clientInfo,
                'Transaction' => [
                    'Reference1' => 'Pickup-' . time(),
                ],
                'Pickup' => $pickupDetails,
                'PickupAddress' => $pickupAddress,
                'Shipments' => $shipments,
            ];

            $response = $client->CreatePickup($params);

            if ($response->HasErrors) {
                Log::error('ARAMEX SchedulePickup Error', [
                    'errors' => $response->Notifications,
                    'request' => $params
                ]);
                throw new Exception('ARAMEX Error: ' . json_encode($response->Notifications));
            }

            return $response;

        } catch (Exception $e) {
            Log::error('ARAMEX SchedulePickup Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
