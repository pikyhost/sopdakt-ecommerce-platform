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
        Log::debug('JT Express Webhook: Endpoint hit', ['headers' => $request->headers->all()]);

        try {
            // Extract and decode bizContent
            $bizContent = $request->input('bizContent');
            if (!$bizContent) {
                Log::error('JT Express Webhook: Missing bizContent');
                return response()->json(['message' => 'Missing bizContent'], 400);
            }

            $data = json_decode($bizContent, true);
            if (!$data) {
                Log::error('JT Express Webhook: Failed to decode bizContent', ['bizContent' => $bizContent]);
                return response()->json(['message' => 'Invalid bizContent'], 400);
            }

            Log::info('JT Express Webhook Received', ['payload' => $data]);

            // Extract tracking number
            $trackingNumber = $data['billCode'] ?? $data['txlogisticId'] ?? null;
            if (!$trackingNumber) {
                Log::error('JT Express Webhook: Missing tracking number', ['payload' => $data]);
                return response()->json(['message' => 'Missing tracking number'], 400);
            }

            // Extract new status
            $newStatus = null;
            if (isset($data['details']) && is_array($data['details']) && count($data['details']) > 0) {
                $detail = $data['details'][0];
                $newStatus = $detail['scanType'] ?? null;
            } else {
                $newStatus = $data['scanType'] ?? null;
            }

            if (!$newStatus) {
                Log::error('JT Express Webhook: Missing status', ['payload' => $data]);
                return response()->json(['message' => 'Missing status'], 400);
            }

            // Find the order
            $order = Order::where('tracking_number', $trackingNumber)->first();
            if (!$order) {
                Log::error('JT Express Webhook: Order not found', ['tracking_number' => $trackingNumber]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Order not found.'
                    ], 200);
            }
            

            // Update order
            $order->update([
                'shipping_status' => $newStatus,
                'shipping_response' => json_encode($data),
            ]);

            // Map to OrderStatus
            $mappedStatus = $this->mapJtExpressStatusToOrderStatus($newStatus);
            if ($mappedStatus) {
                $order->status = $mappedStatus;
                if (!$order->save()) {
                    Log::error('JT Express Webhook: Failed to save order status', [
                        'order_id' => $order->id,
                        'mapped_status' => $mappedStatus,
                    ]);
                }
            } else {
                Log::warning('JT Express Webhook: Unmapped status', [
                    'tracking_number' => $trackingNumber,
                    'status' => $newStatus,
                ]);
            }

            Log::info('JT Express Webhook: Order updated successfully', [
                'order_id' => $order->id,
                'tracking_number' => $trackingNumber,
                'new_status' => $newStatus,
            ]);

            return response()->json(['message' => 'Webhook processed successfully']);
        } catch (\Exception $e) {
            Log::error('JT Express Webhook Error', [
                'error' => $e->getMessage(),
                'payload' => $data ?? null,
            ]);
            return response()->json(['message' => 'Error processing webhook'], 500);
        }
    }

    private function mapJtExpressStatusToOrderStatus(string $jtStatus): ?string
    {
        $englishMap = [
            'pickup' => OrderStatus::Shipping->value,
            'picked up' => OrderStatus::Shipping->value,
            'in transit' => OrderStatus::Shipping->value,
            'out for delivery' => OrderStatus::Shipping->value,
            'delivered' => OrderStatus::Completed->value,
            'cancelled' => OrderStatus::Cancelled->value,
            'returned' => OrderStatus::Refund->value,
            'delayed' => OrderStatus::Delayed->value,
        ];

        $chineseMap = [
            '已调派业务员' => OrderStatus::Preparing->value,
            '已入仓' => OrderStatus::Shipping->value,
            '已取消' => OrderStatus::Cancelled->value,
        ];

        foreach ($englishMap as $key => $value) {
            if (stripos(strtolower($jtStatus), strtolower($key)) !== false) {
                return $value;
            }
        }

        if (isset($chineseMap[$jtStatus])) {
            return $chineseMap[$jtStatus];
        }

        Log::warning('JT Express Webhook: Unmapped status', [
            'status' => $jtStatus,
        ]);
        return null;
    }
}
