<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NormalizeOrderShippingResponse extends Command
{
    protected $signature = 'order:normalize-shipping-response';
    protected $description = 'Normalize shipping_response JSON for existing orders';

    public function handle()
    {
        try {
            $orders = Order::whereNotNull('shipping_response')->get();
            $updated = 0;

            foreach ($orders as $order) {
                $data = json_decode($order->shipping_response, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                    $normalized = [
                        'txlogisticId' => $data['txlogisticId'] ?? $order->tracking_number,
                        'billCode' => $data['billCode'] ?? $order->tracking_number,
                        'sortingCode' => $data['sortingCode'] ?? '',
                        'createOrderTime' => $data['createOrderTime'] ?? now()->toDateTimeString(),
                        'lastCenterName' => $data['lastCenterName'] ?? '',
                        'deliveryStatus' => $data['deliveryStatus'] ?? $order->shipping_status,
                        'raw' => $data,
                    ];

                    $order->update(['shipping_response' => json_encode($normalized)]);
                    $updated++;
                } else {
                    // Handle invalid JSON
                    $normalized = [
                        'txlogisticId' => $order->tracking_number,
                        'billCode' => $order->tracking_number,
                        'sortingCode' => '',
                        'createOrderTime' => now()->toDateTimeString(),
                        'lastCenterName' => '',
                        'deliveryStatus' => $order->shipping_status,
                        'raw' => $data ?? [],
                    ];

                    $order->update(['shipping_response' => json_encode($normalized)]);
                    $updated++;
                }
            }

            $this->info("Successfully normalized $updated orders.");
        } catch (\Exception $e) {
            Log::error('Normalize Shipping Response Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
