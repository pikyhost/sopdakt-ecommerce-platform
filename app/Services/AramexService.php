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
        $items = $order->items;

        // Format DateTime as /Date(<milliseconds>+<timezone>)/
        // Use raw string to avoid double-escaping backslashes
        $shippingDateTime = "/Date(" . (now()->timestamp * 1000) . "+0200)/";
        $dueDate = "/Date(" . (now()->addDays(3)->timestamp * 1000) . "+0200)/";

        $payload = [
            'ClientInfo' => $this->clientInfo,
            'LabelInfo' => [
                'ReportID' => 9201,
                'ReportType' => 'URL',
            ],
            'Shipments' => [
                [
                    'Reference1' => (string)$order->id,
                    'Reference2' => '',
                    'Reference3' => '',
                    'Shipper' => [
                        'Reference1' => 'SHP-' . $order->id,
                        'Reference2' => '',
                        'AccountNumber' => $this->clientInfo['AccountNumber'],
                        'PartyAddress' => [
                            'Line1' => config('aramex.shipper.address', 'Your Company Address'),
                            'Line2' => '',
                            'Line3' => '',
                            'City' => 'Cairo',
                            'StateOrProvinceCode' => '',
                            'PostCode' => '',
                            'CountryCode' => 'EG',
                            'Longitude' => 0,
                            'Latitude' => 0,
                            'BuildingNumber' => null,
                            'BuildingName' => null,
                            'Floor' => null,
                            'Apartment' => null,
                            'POBox' => null,
                            'Description' => null,
                        ],
                        'Contact' => [
                            'Department' => '',
                            'PersonName' => config('aramex.shipper.name', 'Your Company Name'),
                            'Title' => '',
                            'CompanyName' => config('aramex.shipper.company', 'Your Company'),
                            'PhoneNumber1' => config('aramex.shipper.phone', '1234567890'),
                            'PhoneNumber1Ext' => '',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => config('aramex.shipper.phone', '1234567890'),
                            'EmailAddress' => config('aramex.shipper.email', 'info@yourcompany.com'),
                            'Type' => '',
                        ],
                    ],
                    'Consignee' => [
                        'Reference1' => 'CNS-' . $order->id,
                        'Reference2' => '',
                        'AccountNumber' => '',
                        'PartyAddress' => [
                            'Line1' => $contact->addresses()->where('is_primary', true)->first()->address ?? 'N/A',
                            'Line2' => '',
                            'Line3' => '',
                            'City' => $order->city->name ?? 'Cairo',
                            'StateOrProvinceCode' => '',
                            'PostCode' => '',
                            'CountryCode' => $order->country->code ?? 'EG',
                            'Longitude' => 0,
                            'Latitude' => 0,
                            'BuildingNumber' => '',
                            'BuildingName' => '',
                            'Floor' => '',
                            'Apartment' => '',
                            'POBox' => null,
                            'Description' => '',
                        ],
                        'Contact' => [
                            'Department' => '',
                            'PersonName' => $contact->name ?? 'Customer',
                            'Title' => '',
                            'CompanyName' => $contact->company ?? 'N/A',
                            'PhoneNumber1' => preg_replace('/\s+/', '', $contact->phone ?? 'N/A'),
                            'PhoneNumber1Ext' => '',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => preg_replace('/\s+/', '', $contact->phone ?? 'N/A'),
                            'EmailAddress' => $contact->email ?? 'N/A',
                            'Type' => '',
                        ],
                    ],
                    'ThirdParty' => [
                        'Reference1' => '',
                        'Reference2' => '',
                        'AccountNumber' => '',
                        'PartyAddress' => [
                            'Line1' => '',
                            'Line2' => '',
                            'Line3' => '',
                            'City' => '',
                            'StateOrProvinceCode' => '',
                            'PostCode' => '',
                            'CountryCode' => '',
                            'Longitude' => 0,
                            'Latitude' => 0,
                            'BuildingNumber' => null,
                            'BuildingName' => null,
                            'Floor' => null,
                            'Apartment' => null,
                            'POBox' => null,
                            'Description' => null,
                        ],
                        'Contact' => [
                            'Department' => '',
                            'PersonName' => '',
                            'Title' => '',
                            'CompanyName' => '',
                            'PhoneNumber1' => '',
                            'PhoneNumber1Ext' => '',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => '',
                            'EmailAddress' => '',
                            'Type' => '',
                        ],
                    ],
                    'ShippingDateTime' => $shippingDateTime,
                    'DueDate' => $dueDate,
                    'Comments' => '',
                    'PickupLocation' => '',
                    'OperationsInstructions' => '',
                    'AccountingInstrcutions' => '',
                    'Details' => [
                        'Dimensions' => null,
                        'ActualWeight' => [
                            'Unit' => 'KG',
                            'Value' => $items->sum('weight') ?: 0.5,
                        ],
                        'ChargeableWeight' => null,
                        'DescriptionOfGoods' => 'Order #' . $order->id,
                        'GoodsOriginCountry' => 'EG',
                        'NumberOfPieces' => $items->count() ?: 1,
                        'ProductGroup' => 'DOM',
                        'ProductType' => 'CDS',
                        'PaymentType' => 'P',
                        'PaymentOptions' => '',
                        'CustomsValueAmount' => null,
                        'CashOnDeliveryAmount' => null,
                        'InsuranceAmount' => null,
                        'CashAdditionalAmount' => null,
                        'CashAdditionalAmountDescription' => '',
                        'CollectAmount' => null,
                        'Services' => '',
                        'Items' => $items->map(fn($item) => [
                            'PackageType' => 'Box',
                            'Quantity' => $item->quantity,
                            'Weight' => [
                                'Unit' => 'KG',
                                'Value' => $item->weight ?: 0.5,
                            ],
                            'Comments' => $item->name ?? 'N/A',
                            'Reference' => 'ITEM-' . $item->id,
                        ])->toArray(),
                    ],
                    'Attachments' => [],
                    'ForeignHAWB' => '',
                    'TransportType' => 0,
                    'PickupGUID' => '',
                    'Number' => null,
                    'ScheduledDelivery' => null,
                ],
            ],
            'Transaction' => [
                'Reference1' => 'ORDER-' . $order->id,
                'Reference2' => '',
                'Reference3' => '',
                'Reference4' => '',
                'Reference5' => '',
            ],
        ];

        return $this->sendShipmentRequest($order, $payload);
    }
    
    public function testStaticShipment(Order $order): array
    {
        $payload = [
            'ClientInfo' => $this->clientInfo,
            'LabelInfo' => [
                'ReportID' => 9201,
                'ReportType' => 'URL',
            ],
            'Shipments' => [
                [
                    'Reference1' => 'TEST-001',
                    'Reference2' => '',
                    'Reference3' => '',
                    'Shipper' => [
                        'Reference1' => 'SHP-TEST-001',
                        'Reference2' => '',
                        'AccountNumber' => '20016',
                        'PartyAddress' => [
                            'Line1' => '123 Test Street',
                            'Line2' => 'Building A',
                            'Line3' => 'Floor 2',
                            'City' => 'Cairo',
                            'StateOrProvinceCode' => '',
                            'PostCode' => '12345',
                            'CountryCode' => 'EG',
                            'Longitude' => 0,
                            'Latitude' => 0,
                            'BuildingNumber' => '123',
                            'BuildingName' => 'Test Building',
                            'Floor' => '2',
                            'Apartment' => 'A1',
                            'POBox' => null,
                            'Description' => 'Test Shipper Address',
                        ],
                        'Contact' => [
                            'Department' => 'Shipping',
                            'PersonName' => 'Ahmed Mohamed',
                            'Title' => 'Manager',
                            'CompanyName' => 'Test Company',
                            'PhoneNumber1' => '1234567890',
                            'PhoneNumber1Ext' => '123',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => '1234567890',
                            'EmailAddress' => 'ahmed@testcompany.com',
                            'Type' => 'Business',
                        ],
                    ],
                    'Consignee' => [
                        'Reference1' => 'CNS-TEST-001',
                        'Reference2' => '',
                        'AccountNumber' => '',
                        'PartyAddress' => [
                            'Line1' => '456 Customer Road',
                            'Line2' => 'Near Main Square',
                            'Line3' => 'Apartment B',
                            'City' => 'Alexandria',
                            'StateOrProvinceCode' => '',
                            'PostCode' => '67890',
                            'CountryCode' => 'EG',
                            'Longitude' => 0,
                            'Latitude' => 0,
                            'BuildingNumber' => '456',
                            'BuildingName' => 'Customer Building',
                            'Floor' => '1',
                            'Apartment' => 'B2',
                            'POBox' => null,
                            'Description' => 'Test Customer Address',
                        ],
                        'Contact' => [
                            'Department' => '',
                            'PersonName' => 'Fatima Ali',
                            'Title' => '',
                            'CompanyName' => 'Customer Inc',
                            'PhoneNumber1' => '9876543210',
                            'PhoneNumber1Ext' => '',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => '9876543210',
                            'EmailAddress' => 'fatima@customer.com',
                            'Type' => 'Individual',
                        ],
                    ],
                    'ThirdParty' => [
                        'Reference1' => '',
                        'Reference2' => '',
                        'AccountNumber' => '',
                        'PartyAddress' => [
                            'Line1' => '',
                            'Line2' => '',
                            'Line3' => '',
                            'City' => '',
                            'StateOrProvinceCode' => '',
                            'PostCode' => '',
                            'CountryCode' => '',
                            'Longitude' => 0,
                            'Latitude' => 0,
                            'BuildingNumber' => null,
                            'BuildingName' => null,
                            'Floor' => null,
                            'Apartment' => null,
                            'POBox' => null,
                            'Description' => null,
                        ],
                        'Contact' => [
                            'Department' => '',
                            'PersonName' => '',
                            'Title' => '',
                            'CompanyName' => '',
                            'PhoneNumber1' => '',
                            'PhoneNumber1Ext' => '',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => '',
                            'EmailAddress' => '',
                            'Type' => '',
                        ],
                    ],
                    'ShippingDateTime' => '2025-05-14T12:00:00',
                    'DueDate' => '2025-05-17T12:00:00',
                    'Comments' => 'Test shipment for debugging',
                    'PickupLocation' => 'Main Warehouse',
                    'OperationsInstructions' => 'Handle with care',
                    'AccountingInstrcutions' => 'Bill to shipper',
                    'Details' => [
                        'Dimensions' => [
                            'Length' => 10,
                            'Width' => 10,
                            'Height' => 10,
                            'Unit' => 'CM',
                        ],
                        'ActualWeight' => [
                            'Unit' => 'KG',
                            'Value' => 0.5,
                        ],
                        'ChargeableWeight' => [
                            'Unit' => 'KG',
                            'Value' => 0.5,
                        ],
                        'DescriptionOfGoods' => 'Books and Stationery',
                        'GoodsOriginCountry' => 'EG',
                        'NumberOfPieces' => 1,
                        'ProductGroup' => 'EXP',
                        'ProductType' => 'PDX',
                        'PaymentType' => 'P',
                        'PaymentOptions' => '',
                        'CustomsValueAmount' => [
                            'CurrencyCode' => 'EGP',
                            'Value' => 100,
                        ],
                        'CashOnDeliveryAmount' => null,
                        'InsuranceAmount' => null,
                        'CashAdditionalAmount' => null,
                        'CashAdditionalAmountDescription' => '',
                        'CollectAmount' => null,
                        'Services' => '',
                        'Items' => [
                            [
                                'PackageType' => 'Box',
                                'Quantity' => 2,
                                'Weight' => [
                                    'Unit' => 'KG',
                                    'Value' => 0.25,
                                ],
                                'Comments' => 'Books',
                                'Reference' => 'ITEM-001',
                            ],
                            [
                                'PackageType' => 'Box',
                                'Quantity' => 1,
                                'Weight' => [
                                    'Unit' => 'KG',
                                    'Value' => 0.25,
                                ],
                                'Comments' => 'Stationery',
                                'Reference' => 'ITEM-002',
                            ],
                        ],
                    ],
                    'Attachments' => [],
                    'ForeignHAWB' => '',
                    'TransportType' => 0,
                    'PickupGUID' => '',
                    'Number' => null,
                    'ScheduledDelivery' => null,
                ],
            ],
            'Transaction' => [
                'Reference1' => 'TEST-001',
                'Reference2' => '',
                'Reference3' => '',
                'Reference4' => '',
                'Reference5' => '',
            ],
        ];

        return $this->sendShipmentRequest($order, $payload);
    }

    protected function sendShipmentRequest(Order $order, array $payload): array
    {
        Log::info('Aramex create shipment payload', ['order_id' => $order->id, 'payload' => $payload]);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post($this->apiUrl . 'CreateShipments', $payload);

            Log::info('Aramex create shipment response', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            // التحقق مما إذا كان الرد بتنسيق HTML
            if (str_contains($response->body(), '<html')) {
                Log::error('Aramex returned HTML response', [
                    'order_id' => $order->id,
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'رد غير صالح من Aramex: تم إرجاع HTML بدلاً من JSON',
                ];
            }

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
                    'message' => 'تم إنشاء الشحنة بنجاح',
                    'data' => $data,
                ];
            }

            Log::error('Aramex shipment creation failed', [
                'order_id' => $order->id,
                'response' => $data,
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'message' => $data['Notifications'][0]['Message'] ?? 'فشل إنشاء الشحنة: رد غير صالح من Aramex',
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Aramex connection error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'فشل الاتصال بواجهة برمجة Aramex: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            Log::error('Aramex unexpected error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطأ غير متوقع: ' . $e->getMessage(),
            ];
        }
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

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post('https://ws.aramex.net/api/Track/Shipments', $payload);

            $data = $response->json();

            if ($response->successful() && !isset($data['HasErrors'])) {
                $trackingInfo = $data['TrackingResults'][0]['TrackingUpdates'] ?? [];
                $latestUpdate = end($trackingInfo);

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
                    'message' => 'تم تحديث التتبع بنجاح',
                    'data' => $data,
                ];
            }

            Log::error('Aramex tracking failed', [
                'order_id' => $order->id,
                'response' => $data,
            ]);

            return [
                'success' => false,
                'message' => $data['Notifications'][0]['Message'] ?? 'فشل تتبع الشحنة',
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Aramex tracking connection error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'فشل الاتصال بواجهة تتبع Aramex: ' . $e->getMessage(),
            ];
        }
    }
}
