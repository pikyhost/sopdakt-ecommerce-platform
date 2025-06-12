<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\JtExpressService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncJtExpressOrderStatuses extends Command
{
    protected $signature = 'jt-express:sync-statuses';
    protected $description = 'Sync order statuses from J&T Express API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            // Fetch orders with non-final statuses
            $orders = Order::whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Preparing->value,
                OrderStatus::Shipping->value,
                OrderStatus::Delayed->value,
            ])->whereNotNull('tracking_number')->get();

            if ($orders->isEmpty()) {
                $this->info('No orders to sync.');
                return 0;
            }

            $jtExpressService = app(JtExpressService::class);

            foreach ($orders as $order) {
                try {
                    // Call J&T Express API to get status
                    $statusInfo = $jtExpressService->getOrderStatus([
                        'billCode' => $order->tracking_number,
                    ]);

                    if (!$statusInfo) {
                        Log::warning('JT Express Sync: Invalid status response', [
                            'order_id' => $order->id,
                            'tracking_number' => $order->tracking_number,
                        ]);
                        continue;
                    }

                    Log::debug('JT Express Sync: API Response', [
                        'order_id' => $order->id,
                        'response' => $statusInfo,
                    ]);

                    // Extract new status
                    $newStatus = null;
                    if (isset($statusInfo['details']) && is_array($statusInfo['details']) && count($statusInfo['details']) > 0) {
                        $detail = $statusInfo['details'][0];
                        $newStatus = $detail['scanType'] ?? null;
                    } else {
                        $newStatus = $statusInfo['scanType'] ?? null;
                    }

                    if (!$newStatus) {
                        Log::warning('JT Express Sync: Missing status', [
                            'order_id' => $order->id,
                            'tracking_number' => $order->tracking_number,
                            'response' => $statusInfo,
                        ]);
                        continue;
                    }

                    // Map J&T status to OrderStatus enum
                    $mappedStatus = $this->mapJtExpressStatusToOrderStatus($newStatus);

                    // Update order
                    $order->update([
                        'shipping_status' => $newStatus,
                        'shipping_response' => json_encode($statusInfo),
                        'status' => $mappedStatus ?? $order->status,
                    ]);

                    Log::info('JT Express Sync: Order updated', [
                        'order_id' => $order->id,
                        'tracking_number' => $order->tracking_number,
                        'new_status' => $newStatus,
                        'mapped_status' => $mappedStatus,
                    ]);
                } catch (\Exception $e) {
                    Log::error('JT Express Sync: Failed to sync order', [
                        'order_id' => $order->id,
                        'tracking_number' => $order->tracking_number,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->info('J&T Express order statuses synced successfully.');
            return 0;
        } catch (\Exception $e) {
            Log::error('JT Express Sync: Command failed', ['error' => $e->getMessage()]);
            $this->error('Failed to sync J&T Express order statuses.');
            return 1;
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

        Log::warning('JT Express Sync: Unmapped status', [
            'status' => $jtStatus,
        ]);
        return null;
    }
}
