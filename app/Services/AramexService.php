<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AramexService
{
    protected string $apiUrl;
    protected string $username;
    protected string $password;
    protected string $accountNumber;
    protected string $accountPin;
    protected string $accountEntity;
    protected string $accountCountryCode;

    public function __construct()
    {
        $this->apiUrl = config('services.aramex.url', 'https://ws.aramex.net/shippingapi.v2/CreateShipments');
        $this->username = config('services.aramex.username');
        if (!$this->username) {
            throw new \Exception("Aramex username is not configured.");
        }

        $this->password = config('services.aramex.password');
        $this->accountNumber = config('services.aramex.account_number');
        $this->accountPin = config('services.aramex.account_pin');
        $this->accountEntity = config('services.aramex.account_entity');
        $this->accountCountryCode = config('services.aramex.account_country_code');
    }

    public function createShipment(Order $order): array
    {
        try {
            $contact = $order->user ?? $order->contact;

            $data = [
                'ClientInfo' => [
                    'UserName'      => $this->username,
                    'Password'      => $this->password,
                    'Version'       => 'v1.0',
                    'AccountNumber' => $this->accountNumber,
                    'AccountPin'    => $this->accountPin,
                    'AccountEntity' => $this->accountEntity,
                    'AccountCountryCode' => $this->accountCountryCode,
                ],
                'LabelInfo' => [
                    'ReportID'   => 9729,
                    'ReportType' => 'URL',
                ],
                'Shipments' => [
                    [
                        'Reference1'  => 'Order-' . $order->id,
                        'Shipper'     => [
                            'Name'         => 'Your Company',
                            'CellPhone'    => '0000000000',
                            'Email'        => 'support@yourcompany.com',
                            'Line1'        => 'Street Address',
                            'City'         => 'Your City',
                            'CountryCode'  => $this->accountCountryCode,
                        ],
                        'Consignee' => [
                            'Name'        => $contact->name,
                            'CellPhone'   => $contact->phone,
                            'Email'       => $contact->email,
                            'Line1'       => $contact->addresses()->first()->address,
                            'City'        => $order->city?->name ?? 'City',
                            'CountryCode' => $order->country?->code ?? 'EG',
                        ],
                        'ShippingDateTime' => now()->toIso8601String(),
                        'DueDate'          => now()->addDays(3)->toIso8601String(),
                        'Comments'         => 'Handle with care',
                        'PickupLocation'   => 'Reception',
                        'Details' => [
                            'Dimensions' => [
                                'Length' => 10,
                                'Width'  => 10,
                                'Height' => 10,
                                'Unit'   => 'cm'
                            ],
                            'ActualWeight' => [
                                'Value' => 1,
                                'Unit'  => 'KG'
                            ],
                            'ProductGroup' => 'EXP',
                            'ProductType'  => 'PPX',
                            'PaymentType'  => 'P',
                            'PaymentOptions' => '',
                            'Services' => '',
                            'NumberOfPieces' => 1,
                            'DescriptionOfGoods' => 'E-commerce Order',
                            'GoodsOriginCountry' => $this->accountCountryCode,
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiUrl, $data);

            $result = $response->json();

            if ($response->failed() || ($result['HasErrors'] ?? false)) {
                Log::error('Aramex shipment failed', [
                    'order_id' => $order->id,
                    'response' => $result,
                    'request' => $data,
                ]);

                return [
                    'success' => false,
                    'message' => $result['Notifications'][0]['Message']
                        ?? $result['Notifications'][0]['Description']
                            ?? 'Unknown error',
                ];
            }

            $shipment = $result['Shipments'][0];

            // Save Aramex tracking info
            $order->update([
                'aramex_shipment_id'    => $shipment['ID'] ?? null,
                'aramex_tracking_number'=> $shipment['ShipmentID'] ?? null,
                'aramex_tracking_url'   => $shipment['ShipmentLabel'] ?? null,
                'status'                => 'shipping',
                'aramex_response'       => json_encode($result),
            ]);

            return [
                'success' => true,
                'tracking_number' => $shipment['ShipmentID'],
                'tracking_url'    => $shipment['ShipmentLabel'],
            ];
        } catch (\Throwable $e) {
            Log::error('Aramex shipment exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Unexpected error: ' . $e->getMessage(),
            ];
        }
    }
}
