<?php

// app/Jobs/ProcessAramexWebhook.php
namespace App\Jobs;

use App\Models\Order;
use App\Services\AramexService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAramexWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $webhookData;

    public function __construct(array $webhookData)
    {
        $this->webhookData = $webhookData;
    }

    public function handle(AramexService $aramexService)
    {
        try {
            $shipmentId = $this->webhookData['ShipmentID'] ?? null;
            if (!$shipmentId) {
                Log::error('Invalid Aramex webhook data: Missing ShipmentID');
                return;
            }

            $order = Order::where('aramex_shipment_id', $shipmentId)->first();
            if (!$order) {
                Log::warning('No order found for Aramex shipment ID: ' . $shipmentId);
                return;
            }

            $tracking = $aramexService->trackShipment($shipmentId);
            if (!$tracking['success']) {
                Log::error('Failed to track Aramex shipment: ' . $tracking['error']);
                return;
            }

            $aramexStatus = $tracking['status'];
            $newStatus = $aramexService->mapAramexStatusToOrderStatus($aramexStatus);

            if ($newStatus && $order->status !== $newStatus) {
                $order->update(['status' => $newStatus]);
                Log::info("Order #{$order->id} status updated to {$newStatus} via Aramex webhook");
            }
        } catch (\Exception $e) {
            Log::error('Aramex webhook processing failed: ' . $e->getMessage());
        }
    }
}
