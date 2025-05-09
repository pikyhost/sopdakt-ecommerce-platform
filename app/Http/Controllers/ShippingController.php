<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\{LandingPage, Order, Region, ShippingType};
use App\Http\Requests\Shipping\CalculateShippingRequest;

class ShippingController extends Controller
{
    public function handleWebhook(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('JT Express Webhook Received', ['payload' => $data]);

            // Extract relevant fields (adjust based on actual payload structure)
            $trackingNumber = $data['billCode'] ?? $data['txlogisticId'] ?? null;
            $newStatus = $data['status'] ?? $data['deliveryStatus'] ?? null;

            if (!$trackingNumber || !$newStatus) {
                Log::error('JT Express Webhook: Missing tracking number or status', ['payload' => $data]);
                return response()->json(['message' => 'Invalid webhook data'], 400);
            }

            // Find the order by tracking number
            $order = Order::where('tracking_number', $trackingNumber)->first();

            if (!$order) {
                Log::error('JT Express Webhook: Order not found', ['tracking_number' => $trackingNumber]);
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Normalize webhook data to match API response structure
            $normalizedData = [
                'txlogisticId' => $data['txlogisticId'] ?? $trackingNumber,
                'billCode' => $data['billCode'] ?? $trackingNumber,
                'sortingCode' => $data['sortingCode'] ?? '',
                'createOrderTime' => $data['createOrderTime'] ?? now()->toDateTimeString(),
                'lastCenterName' => $data['lastCenterName'] ?? '',
                'deliveryStatus' => $newStatus,
                'raw' => $data, // Store original payload for debugging
            ];

            // Update order with new status and response
            $order->update([
                'shipping_status' => $newStatus,
                'shipping_response' => json_encode($normalizedData),
            ]);

            // Map J&T status to OrderStatus enum
            $mappedStatus = $this->mapJtExpressStatusToOrderStatus($newStatus);
            if ($mappedStatus) {
                $order->status = $mappedStatus;
                $order->save();
            }

            Log::info('JT Express Webhook: Order updated successfully', [
                'tracking_number' => $trackingNumber,
                'new_status' => $newStatus,
            ]);

            return response()->json(['message' => 'Webhook processed successfully']);
        } catch (\Exception $e) {
            Log::error('JT Express Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $data ?? null,
            ]);
            return response()->json(['message' => 'Error processing webhook'], 500);
        }
    }

    private function mapJtExpressStatusToOrderStatus(string $jtStatus): ?string
    {
        $statusMap = [
            'created' => OrderStatus::Pending->value,
            'picked_up' => OrderStatus::Preparing->value,
            'in_transit' => OrderStatus::Shipping->value,
            'delivered' => OrderStatus::Completed->value,
            'cancelled' => OrderStatus::Cancelled->value,
            'returned' => OrderStatus::Refund->value,
            'delayed' => OrderStatus::Delayed->value,
        ];

        return $statusMap[strtolower($jtStatus)] ?? null;
    }

    public function calculateShipping(CalculateShippingRequest $request)
    {
        try {
            $region = Region::find($request->region_id);
            $landingPage = LandingPage::find($request->landing_page_id);
            $shippingType = ShippingType::find($request->shipping_type_id);

            if (!$region || !$landingPage || !$shippingType) {
                return response()->json(['message' => 'Invalid region, landing page, or shipping type'], 400);
            }

            $shippingCost = $landingPage->shippingCost($region, $shippingType);

            return response()->json([
                'shipping_cost' => $shippingCost,
                'message' => 'Shipping cost calculated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Calculate Shipping Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'An error occurred while calculating shipping cost.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
