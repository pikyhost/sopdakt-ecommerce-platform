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
    protected ?string $businessLocationId;

    public function __construct()
    {
        $this->apiKey = config('services.bosta.api_key');
        $this->apiUrl = config('services.bosta.api_url');
        $this->businessLocationId = config('services.bosta.business_location_id');

        // Log configuration for debugging
        Log::debug('BostaService configuration', [
            'api_key' => $this->apiKey ? 'set' : 'missing',
            'api_url' => $this->apiUrl,
            'business_location_id' => $this->businessLocationId,
            'webhook_url' => config('services.bosta.webhook_url'),
            'webhook_secret' => config('services.bosta.webhook_secret') ? 'set' : 'missing',
        ]);

        if (!$this->apiKey || !$this->apiUrl || !$this->businessLocationId) {
            Log::error('Bosta configuration incomplete', [
                'api_key' => $this->apiKey,
                'api_url' => $this->apiUrl,
                'business_location_id' => $this->businessLocationId,
            ]);
            throw new \Exception('Bosta API key, URL, or business location ID is not configured.');
        }

        $this->client = new Client([
            'base_uri' => rtrim($this->apiUrl, '/') . '/',
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
                'debug' => app()->environment('local'),
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

            if (str_contains($error['response'], 'Business Pickup Location not found')) {
                Notification::make()
                    ->title('Failed to send order to Bosta: Invalid Business Location')
                    ->body('The Business Pickup Location ID is invalid. Please contact Bosta support at techsupport@bosta.co to verify the ID.')
                    ->danger()
                    ->send();
            } else {
                Notification::make()
                    ->title('Failed to send order to Bosta: API error')
                    ->body($error['response'])
                    ->danger()
                    ->send();
            }

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
                'contact' => $contact ? 'set' : 'missing',
                'city' => $city ? 'set' : 'missing',
                'bosta_code' => $city->bosta_code ?? 'missing',
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
            'businessLocationId' => $this->businessLocationId,
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
        ];

        // Add webhook fields if set
        $webhookUrl = config('services.bosta.webhook_url');
        if ($webhookUrl) {
            $payload['webhookUrl'] = $webhookUrl;
            $payload['webhookCustomHeaders'] = [
                'Authorization' => 'Bearer ' . config('services.bosta.webhook_secret'),
            ];
        } else {
            Log::warning('BOSTA_WEBHOOK_URL is not set in .env', ['order_id' => $order->id]);
        }

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
            'scheduledDate' => $scheduledDate,
            'businessLocationId' => $this->businessLocationId,
            'contactPerson' => [
                'name' => $contactPerson['name'],
                'phone' => $contactPerson['phone'],
                'email' => $contactPerson['email'] ?? null,
            ],
            'noOfPackages' => $noOfPackages,
            'packageType' => 'Normal',
            'notes' => 'Pickup for orders',
        ];

        if (!$payload['businessLocationId']) {
            Log::error('Cannot create Bosta pickup: missing businessLocationId');
            Notification::make()
                ->title('Cannot create Bosta pickup: Missing business location ID')
                ->danger()
                ->send();
            return null;
        }

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

            Notification::make()
                ->title('Failed to create Bosta pickup: API error')
                ->body($error['response'])
                ->danger()
                ->send();
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
            10 => OrderStatus::Preparing,
            20, 24, 30, 105 => OrderStatus::Shipping,
            21, 23, 41 => OrderStatus::Shipping,
            22, 40 => OrderStatus::Shipping,
            25 => OrderStatus::Completed,
            45 => OrderStatus::Completed,
            46 => OrderStatus::Refund,
            47 => OrderStatus::Delayed,
            49 => OrderStatus::Cancelled,
            48 => OrderStatus::Cancelled,
            60 => OrderStatus::Refund,
            100 => OrderStatus::Cancelled,
            101 => OrderStatus::Cancelled,
            102 => OrderStatus::Delayed,
            103 => OrderStatus::Refund,
            104 => OrderStatus::Cancelled,
            default => OrderStatus::Shipping,
        };
    }

    /**
     * Map Bosta string status to local OrderStatus enum.
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
