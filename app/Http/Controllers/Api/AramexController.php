<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AramexService;
use Illuminate\Http\Request;

class AramexController extends Controller
{
    protected $aramexService;

    public function __construct(AramexService $aramexService)
    {
        $this->aramexService = $aramexService;
    }

    /**
     * Create ARAMEX shipment for an order
     */
    public function createShipment(Order $order)
    {
        try {
            // Prepare shipment data
            $shipmentData = [
                'reference' => 'ORDER-' . $order->id,
                'weight' => $order->items->sum('weight') ?: 1, // Default to 1kg if no weight
                'description' => 'Order #' . $order->id,
                'product_group' => 'DOM', // Domestic
                'product_type' => 'OND', // On Demand
                'payment_type' => 'P', // Prepaid
            ];

            // Shipper data (your store info)
            $shipperData = [
                'Reference1' => 'STORE-' . $order->id,
                'Reference2' => '',
                'AccountNumber' => config('services.aramex.account_number'),
                'PartyAddress' => [
                    'Line1' => '123 Main St',
                    'Line2' => '',
                    'Line3' => '',
                    'City' => 'Cairo',
                    'StateOrProvinceCode' => 'C',
                    'PostCode' => '11511',
                    'CountryCode' => 'EG',
                ],
                'Contact' => [
                    'Department' => '',
                    'PersonName' => 'Store Manager',
                    'Title' => '',
                    'CompanyName' => 'Your Store Name',
                    'PhoneNumber1' => '+201000000000',
                    'PhoneNumber1Ext' => '',
                    'PhoneNumber2' => '',
                    'PhoneNumber2Ext' => '',
                    'FaxNumber' => '',
                    'CellPhone' => '+201000000000',
                    'EmailAddress' => 'store@example.com',
                    'Type' => ''
                ],
            ];

            // Consignee data (customer info)
            $consigneeData = [
                'Reference1' => 'CUSTOMER-' . $order->user_id,
                'Reference2' => '',
                'AccountNumber' => '',
                'PartyAddress' => [
                    'Line1' => $order->contact->address_line_1,
                    'Line2' => $order->contact->address_line_2,
                    'Line3' => '',
                    'City' => $order->city->name,
                    'StateOrProvinceCode' => $order->governorate->code ?? 'C',
                    'PostCode' => $order->contact->postal_code ?? '00000',
                    'CountryCode' => $order->country->code,
                ],
                'Contact' => [
                    'Department' => '',
                    'PersonName' => $order->contact->full_name,
                    'Title' => '',
                    'CompanyName' => '',
                    'PhoneNumber1' => $order->contact->phone_number,
                    'PhoneNumber1Ext' => '',
                    'PhoneNumber2' => '',
                    'PhoneNumber2Ext' => '',
                    'FaxNumber' => '',
                    'CellPhone' => $order->contact->phone_number,
                    'EmailAddress' => $order->contact->email,
                    'Type' => ''
                ],
            ];

            // Prepare items
            $items = [];
            foreach ($order->items as $item) {
                $items[] = [
                    'PackageType' => 'Box',
                    'Quantity' => $item->quantity,
                    'Weight' => [
                        'Value' => $item->weight ?: 0.5,
                        'Unit' => 'kg'
                    ],
                    'Comments' => $item->product->name,
                    'Reference' => 'ITEM-' . $item->id,
                ];
            }

            // Create shipment
            $response = $this->aramexService->createShipment($shipmentData, $shipperData, $consigneeData, $items);

            // Update order with ARAMEX details
            $order->update([
                'aramex_shipment_id' => $response->Shipments->ProcessedShipment->ID,
                'aramex_tracking_number' => $response->Shipments->ProcessedShipment->ShipmentNumber,
                'aramex_tracking_url' => 'https://www.aramex.com/track/results?ShipmentNumber=' . $response->Shipments->ProcessedShipment->ShipmentNumber,
                'aramex_response' => json_encode($response),
                'status' => 'shipping',
            ]);

            return response()->json([
                'success' => true,
                'tracking_number' => $response->Shipments->ProcessedShipment->ShipmentNumber,
                'tracking_url' => 'https://www.aramex.com/track/results?ShipmentNumber=' . $response->Shipments->ProcessedShipment->ShipmentNumber,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ARAMEX shipment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Track ARAMEX shipment
     */
    public function trackShipment(Order $order)
    {
        try {
            if (!$order->aramex_shipment_id) {
                throw new \Exception('No ARAMEX shipment ID found for this order');
            }

            $response = $this->aramexService->trackShipment($order->aramex_shipment_id);

            // Update order status based on tracking
            $this->updateOrderStatusFromTracking($order, $response);

            return response()->json([
                'success' => true,
                'tracking_info' => $response,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track ARAMEX shipment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook for ARAMEX to update shipment status
     */
    public function webhook(Request $request)
    {
        try {
            $data = $request->all();
            \Log::info('ARAMEX Webhook Received:', $data);

            // Validate webhook data
            if (empty($data['ShipmentNumber']) || empty($data['Status'])) {
                throw new \Exception('Invalid webhook data');
            }

            // Find order by tracking number
            $order = Order::where('aramex_tracking_number', $data['ShipmentNumber'])->first();
            if (!$order) {
                throw new \Exception('Order not found for tracking number: ' . $data['ShipmentNumber']);
            }

            // Update order status
            $this->updateOrderStatus($order, $data['Status']);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('ARAMEX Webhook Error:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update order status based on ARAMEX tracking
     */
    protected function updateOrderStatusFromTracking(Order $order, $trackingData)
    {
        $latestUpdate = collect($trackingData->TrackingResults->KeyValueOfstringArrayOfTrackingResultmFAkxlpY->Value->TrackingResult)
            ->sortByDesc('UpdateDateTime')
            ->first();

        if ($latestUpdate) {
            $this->updateOrderStatus($order, $latestUpdate->UpdateDescription);
        }
    }

    /**
     * Map ARAMEX status to order status
     */
    protected function updateOrderStatus(Order $order, $aramexStatus)
    {
        $statusMap = [
            'Shipment Picked Up' => 'shipping',
            'Shipment Delivered' => 'completed',
            'Shipment Out for Delivery' => 'shipping',
            'Shipment In Transit' => 'shipping',
            'Shipment Returned' => 'refund',
            'Shipment Cancelled' => 'cancelled',
            'Shipment Delayed' => 'delayed',
        ];

        $newStatus = $statusMap[$aramexStatus] ?? $order->status;

        $order->update([
            'status' => $newStatus,
            'aramex_response' => json_encode(['latest_status' => $aramexStatus, 'updated_at' => now()]),
        ]);
    }
}
