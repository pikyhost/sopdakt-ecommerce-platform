<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AramexService
{
    public function createShipment(array $shipmentData, array $shipperData, array $consigneeData, array $items): array
    {
        $url = config('services.aramex.url') . '/CreateShipments';

        // تحويل التاريخ إلى صيغة JSON الخاصة بـ Aramex
        $shippingDateTime = Carbon::now()->getTimestamp() * 1000;
        $dueDate = Carbon::now()->addDay()->getTimestamp() * 1000;

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
                    'ShippingDateTime' => "\/Date($shippingDateTime)\/",
                    'DueDate' => "\/Date($dueDate)\/",
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

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            $responseData = $response->json();
            $responseBody = $response->body();

            Log::info('Aramex API Response', [
                'status' => $response->status(),
                'data' => $responseData ?? [],
                'raw_body' => $responseBody,
            ]);

            if (!$response->successful()) {
                Log::error('Aramex API failed', [
                    'status' => $response->status(),
                    'body' => $responseBody,
                ]);
                throw new \Exception('Aramex API request failed with status ' . $response->status() . ': ' . $responseBody);
            }

            if (isset($responseData['HasErrors']) && $responseData['HasErrors']) {
                $errorMessage = $responseData['Notifications'][0]['Message'] ?? 'Unknown error';
                Log::error('Aramex API error', ['response' => $responseData]);
                throw new \Exception($errorMessage);
            }

            if (!isset($responseData['Shipments'][0]['ProcessedShipment']['ID'])) {
                Log::error('Aramex API invalid response', ['response' => $responseData]);
                throw new \Exception('Invalid response format from Aramex API');
            }

            return $responseData;
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
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            $responseData = $response->json();
            $responseBody = $response->body();

            Log::info('Aramex Tracking Response', [
                'status' => $response->status(),
                'data' => $responseData ?? [],
                'raw_body' => $responseBody,
            ]);

            if (!$response->successful()) {
                Log::error('Aramex tracking failed', [
                    'status' => $response->status(),
                    'body' => $responseBody,
                ]);
                throw new \Exception('Tracking request failed with status ' . $response->status());
            }

            if (isset($responseData['HasErrors']) && $responseData['HasErrors']) {
                $errorMessage = $responseData['Notifications'][0]['Message'] ?? 'Tracking failed';
                throw new \Exception($errorMessage);
            }

            if (isset($responseData['TrackingResults'][0]['Value'][0])) {
                $trackingResult = $responseData['TrackingResults'][0]['Value'][0];
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
