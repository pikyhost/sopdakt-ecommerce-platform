<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BostaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BostaWebhookController extends Controller
{
    /**
     * Handle Bosta webhook POST requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        // Verify authorization
        $secret = config('services.bosta.webhook_secret');
        $authHeader = $request->header('Authorization');

        if (!$secret || $authHeader !== "Bearer $secret") {
            Log::error('Unauthorized Bosta webhook request', [
                'auth_header' => $authHeader,
                'ip' => $request->ip(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        $deliveryId = $data['_id'] ?? null;
        $bostaStateCode = $data['state'] ?? null;

        if ($deliveryId && $bostaStateCode !== null) {
            $order = Order::where('bosta_delivery_id', $deliveryId)->first();
            if ($order) {
                $bostaService = new BostaService();
                $orderStatus = $bostaService->mapBostaStateCodeToOrderStatus($bostaStateCode);

                $updateData = ['status' => $orderStatus];
                if (isset($data['exceptionReason'])) {
                    $updateData['exception_reason'] = $data['exceptionReason'];
                }
                if (isset($data['cod']) && $bostaStateCode === 45) {
                    $updateData['cod_collected'] = $data['cod'];
                }

                $order->update($updateData);

                Log::info('Bosta webhook processed', [
                    'order_id' => $order->id,
                    'bosta_delivery_id' => $deliveryId,
                    'bosta_state_code' => $bostaStateCode,
                    'new_status' => $orderStatus->value,
                    'exception_reason' => $data['exceptionReason'] ?? null,
                    'cod_collected' => $data['cod'] ?? null,
                ]);
            } else {
                Log::warning('Order not found for Bosta delivery ID', [
                    'delivery_id' => $deliveryId,
                    'payload' => $data,
                ]);
            }
        } else {
            Log::error('Invalid Bosta webhook payload', [
                'payload' => $data,
                'ip' => $request->ip(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
