<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BostaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BostaWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verify webhook secret (if configured in Bosta)
        $secret = config('services.bosta.webhook_secret');
        if ($secret && $request->header('X-Bosta-Signature') !== hash_hmac('sha256', $request->getContent(), $secret)) {
            Log::warning('Bosta webhook signature verification failed.');
            return response()->json(['status' => 'unauthorized'], 401);
        }

        $payload = $request->all();
        $deliveryId = $payload['deliveryId'] ?? null;
        $state = $payload['state'] ?? null;

        if (!$deliveryId || !$state) {
            Log::warning('Bosta webhook missing required fields.', ['payload' => $payload]);
            return response()->json(['status' => 'invalid payload'], 400);
        }

        $order = Order::where('bosta_delivery_id', $deliveryId)->first();
        if (!$order) {
            Log::warning('Order not found for Bosta delivery ID.', ['delivery_id' => $deliveryId]);
            return response()->json(['status' => 'order not found'], 404);
        }

        $bostaService = new BostaService();
        $newStatus = $bostaService->mapBostaStatusToOrderStatus($state);
        $order->update(['status' => $newStatus]);

        Log::info('Order status updated via Bosta webhook.', [
            'order_id' => $order->id,
            'bosta_delivery_id' => $deliveryId,
            'new_status' => $newStatus->value,
        ]);

        return response()->json(['status' => 'success']);
    }
}
