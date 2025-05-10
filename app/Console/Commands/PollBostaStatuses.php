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
        $orders = Order::whereNotNull('bosta_delivery_id')->where('status', '!=', 'Completed')->get();
        $bostaService = new BostaService();

        foreach ($orders as $order) {
            // TODO: Call Bosta tracking API to get status
            // Update order status using $bostaService->mapBostaStateCodeToOrderStatus
        }
    }
}
