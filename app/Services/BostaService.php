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

        return [
            'type' => 10,
            'businessLocationId' => config('services.bosta.business_location_id'),
            'specs' => [
                'packageDetails' => [
                    'itemsCount' => $order->items->sum('quantity'),
                    'description' => 'تفاصيل الطلب رقم ' . $order->id . ': ' . PHP_EOL .
                        $order->items->map(function ($item, $index) {
                            $product = optional($item->product);
                            $bundle = optional($item->bundle);
                            $size = optional($item->size)->name;
                            $color = optional($item->color)->name;

                            $details = "المنتج رقم " . ($index + 1) . ": ";

                            if ($product->name) {
                                $details .= "المنتج: {$product->name} (رمز المنتج: {$product->sku})";
                            } elseif ($bundle->name) {
                                $details .= "الباقة: {$bundle->name}";
                            }

                            if ($size) {
                                $details .= "، المقاس: $size";
                            }

                            if ($color) {
                                $details .= "، اللون: $color";
                            }

                            $details .= "، الكمية: {$item->quantity}";
                            $details .= "، سعر الوحدة: {$item->price_per_unit} جنيه";
                            $details .= "، الإجمالي: {$item->subtotal} جنيه.";

                            return $details;
                        })->implode(PHP_EOL),

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
    }

    /**
     * Map Bosta status to local OrderStatus enum.
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

    public function getDeliveryStatus(string $bostaDeliveryId): ?string
    {
        try {
            $response = $this->client->get("/api/v2/deliveries/track/$bostaDeliveryId");
            $result = json_decode($response->getBody()->getContents(), true);

            // Log the full response if needed
            Log::debug("Bosta tracking response", ['delivery_id' => $bostaDeliveryId, 'result' => $result]);

            return $result['CurrentStatus'] ?? null;
        } catch (RequestException $e) {
            Log::error("Bosta status fetch failed", [
                'delivery_id' => $bostaDeliveryId,
                'message' => $e->getMessage(),
                'response' => $e->hasResponse()
                    ? $e->getResponse()->getBody()->getContents()
                    : 'No response from Bosta API',
            ]);

            return null;
        }
    }

}
