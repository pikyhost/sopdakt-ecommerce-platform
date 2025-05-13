<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AramexService
{
    public function createShipment(array $shipmentData, array $shipperData, array $consigneeData, array $items): array
    {
        $url = config('services.aramex.url') . '/CreateShipments';

        $payload = [
            'ClientInfo' => [
                'UserName' => config('services.aramex.username'),
                'Password' => config('services.aramex.password'),
                'Version' => 'v1',
                'AccountNumber' => config('services.aramex.account_number'),
                'AccountPin' => config('services.aramex.account_pin'),
                'AccountEntity' => config('services.aramex.account_entity'),
                'AccountCountryCode' => config('services.aramex.account_country_code'),
            ],
            'LabelInfo' => [
                'ReportID' => 9729,
                'ReportType' => 'URL',
            ],
            'Shipments' => [
                [
                    'Reference1' => $shipmentData['reference'],
                    'Reference2' => '',
                    'Shipper' => $shipperData,
                    'Consignee' => $consigneeData,
                    'ShippingDateTime' => now()->toDateTimeString(),
                    'DueDate' => now()->addDays(1)->toDateTimeString(),
                    'Details' => [
                        'Dimensions' => [
                            'Length' => 0,
                            'Width' => 0,
                            'Height' => 0,
                            'Unit' => 'cm',
                        ],
                        'ActualWeight' => [
                            'Value' => $shipmentData['weight'],
                            'Unit' => 'kg',
                        ],
                        'ChargeableWeight' => [
                            'Value' => $shipmentData['weight'],
                            'Unit' => 'kg',
                        ],
                        'DescriptionOfGoods' => $shipmentData['description'],
                        'GoodsOriginCountry' => 'EG',
                        'NumberOfPieces' => count($items),
                        'ProductGroup' => $shipmentData['product_group'],
                        'ProductType' => $shipmentData['product_type'],
                        'PaymentType' => $shipmentData['payment_type'],
                        'PaymentOptions' => '',
                        'Services' => 'CODS',
                        'CashOnDeliveryAmount' => [
                            'Value' => $shipmentData['cash_on_delivery_amount'] ?? 0,
                            'CurrencyCode' => 'USD',
                        ],
                        'Items' => $items,
                    ],
                ],
            ],
        ];

        try {
            Log::info('Aramex Shipment Request', $payload);

            $response = Http::timeout(20)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            $responseData = $response->json();

            Log::info('Aramex API Response', $responseData ?? []);

            if ($response->successful() && isset($responseData['HasErrors']) && !$responseData['HasErrors']) {
                return $responseData;
            }

            $errorMessage = $responseData['Notifications'][0]['Message'] ?? 'Aramex API returned an error';
            Log::error('Aramex API error', ['response' => $responseData]);
            throw new \Exception($errorMessage);
        } catch (\Throwable $e) {
            Log::error('Aramex shipment failed', [
                'error' => $e->getMessage(),
                'request' => $payload,
            ]);
            throw new \Exception('Failed to create shipment: ' . $e->getMessage());
        }
    }

    public function trackShipment(string $shipmentNumber): array
    {
        $url = config('services.aramex.url') . '/TrackShipments';

        $payload = [
            'ClientInfo' => [
                'UserName' => config('services.aramex.username'),
                'Password' => config('services.aramex.password'),
                'Version' => 'v1',
                'AccountNumber' => config('services.aramex.account_number'),
                'AccountPin' => config('services.aramex.account_pin'),
                'AccountEntity' => config('services.aramex.account_entity'),
                'AccountCountryCode' => config('services.aramex.account_country_code'),
            ],
            'Shipments' => [$shipmentNumber],
        ];

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            $responseData = $response->json();

            Log::info('Aramex Tracking Response', $responseData ?? []);

            if ($response->successful() && isset($responseData['TrackingResults'])) {
                $trackingResult = $responseData['TrackingResults'][0]['Value'][0] ?? null;
                if ($trackingResult) {
                    return [
                        'success' => true,
                        'status' => $trackingResult['UpdateDescription'] ?? 'Unknown',
                        'last_update' => $trackingResult['UpdateDateTime'] ?? now(),
                    ];
                }
                return [
                    'success' => false,
                    'error' => 'No tracking results found',
                ];
            }

            $errorMessage = $responseData['Notifications'][0]['Message'] ?? 'Tracking failed';
            throw new \Exception($errorMessage);
        } catch (\Throwable $e) {
            Log::error('Aramex tracking failed', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to track shipment: ' . $e->getMessage());
        }
    }

    public function mapAramexStatusToOrderStatus(string $aramexStatus): ?string
    {
        $statusMap = [
            'Shipped' => 'shipping',
            'In Transit' => 'shipping',
            'Out for Delivery' => 'shipping',
            'Delivered' => 'completed',
            'Delivery Attempted' => 'delayed',
            'Cancelled' => 'cancelled',
            'Returned' => 'refund',
        ];

        return $statusMap[$aramexStatus] ?? null;
    }
}
