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

    public function __construct()
    {
        $this->apiKey = config('services.bosta.api_key');
        $this->apiUrl = config('services.bosta.api_url');

        $this->client = new Client([
            'base_uri' => rtrim($this->apiUrl, '/') . '/', // Ensure no double slashes
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
     * @param Order $order
     * @return array|null
     */
    public function createDelivery(Order $order): ?array
    {
        $payload = $this->buildDeliveryPayload($order);

        if (!$payload) {
            return null;
        }

        Log::debug('Bosta delivery payload', ['order_id' => $order->id, 'payload' => $payload]);

        try {
            $response = $this->client->post('/api/v2/deliveries', [
                'json' => $payload,
                'debug' => app()->environment('local'), // Enable debug only in local
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            Log::info('Bosta delivery created', [
                'order_id' => $order->id,
                'response' => $result,
            ]);

            return $result;
        } catch (RequestException $e) {
            $error = [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'response' => $e->hasResponse()
                    ? $e->getResponse()->getBody()->getContents()
                    : 'No response from Bosta API',
            ];

            Log::error('Bosta create delivery failed', $error);

            Notification::make()
                ->title('Failed to send order to Bosta: API error')
                ->body($error['response'])
                ->danger()
                ->send();

            return null;
        }
    }

    /**
     * Build the payload for Bosta delivery request.
     *
     * @param Order $order
     * @return array|null
     */
    protected function buildDeliveryPayload(Order $order): ?array
    {
        $contact = $order->user ?? $order->contact;
        $city = $order->city;

        if (!$contact || !$city || !$city->bosta_code) {
            Log::error('Cannot create Bosta delivery: missing contact or city info', [
                'order_id' => $order->id,
            ]);

            Notification::make()
                ->title('Cannot send order to Bosta: Missing contact or city info')
                ->danger()
                ->send();

            return null;
        }

        $firstLine = null;

        if ($contact instanceof User) {
            $primaryAddress = $contact->addresses()->where('is_primary', true)->first();
            $firstLine = $primaryAddress?->address;
        } else {
            $firstLine = $contact->address ?? null;
        }

        if (!$firstLine) {
            Log::error('Cannot create Bosta delivery: missing contact address', ['order_id' => $order->id]);

            Notification::make()
                ->title('Cannot send order to Bosta: Missing contact address')
                ->danger()
                ->send();

            return null;
        }

        $fullName = $contact->name ?? 'Unknown Name';
        $nameParts = explode(' ', $fullName);
        $firstName = $nameParts[0] ?? 'First';
        $lastName = $nameParts[1] ?? 'Last';

        $payload = [
            'type' => 10,
            'businessLocationId' => config('services.bosta.business_location_id'),
            'specs' => [
                'packageDetails' => [
                    'itemsCount' => 1,
                    'description' => 'Order #' . $order->id,
                ],
            ],
            'notes' => $order->notes ?? '',
            'cod' => $order->total,
            'dropOffAddress' => [
                'city' => $city->bosta_code,
                'firstLine' => $firstLine,
                'district' => $city->name ?? 'Unknown',
            ],
            'receiver' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phone' => $contact->phone ?? '+201234567890',
                'email' => $contact->email ?? 'test@example.com',
            ],
            'webhookUrl' => config('services.bosta.webhook_url'), // Register webhook
            'webhookCustomHeaders' => [
                'Authorization' => 'Bearer ' . config('services.bosta.webhook_secret'),
            ],
        ];

        return $payload;
    }

    /**
     * Create a pickup request in Bosta.
     *
     * @param string $scheduledDate
     * @param array $contactPerson
     * @param int $noOfPackages
     * @return array|null
     */
    public function createPickup(string $scheduledDate, array $contactPerson, int $noOfPackages = 1): ?array
    {
        $payload = [
            'scheduledDate' => $scheduledDate, // e.g., "2025-05-11"
            'businessLocationId' => config('services.bosta.business_location_id'),
            'contactPerson' => [
                'name' => $contactPerson['name'],
                'phone' => $contactPerson['phone'],
                'email' => $contactPerson['email'] ?? null,
            ],
            'noOfPackages' => $noOfPackages,
            'packageType' => 'Normal',
            'notes' => 'Pickup for orders',
        ];

        try {
            $response = $this->client->post('/api/v2/pickups', [
                'json' => $payload,
                'debug' => app()->environment('local'),
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('Bosta pickup created', ['result' => $result]);
            return $result;
        } catch (RequestException $e) {
            $error = [
                'message' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response from Bosta API',
            ];
            Log::error('Bosta pickup creation failed', $error);
            return null;
        }
    }

    /**
     * Map Bosta numeric state code to local OrderStatus enum.
     *
     * @param int $bostaStateCode
     * @return OrderStatus
     */
    public function mapBostaStateCodeToOrderStatus($bostaStateCode): OrderStatus
    {
        return match ($bostaStateCode) {
            10 => OrderStatus::Preparing, // Pickup requested
            20, 24, 30, 105 => OrderStatus::Shipping, // Route Assigned, Received at warehouse, In transit, On hold
            21, 23, 41 => OrderStatus::Shipping, // Picked up
            22, 40 => OrderStatus::Shipping, // Picking up from consignee or for cash collection
            25 => OrderStatus::Completed, // Fulfilled (Fulfillment)
            45 => OrderStatus::Completed, // Delivered
            46 => OrderStatus::Refund, // Returned to business
            47 => OrderStatus::Delayed, // Exception
            49 => OrderStatus::Cancelled, // Canceled
            48 => OrderStatus::Cancelled, // Terminated
            60 => OrderStatus::Refund, // Returned to stock (Fulfillment)
            100 => OrderStatus::Cancelled, // Lost
            101 => OrderStatus::Cancelled, // Damaged
            102 => OrderStatus::Delayed, // Investigation
            103 => OrderStatus::Refund, // Awaiting your action
            104 => OrderStatus::Cancelled, // Archived
            default => OrderStatus::Shipping, // Fallback
        };
    }

    /**
     * Map Bosta string status to local OrderStatus enum (for legacy compatibility).
     *
     * @param string $bostaStatus
     * @return OrderStatus
     */
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
