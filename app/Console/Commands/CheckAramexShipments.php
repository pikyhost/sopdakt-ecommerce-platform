<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\AramexService;
use Illuminate\Console\Command;

class CheckAramexShipments extends Command
{
    protected $signature = 'aramex:check-shipments';
    protected $description = 'Check status of ARAMEX shipments and update orders';

    public function handle(AramexService $aramexService)
    {
        $orders = Order::whereNotNull('aramex_shipment_id')
            ->whereNotIn('status', ['completed', 'cancelled', 'refund'])
            ->get();

        foreach ($orders as $order) {
            try {
                $this->info("Checking status for order #{$order->id}");
                $aramexService->trackShipment($order->aramex_shipment_id);
                $this->info("Status updated for order #{$order->id}");
            } catch (\Exception $e) {
                $this->error("Failed to check status for order #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info("Completed checking {$orders->count()} shipments");
    }
}
