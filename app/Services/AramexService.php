<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AramexService
{
    protected $apiUrl;
    protected $clientInfo;

    public function __construct()
    {
        $this->apiUrl = config('aramex.api_url');
        $this->clientInfo = config('aramex.client_info');
    }

    public function createShipment(Order $order): array
    {
        $contact = $order->user ?? $order->contact;
        $items = $order->items; // Assuming you have an items relationship

        $payload = [
            'ClientInfo' => $this->clientInfo,
            'LabelInfo' => [
                'ReportID' => 9201,
                'ReportType' => 'URL',
            ],
            'Shipments' => [
                [
                    'Reference1' => $order->id,
                    'Shipper' => [
                        'Reference1' => 'SHP-' . $order->id,
                        'AccountNumber' => $this->clientInfo['AccountNumber'],
                        'PartyAddress' => [
                            'Line1' => 'Your Company Address', // Replace with your company address
                            'City' => 'Cairo',
                            'CountryCode' => 'EG',
                        ],
                        'Contact' => [
                            'PersonName' => 'Your Company Name',
                            'CompanyName' => 'Your Company',
                            'PhoneNumber1' => '1234567890',
                            'EmailAddress' => 'info@yourcompany.com',
                        ],
                    ],
                    'Consignee' => [
                        'Reference1' => 'CNS-' . $order->id,
                        'PartyAddress' => [
                            'Line1' => $contact->address ?? 'N/A',
                            'City' => $order->city->name ?? 'Cairo',
                            'CountryCode' => $order->country->code ?? 'EG',
                        ],
                        'Contact' => [
                            'PersonName' => $contact->name ?? 'Customer',
                            'CompanyName' => $contact->company ?? 'N/A',
                            'PhoneNumber1' => $contact->phone ?? 'N/A',
                            'EmailAddress' => $contact->email ?? 'N/A',
                        ],
                    ],
                    'ShippingDateTime' => now()->toDateTimeString(),
                    'DueDate' => now()->addDays(3)->toDateTimeString(),
                    'Details' => [
                        'ActualWeight' => [
                            'Unit' => 'KG',
                            'Value' => $items->sum('weight') ?: 0.5, // Sum item weights or default
                        ],
                        'DescriptionOfGoods' => 'Order #' . $order->id,
                        'GoodsOriginCountry' => 'EG',
                        'NumberOfPieces' => $items->count() ?: 1,
                        'ProductGroup' => 'EXP',
                        'ProductType' => 'PDX',
                        'PaymentType' => 'P',
                        'Services' => '',
                        'Items' => $items->map(fn($item) => [
                            'PackageType' => 'Box',
                            'Quantity' => $item->quantity,
                            'Weight' => [
                                'Unit' => 'KG',
                                'Value' => $item->weight ?: 0.5,
                            ],
                            'Comments' => $item->name,
                        ])->toArray(),
                    ],
                ],
            ],
            'Transaction' => [
                'Reference1' => 'ORDER-' . $order->id,
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($this->apiUrl . 'CreateShipments', $payload);

        $data = $response->json();

        if ($response->successful() && !isset($data['HasErrors'])) {
            $shipment = $data['Shipments'][0];
            $order->update([
                'aramex_shipment_id' => $shipment['ID'],
                'aramex_tracking_number' => $shipment['ID'],
                'aramex_tracking_url' => $shipment['LabelInfo']['URL'] ?? null,
                'aramex_response' => json_encode($data),
                'status' => OrderStatus::Shipping->value,
            ]);

            return [
                'success' => true,
                'message' => 'Shipment created successfully',
                'data' => $data,
            ];
        }

        Log::error('Aramex shipment creation failed', [
            'order_id' => $order->id,
            'response' => $data,
        ]);

        return [
            'success' => false,
            'message' => $data['Notifications'][0]['Message'] ?? 'Failed to create shipment',
        ];
    }

    public function trackShipment(Order $order): array
    {
        $payload = [
            'ClientInfo' => $this->clientInfo,
            'Transaction' => [
                'Reference1' => 'ORDER-' . $order->id,
            ],
            'Shipments' => [$order->aramex_tracking_number],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://ws.dev.aramex.net/api/Track/Shipments', $payload);

        $data = $response->json();

        if ($response->successful() && !isset($data['HasErrors'])) {
            $trackingInfo = $data['TrackingResults'][0]['TrackingUpdates'] ?? [];
            $latestUpdate = end($trackingInfo);

            // Map Aramex status to your OrderStatus enum
            $statusMap = [
                'Shipped' => OrderStatus::Shipping,
                'InTransit' => OrderStatus::Shipping,
                'OutForDelivery' => OrderStatus::Shipping,
                'Delivered' => OrderStatus::Completed,
                'Cancelled' => OrderStatus::Cancelled,
                'Delayed' => OrderStatus::Delayed,
            ];

            $newStatus = $statusMap[$latestUpdate['UpdateDescription']] ?? OrderStatus::Shipping;

            $order->update([
                'status' => $newStatus->value,
                'aramex_response' => json_encode($data),
            ]);

            return [
                'success' => true,
                'message' => 'Tracking updated successfully',
                'data' => $data,
            ];
        }

        Log::error('Aramex tracking failed', [
            'order_id' => $order->id,
            'response' => $data,
        ]);

        return [
            'success' => false,
            'message' => $data['Notifications'][0]['Message'] ?? 'Failed to track shipment',
        ];
    }
}
