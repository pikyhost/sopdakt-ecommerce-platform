<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class JtExpressService
{
    protected $baseUrl;
    protected $apiKey;
    protected $customerCode;

    public function __construct()
    {
        $this->baseUrl      = env('JT_EXPRESS_BASE_URL');
        $this->apiKey       = env('JT_EXPRESS_API_KEY');
        $this->customerCode = env('JT_EXPRESS_CUSTOMER_CODE');
    }

    public function createOrder(array $orderData)
    {
        try {
            $response = Http::withHeaders([
                'ApiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/order/create', [
                'customerCode' => $this->customerCode,
                'orderNumber' => $orderData['order_number'],
                'serviceType' => $orderData['service_type'],
                'parcelContent' => $orderData['parcel_content'],
                'sender' => [
                    'name' => $orderData['sender_name'],
                    'phone' => $orderData['sender_phone'],
                    'address' => $orderData['sender_address'],
                    'city' => $orderData['sender_city'],
                    'province' => $orderData['sender_province'],
                    'postalCode' => $orderData['sender_postal_code'],
                ],
                'receiver' => [
                    'name' => $orderData['receiver_name'],
                    'phone' => $orderData['receiver_phone'],
                    'address' => $orderData['receiver_address'],
                    'city' => $orderData['receiver_city'],
                    'province' => $orderData['receiver_province'],
                    'postalCode' => $orderData['receiver_postal_code'],
                ],
                'parcel' => [
                    'weight' => $orderData['weight'],
                    'length' => $orderData['length'],
                    'width' => $orderData['width'],
                    'height' => $orderData['height'],
                ],
            ]);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to create J&T Express order: ' . $e->getMessage());
        }
    }

    public function trackShipment(string $trackingNumber)
    {
        try {
            $response = Http::withHeaders([
                'ApiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/tracking', [
                'waybillNumber' => $trackingNumber,
            ]);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to track J&T Express shipment: ' . $e->getMessage());
        }
    }

    public function calculateShippingCost(array $parameters)
    {
        try {
            $response = Http::withHeaders([
                'ApiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/price/query', [
                'customerCode' => $this->customerCode,
                'originCity' => $parameters['origin_city'],
                'destinationCity' => $parameters['destination_city'],
                'weight' => $parameters['weight'],
                'length' => $parameters['length'],
                'width' => $parameters['width'],
                'height' => $parameters['height'],
            ]);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to calculate J&T Express shipping cost: ' . $e->getMessage());
        }
    }
}
