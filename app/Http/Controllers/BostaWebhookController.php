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
        $data = $request->all();
        $deliveryId = $data['deliveryId'];
        $bostaStatus = $data['state'];

        $order = Order::where('bosta_delivery_id', $deliveryId)->first();

        if ($order) {
            $orderStatus = (new BostaService())->mapBostaStatusToOrderStatus($bostaStatus);
            $order->update(['status' => $orderStatus]);
        }

        return response()->json(['status' => 'success']);
    }
}
