<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    /**
     * List all orders for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * Response:
     * {
     *   "success": true,
     *   "orders": [ { order_object }, ... ]
     * }
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['user', 'contact', 'paymentMethod', 'coupon', 'shippingType', 'country', 'governorate', 'city'])
            ->latest()
            ->get()
            ->map(fn ($order) => $this->transformOrder($order));

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    /**
     * Track a specific order by its tracking number.
     *
     * @param string $trackingNumber
     * @return \Illuminate\Http\JsonResponse
     *
     * Response (Success):
     * {
     *   "success": true,
     *   "status": "shipping",
     *   "shipping_status": "out_for_delivery",
     *   "shipping_response": { ... }
     * }
     *
     * Response (Not Found):
     * {
     *   "success": false,
     *   "message": "Order not found."
     * }
     */
    public function track($trackingNumber)
    {
        $order = Order::where('tracking_number', $trackingNumber)->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'shipping_status' => $order->shipping_status,
            'shipping_response' => $order->shipping_response,
        ]);
    }

    /**
     * Update the order (only if it belongs to the authenticated user).
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     *
     * Request:
     * {
     *   "notes": "optional string",
     *   "status": "pending|preparing|shipping|delayed|refund|cancelled|completed"
     * }
     *
     * Response:
     * {
     *   "success": true,
     *   "message": "Order updated successfully.",
     *   "order": { order_object }
     * }
     */
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
            'order' => $this->transformOrder($order->load([
                'user', 'contact', 'paymentMethod', 'coupon', 'shippingType', 'country', 'governorate', 'city'
            ])),
        ]);
    }

    /**
     * Store a new order after verifying WhatsApp code if payment method is COD.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|integer',
            'phone' => 'required|string',
        ]);

        $COD_PAYMENT_METHOD_ID = 1;

        if ($request->payment_method_id == $COD_PAYMENT_METHOD_ID) {
            $isVerified = Cache::get('whatsapp_verified_' . $request->phone);

            if (!$isVerified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your WhatsApp number before placing the order.',
                ], 403);
            }

            Cache::forget('whatsapp_verified_' . $request->phone);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully (placeholder).',
        ]);
    }


    /**
     * Delete the order (only if it belongs to the authenticated user).
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     *
     * Response:
     * {
     *   "success": true,
     *   "message": "Order deleted successfully."
     * }
     */
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

    /**
     * Transform an Order model into a consistent array format.
     *
     * @param \App\Models\Order $order
     * @return array
     */
    protected function transformOrder(Order $order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'user_name' => $order->user->name ?? null,

            'payment_method_id' => $order->payment_method_id,
            'payment_method_name' => $order->paymentMethod->name ?? null,

            'coupon_id' => $order->coupon_id,

            'shipping_cost' => $order->shipping_cost,
            'tax_percentage' => $order->tax_percentage,
            'tax_amount' => $order->tax_amount,
            'subtotal' => $order->subtotal,
            'total' => $order->total,

            'shipping_type_id' => $order->shipping_type_id,
            'shipping_type_name' => $order->shippingType->name ?? null,

            'country_id' => $order->country_id,
            'country_name' => $order->country->name ?? null,

            'governorate_id' => $order->governorate_id,
            'governorate_name' => $order->governorate->name ?? null,

            'city_id' => $order->city_id,
            'city_name' => $order->city?->name ?? null,

            'status' => $order->status,
            'notes' => $order->notes,
            'tracking_number' => $order->tracking_number,
            'shipping_status' => $order->shipping_status,
            'shipping_response' => $order->shipping_response,
            'checkout_token' => $order->checkout_token,

            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
        ];
    }
}
