<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Filament\Notifications\Notification;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class BostaService
{
    protected Client $client;
    protected string $apiKey;
    protected string $apiUrl;
    protected ?string $businessLocationId = null;

    public function __construct()
    {
        $this->apiKey = config('services.bosta.api_key');
        $this->apiUrl = rtrim(config('services.bosta.api_url'), '/'); // Ensure no trailing slash

        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'Authorization' => $this->apiKey, // ❌ No 'Bearer'
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $this->businessLocationId = config('services.bosta.business_location_id');
        if (empty($this->businessLocationId)) {
            $locations = $this->getBusinessLocations();
            if (!empty($locations['data'][0]['_id'])) {
                $this->businessLocationId = $locations['data'][0]['_id'];
            }
        }
    }

    public function getBusinessLocations(): array
    {
        try {
            $response = $this->client->get('/api/v2/business/locations');
            $data = json_decode($response->getBody()->getContents(), true);
            Log::debug('Bosta locations fetched', ['locations' => $data]);
            return $data;
        } catch (RequestException $e) {
            Log::error('Failed to fetch Bosta locations', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);

            Notification::make()
                ->title('Failed to fetch Bosta locations')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return [];
        }
    }

    public function createDelivery(Order $order): ?array
    {
        if (empty($this->businessLocationId)) {
            Notification::make()
                ->title('Cannot create delivery')
                ->body('No business location configured')
                ->danger()
                ->send();
            return null;
        }

        $payload = $this->buildDeliveryPayload($order);
        if (!$payload) {
            return null;
        }

        Log::debug('Bosta delivery payload', ['order_id' => $order->id, 'payload' => $payload]);

        try {
            $response = $this->client->post('/api/v2/deliveries', [
                'json' => $payload,
                'debug' => app()->environment('local'),
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('Bosta delivery created', ['order_id' => $order->id, 'response' => $result]);
            return $result;
        } catch (RequestException $e) {
            $error = [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response',
            ];

            Log::error('Bosta create delivery failed', $error);
            return null;
        }
    }

    protected function buildDeliveryPayload(Order $order): ?array
    {
        $contact = $order->user ?? $order->contact;
        $city = $order->city;

        if (!$contact || !$city || !$city->bosta_code) {
            Log::error('Missing contact or city info', ['order_id' => $order->id]);
            return null;
        }

        $firstLine = $contact instanceof User
            ? $contact->addresses()->where('is_primary', true)->first()?->address
            : $contact->address;

        if (!$firstLine) {
            Log::error('Missing contact address', ['order_id' => $order->id]);
            return null;
        }

        $fullName = $contact->name ?? 'Unknown Name';
        $nameParts = explode(' ', $fullName);
        $firstName = $nameParts[0] ?? 'First';
        $lastName = $nameParts[1] ?? 'Last';
        $fullReceiverName = $fullName;

        return [
            'type' => 'SEND', // ✅ Correct type
            'businessReference' => (string) $order->id,
            'codAmount' => $order->total,
            'allowToOpenPackage' => false,

            'pickupAddress' => [
                'firstLine' => 'Warehouse Pickup', // Replace with your real pickup address
                'districtId' => '6YrUkz3c--',
                'city' => $city->name,
            ],

            'dropOffAddress' => [
                'firstLine' => $firstLine,
                'districtId' => '6YrUkz3c--',
                'city' => $city->bosta_code,
            ],

            'receiver' => [
                'phone' => $contact->phone ?? '+201234567890',
                'fullName' => $fullReceiverName,
            ],

            'specs' => [
                'packageDetails' => [
                    'itemsCount' => $order->items->sum('quantity'),
                    'description' => 'Order #' . $order->id,
                    'weight' => 1, // Must be a number > 0
                ],
            ],

            'notes' => $order->notes ?? '',
        ];
    }

   public function getDeliveryStatus(string $bostaDeliveryId): ?string
{
    try {
        $response = $this->client->get("/api/v2/deliveries/track/$bostaDeliveryId");
        $result = json_decode($response->getBody()->getContents(), true);
        return $result['CurrentStatus'] ?? null;
    } catch (RequestException $e) {
        Log::error("Bosta status fetch failed", [
            'delivery_id' => $bostaDeliveryId,
            'error' => $e->getMessage(),
        ]);
        return null;
    }
}


    public function mapBostaStatusToOrderStatus(string $bostaStatus): OrderStatus
    {
        return match ($bostaStatus) {
            'Delivered' => OrderStatus::Completed,
            'Delivery Failed', 'Returned' => OrderStatus::Refund,
            'Cancelled' => OrderStatus::Cancelled,
            'Out for Delivery' => OrderStatus::Shipping,
            'Delayed' => OrderStatus::Delayed,
            default => OrderStatus::Shipping,
        };
    }
}
