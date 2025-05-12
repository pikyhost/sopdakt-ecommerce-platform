<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\BostaService;
use Illuminate\Console\Command;

class PollBostaStatuses extends Command
{
    protected $signature = 'bosta:poll-statuses';
    protected $description = 'Poll Bosta for delivery status updates';

    public function handle()
    {
        $orders = Order::whereNotNull('bosta_delivery_id')
            ->where('status', '!=', \App\Enums\OrderStatus::Completed)
            ->get();

        $bostaService = new BostaService();

        foreach ($orders as $order) {
            try {
                $bostaStatus = $bostaService->getDeliveryStatus($order->bosta_delivery_id);

                if ($bostaStatus) {
                    $newStatus = $bostaService->mapBostaStatusToOrderStatus($bostaStatus);

                    // Only update if changed
                    if ($order->status !== $newStatus) {
                        $order->status = $newStatus;
                        $order->save();

                        $this->info("Order #{$order->id} updated to status: {$newStatus->value}");
                    }
                } else {
                    $this->warn("No status returned for Order #{$order->id}");
                }
            } catch (\Exception $e) {
                $this->error("Failed to fetch status for Order #{$order->id}: " . $e->getMessage());
            }
        }
    }

}
