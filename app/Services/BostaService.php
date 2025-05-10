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

        // Determine the correct address
        if ($contact instanceof \App\Models\User) {
            $primaryAddress = $contact->addresses()->where('is_primary', true)->first();
            $firstLine = $primaryAddress?->address;
        } else {
            $firstLine = $contact->address ?? null;
        }

        if (!$contact || !$firstLine) {
            Log::error('Cannot create Bosta delivery: missing contact address', ['order_id' => $order->id]);
            Notification::make()->title('Cannot send order to Bosta: Missing contact address')->danger()->send();
            return null;
        }

        if (!$city || !$city->bosta_code) {
            Log::error('Cannot create Bosta delivery: missing city code', ['order_id' => $order->id]);
            Notification::make()->title('Cannot send order to Bosta: Missing city code')->danger()->send();
            return null;
        }

        $payload = [
            'type' => 10,
            'businessLocationId' => config('services.bosta.business_location_id'), // Add if provided
            'specs' => [
                'packageDetails' => [
                    'itemsCount' => 1,
                    'description' => 'Order #' . $order->id,
                ],
            ],
            'notes' => $order->notes ?? '',
            'cod' => $order->total,
            'dropOffAddress' => [
                'city' => 'EG-01', // Cairo
                'firstLine' => '123 Test Street',
                'district' => 'Heliopolis',
            ],
            'receiver' => [
                'firstName' => $contact->first_name ?? 'Test',
                'lastName' => $contact->last_name ?? 'User',
                'phone' => $contact->phone ?? '+201234567890',
                'email' => $contact->email ?? 'test@example.com',
            ],
        ];

        Log::debug('Bosta delivery payload', ['order_id' => $order->id, 'payload' => $payload]);

        try {
            $response = $this->client->post('/api/v2/deliveries', [
                'json' => $payload,
                'debug' => true, // Enable Guzzle debug to log raw request/response
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('Bosta delivery created', ['order_id' => $order->id, 'response' => $result]);
            return $result;
        } catch (RequestException $e) {
            $errorDetails = [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response from Bosta API',
            ];
            Log::error('Bosta create delivery failed', $errorDetails);
            Notification::make()
                ->title('Failed to send order to Bosta: API error')
                ->body($errorDetails['response'])
                ->danger()
                ->send();
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
