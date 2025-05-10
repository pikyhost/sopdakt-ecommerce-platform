<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AramexWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $trackingNumber = $request->input('TrackingNumber');
        $statusCode = $request->input('UpdateCode');

        $order = Order::where('aramex_tracking_number', $trackingNumber)->first();
        if (! $order) {
            return response()->noContent();
        }

        $newStatus = match ($statusCode) {
            'SH001' => 'shipping',
            'DL001' => 'completed',
            'CX001' => 'cancelled',
            'SH002' => 'delayed',
            default => $order->status,
        };

        $order->update([
            'status' => $newStatus,
        ]);

        return response()->noContent();
    }
}
