<?php

namespace App\Services;

use App\Enums\OrderStatus;
use Filament\Notifications\Notification;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class BostaService
{
    protected $client;
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.bosta.api_key');
        $this->apiUrl = config('services.bosta.api_url');
        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Create a delivery in Bosta.
     *
     * @param \App\Models\Order $order
     * @return array|null
     */
    public function createDelivery($order)
    {
        $contact = $order->user ?? $order->contact;
        $city = $order->city;

        if (!$contact || !$contact->address) {
            Log::error('Cannot create Bosta delivery: missing contact address', ['order_id' => $order->id]);
            Notification::make()
                ->title('Cannot send order to Bosta: Missing contact address')
                ->danger()
                ->send();
            return null;
        }

        if (!$city || !$city->bosta_code) {
            Log::error('Cannot create Bosta delivery: missing city code', ['order_id' => $order->id]);
            return null;
        }

        $payload = [
            'type' => 10, // Cash on Delivery (adjust as needed)
            'specs' => [
                'packageDetails' => [
                    'itemsCount' => 1,
                    'description' => 'Order #' . $order->id,
                ],
            ],
            'notes' => $order->notes ?? '',
            'cod' => $order->total, // Cash on Delivery amount
            'dropOffAddress' => [
                'city' => $city->bosta_code, // Use Bosta city code
                'firstLine' => $contact->address ?? 'test',
                'district' => $order->governorate->name,
            ],
            'receiver' => [
                'firstName' => $contact->first_name ?? 'N/A',
                'lastName' => $contact->last_name ?? 'N/A',
                'phone' => $contact->phone,
                'email' => $contact->email ?? null,
            ],
        ];

        try {
            $response = $this->client->post('/api/v2/deliveries', [
                'json' => $payload,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Bosta create delivery failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
            return null;
        }
    }

    /**
     * Map Bosta status to local OrderStatus enum.
     *
     * @param string $bostaStatus
     * @return OrderStatus
     */
    public function mapBostaStatusToOrderStatus($bostaStatus)
    {
        // Bosta statuses: https://docs.bosta.co/docs/api-docs/delivery-tracking
        return match ($bostaStatus) {
            'Delivered' => OrderStatus::Completed,
            'Delivery Failed', 'Returned' => OrderStatus::Refund,
            'Cancelled' => OrderStatus::Cancelled,
            'Out for Delivery' => OrderStatus::Shipping,
            'Delayed' => OrderStatus::Delayed,
            default => OrderStatus::Shipping, // Fallback for statuses like 'Picked', 'In Transit'
        };
    }
}
