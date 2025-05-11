<?php

namespace App\Services;

namespace App\Services;

use SoapClient;

class AramexService
{
    protected $shippingWsdl;
    protected $trackingWsdl;

    public function __construct()
    {
        $this->shippingWsdl = config('services.aramex.testing')
            ? 'http://ws.dev.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc'
            : 'https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc';
        $this->trackingWsdl = config('services.aramex.testing')
            ? 'https://ws.dev.aramex.net/shippingapi/tracking/service_1_0.svc'
            : 'https://ws.aramex.net/shippingapi/tracking/service_1_0.svc';
    }

    public function createShipment($shipmentData)
    {
        $client = new SoapClient($this->shippingWsdl);
        $request = $this->buildCreateShipmentRequest($shipmentData);
        $response = $client->CreateShipments($request);
        return $this->parseCreateShipmentResponse($response);
    }

    public function getTrackingStatus($trackingNumbers)
    {
        $client = new SoapClient($this->trackingWsdl);
        $request = $this->buildTrackingRequest($trackingNumbers);
        $response = $client->ShipmentTracking($request);
        return $this->parseTrackingResponse($response);
    }

    protected function buildCreateShipmentRequest($shipmentData)
    {
        return [
            'ClientInfo' => [
                'UserName' => config('services.aramex.username'),
                'Password' => config('services.aramex.password'),
                'Version' => config('services.aramex.version'),
                'AccountNumber' => config('services.aramex.account_number'),
                'AccountPin' => config('services.aramex.account_pin'),
                'AccountEntity' => config('services.aramex.account_entity'),
                'AccountCountryCode' => config('services.aramex.account_country_code'),
            ],
            'Transaction' => [
                'Reference1' => $shipmentData['order_id'],
            ],
            'Shipments' => [
                [
                    'Shipper' => [
                        'Name' => 'Your Company Name',
                        'PhoneNumber' => '1234567890',
                        'Address' => [
                            'Line1' => 'Your Address Line 1',
                            'City' => 'Your City',
                            'CountryCode' => 'EG',
                        ],
                    ],
                    'Consignee' => [
                        'Name' => $shipmentData['consignee_name'],
                        'PhoneNumber' => $shipmentData['consignee_phone'],
                        'Address' => [
                            'Line1' => $shipmentData['consignee_address'],
                            'City' => $shipmentData['consignee_city'],
                            'CountryCode' => $shipmentData['consignee_country_code'],
                        ],
                    ],
                    'ShippingDateTime' => now()->toDateTimeString(),
                    'Details' => [
                        'Dimensions' => [
                            'Length' => 10,
                            'Width' => 10,
                            'Height' => 10,
                            'Unit' => 'CM',
                        ],
                        'ActualWeight' => [
                            'Value' => $shipmentData['weight'],
                            'Unit' => 'KG',
                        ],
                        'ProductGroup' => 'EXP',
                        'ProductType' => 'PDX',
                        'PaymentType' => 'P',
                        'NumberOfPieces' => 1,
                        'DescriptionOfGoods' => 'Order Items',
                    ],
                ],
            ],
        ];
    }

    protected function parseCreateShipmentResponse($response)
    {
        if (isset($response->HasErrors) && $response->HasErrors) {
            throw new \Exception($response->Notifications->Notification->Message);
        }
        $shipment = $response->Shipments[0];
        return [
            'shipment_id' => $shipment->ID,
            'tracking_number' => $shipment->ShipmentNumber,
            'tracking_url' => 'https://www.aramex.com/track/shipments?ShipmentNumber=' . $shipment->ShipmentNumber,
            'response' => json_encode($response),
        ];
    }

    protected function buildTrackingRequest($trackingNumbers)
    {
        return [
            'ClientInfo' => [
                'UserName' => config('services.aramex.username'),
                'Password' => config('services.aramex.password'),
                'Version' => config('services.aramex.version'),
                'AccountNumber' => config('services.aramex.account_number'),
                'AccountPin' => config('services.aramex.account_pin'),
                'AccountEntity' => config('services.aramex.account_entity'),
                'AccountCountryCode' => config('services.aramex.account_country_code'),
            ],
            'Shipments' => $trackingNumbers,
            'GetLastTrackingUpdateOnly' => true,
        ];
    }

    protected function parseTrackingResponse($response)
    {
        $statuses = [];
        if (isset($response->HasErrors) && $response->HasErrors) {
            throw new \Exception($response->Notifications->Notification->Message);
        }
        foreach ($response->TrackingResults->TrackingResult as $result) {
            $statuses[$result->WaybillNumber] = $result->UpdateDescription;
        }
        return $statuses;
    }
}
