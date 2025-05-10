<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class AramexService
{
    public function createShipment(Order $order): array
    {
        $contact = $order->user?? $order->contact;

        $data = [
            'ClientInfo' => [
                'UserName' => env('ARAMEX_USERNAME'),
                'Password' => env('ARAMEX_PASSWORD'),
                'AccountNumber' => env('ARAMEX_ACCOUNT_NUMBER'),
                'AccountPin' => env('ARAMEX_ACCOUNT_PIN'),
                'AccountEntity' => env('ARAMEX_ACCOUNT_ENTITY'),
                'AccountCountryCode' => env('ARAMEX_COUNTRY_CODE'),
            ],
            'LabelInfo' => [
                'ReportID' => 9729,
                'ReportType' => 'URL'
            ],
            'Shipments' => [[
                'Reference1' => $order->id,
                'Shipper' => [
                    'Name' => 'Your Company Name',
                    'EmailAddress' => 'shipper@example.com',
                    'PhoneNumber1' => '0799999999',
                    'Line1' => 'Your address line 1',
                    'City' => 'Amman',
                    'CountryCode' => env('ARAMEX_COUNTRY_CODE'),
                ],
                'Consignee' => [
                    'Name' => $contact->name ?? 'Customer',
                    'EmailAddress' => $contact->email ?? 'customer@example.com',
                    'PhoneNumber1' => $contact->phone,
                    'Line1' => 'Tanta test',
                    'City' => $order->city->name,
                    'CountryCode' => $order->country->code,
                ],
                'Details' => [
                    'ActualWeight' => ['Value' => 1, 'Unit' => 'KG'],
                    'ProductGroup' => 'DOM',
                    'ProductType' => 'OND',
                    'PaymentType' => 'P',
                    'NumberOfPieces' => 1,
                    'DescriptionOfGoods' => 'Order #' . $order->id,
                    'GoodsOriginCountry' => env('ARAMEX_COUNTRY_CODE'),
                ],
            ]],
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post('https://ws.aramex.net/shippingapi.v2/CreateShipments', $data);

        $result = $response->json();

        if ($response->failed() || ($result['HasErrors'] ?? false)) {
            return [
                'success' => false,
                'message' => $result['Notifications'][0]['Message'] ?? 'Unknown error',
            ];
        }

        $shipment = $result['Shipments'][0];

        return [
            'success' => true,
            'shipment_id' => $shipment['ID'],
            'tracking_number' => $shipment['ID'],
            'tracking_url' => "https://www.aramex.com/track/shipments?ShipmentNumber={$shipment['ID']}",
            'raw' => $result,
        ];
    }
}
