<?php

// app/Services/AramexService.php
namespace App\Services;

use Octw\Aramex\Aramex;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class AramexService
{
    protected $aramex;

    public function __construct()
    {
        $this->aramex = new Aramex();
    }

    public function createShipment(Order $order): array
    {
        try {
            $contact = $order->contact;
            $city = $order->city;

            $shipmentData = [
                'shipper' => [
                    'name' => config('aramex.shipper.name'),
                    'email' => config('aramex.shipper.email'),
                    'phone' => config('aramex.shipper.phone'),
                    'cell_phone' => config('aramex.shipper.phone'),
                    'country_code' => config('aramex.shipper.country_code'),
                    'city' => config('aramex.shipper.city'),
                    'zip_code' => config('aramex.shipper.zip_code', ''),
                    'line1' => config('aramex.shipper.address'),
                    'line2' => config('aramex.shipper.line2', 'Unknown Address'),
                    'line3' => '',
                ],
                'consignee' => [
                    'name' => $contact->name ?? 'Customer',
                    'email' => $contact->email ?? 'unknown@example.com',
                    'phone' => $contact->phone ?? '0000000000',
                    'cell_phone' => $contact->phone ?? '0000000000',
                    'country_code' => $order->country->code ?? 'SA',
                    'city' => $city->name ?? 'Unknown City',
                    'zip_code' => $contact->zip_code ?? '',
                    'line1' => $contact->address ?? 'Unknown Address',
                    'line2' => $contact->line2 ?? 'Unknown Address',
                    'line3' => '',
                ],
                'shipping_date_time' => time(),
                'due_date' => time() + 86400, // 1 day later
                'comments' => 'Order #' . $order->id,
                'pickup_location' => config('aramex.shipper.address'), // Set to shipper's address
                'weight' => 1, // Adjust based on actual weight
                'number_of_pieces' => 1,
                'description' => 'Order #' . $order->id,
                'reference' => 'REF' . $order->id,
                'shipper_reference' => 'SHIPPER' . $order->id,
                'consignee_reference' => 'CONSIGNEE' . $order->id,
                'services' => 'CODS',
                'cash_on_delivery_amount' => $order->total / 100, // Assuming total is in cents
                'product_group' => config('aramex.product_group', 'EXP'),
                'product_type' => config('aramex.product_type', 'PPX'),
                'payment_type' => config('aramex.payment_type', 'P'),
            ];

            // Log request for debugging
            Log::info('Aramex Shipment Data', $shipmentData);

            $response = $this->aramex->createShipment($shipmentData);

            // Log response for debugging
            Log::info('Aramex API Response', (array) $response);

            if (empty($response->error) && isset($response->Shipments->ProcessedShipment->ID)) {
                $shipment = $response->Shipments->ProcessedShipment;
                return [
                    'success' => true,
                    'shipment_id' => $shipment->ID,
                    'tracking_number' => $shipment->ID,
                    'tracking_url' => $shipment->ShipmentLabel->LabelURL ?? 'https://www.aramex.com/track/shipments',
                    'response' => json_encode($response),
                ];
            } else {
                $errorMessage = 'Unknown error';
                if (!empty($response->error) && !empty($response->errors)) {
                    $errorMessage = collect($response->errors)->pluck('Message')->implode('; ');
                } elseif (isset($response->error)) {
                    $errorMessage = $response->error;
                }
                return [
                    'success' => false,
                    'error' => $errorMessage,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Aramex shipment creation failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function trackShipment(string $shipmentId): array
    {
        try {
            $response = $this->aramex->trackShipments([$shipmentId]);

            // Log response for debugging
            Log::info('Aramex Tracking Response', (array) $response);

            if (empty($response->error) && isset($response->TrackingResults)) {
                $trackingResult = collect($response->TrackingResults->KeyValueOfstringArrayOfTrackingResultmFAkxlpY)
                    ->firstWhere('Key', $shipmentId);

                if ($trackingResult && !empty($trackingResult->Value->TrackingResult)) {
                    $latestUpdate = collect($trackingResult->Value->TrackingResult)->first();
                    return [
                        'success' => true,
                        'status' => $latestUpdate->UpdateDescription ?? 'Unknown',
                        'last_update' => $latestUpdate->UpdateDateTime ?? now(),
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'No tracking results found',
                    ];
                }
            } else {
                $errorMessage = 'Unknown error';
                if (!empty($response->error) && !empty($response->errors)) {
                    $errorMessage = collect($response->errors)->pluck('Message')->implode('; ');
                } elseif (isset($response->error)) {
                    $errorMessage = $response->error;
                }
                return [
                    'success' => false,
                    'error' => $errorMessage,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Aramex tracking failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
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
