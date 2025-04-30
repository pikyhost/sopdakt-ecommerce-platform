<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // List the authenticated user's orders
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['contact', 'paymentMethod', 'coupon', 'shippingType', 'country', 'governorate', 'city'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    // Track an order by tracking number
    public function track($trackingNumber)
    {
        $order = Order::where('tracking_number', $trackingNumber)->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'shipping_status' => $order->shipping_status,
            'shipping_response' => $order->shipping_response,
        ]);
    }

    // Update an order (only if belongs to user)
    public function update(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'status' => 'in:pending,preparing,shipping,delayed,refund,cancelled,completed',
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully.',
            'order' => $order,
        ]);
    }

    // Delete an order (only if belongs to user)
    public function destroy(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
            ], 403);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.',
        ]);
    }
}
