<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JtExpressService
{
    protected $apiKey;
    protected $apiSecret;
    protected $baseUrl;
    protected $accountId;

    public function __construct()
    {
        $this->apiKey = config('services.jt_express.api_key');
        $this->apiSecret = config('services.jt_express.api_secret');
        $this->baseUrl = config('services.jt_express.base_url');
        $this->accountId = config('services.jt_express.account_id');
    }

    protected function generateSignature(string $payload, string $timestamp): string
    {
        // J&T Express typically uses HMAC-SHA256 with API secret, timestamp, and payload
        $data = $timestamp . $payload;
        return base64_encode(hash_hmac('sha256', $data, $this->apiSecret, true));
    }

    protected function makeRequest(string $endpoint, array $payload, string $method = 'POST')
    {
        try {
            $timestamp = now()->timestamp; // Use Unix timestamp
            $payloadJson = json_encode($payload);
            $signature = $this->generateSignature($payloadJson, $timestamp);

            $headers = [
                'API-KEY' => $this->apiKey,
                'SIGNATURE' => $signature,
                'TIMESTAMP' => $timestamp,
                'Content-Type' => 'application/json',
            ];

            Log::info("J&T Express Request", [
                'endpoint' => $endpoint,
                'payload' => $payload,
                'headers' => $headers,
            ]);

            $response = Http::withHeaders($headers)
                ->$method($this->baseUrl . $endpoint, $payload);

            $responseData = $response->json();

            Log::info("J&T Express Response", [
                'endpoint' => $endpoint,
                'response' => $responseData,
            ]);

            return $responseData;
        } catch (\Exception $e) {
            Log::error('J&T Express Request Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'code' => 0,
                'msg' => 'Request failed: ' . $e->getMessage(),
            ];
        }
    }

    public function createOrder(array $orderData)
    {
        $payload = [
            'accountId' => $this->accountId,
            'serviceType' => 'STANDARD',
            'order' => $orderData,
        ];

        return $this->makeRequest('/order/create', $payload);
    }

    public function trackLogistics($trackingData)
    {
        $payload = [
            'accountId' => $this->accountId,
            'txlogisticId' => $trackingData->txlogisticId,
            'billCode' => $trackingData->billCode,
        ];

        Log::info('track J&T Express shipment', ['payload' => $payload]);

        return $this->makeRequest('/tracking', $payload);
    }

    public function checkingOrder($orderData)
    {
        $payload = [
            'accountId' => $this->accountId,
            'txlogisticId' => $orderData->txlogisticId,
            'billCode' => $orderData->billCode,
        ];

        return $this->makeRequest('/order/check', $payload);
    }

    public function getOrderStatus($orderData)
    {
        $payload = [
            'accountId' => $this->accountId,
            'txlogisticId' => $orderData->txlogisticId,
            'billCode' => $orderData->billCode,
        ];

        return $this->makeRequest('/order/status', $payload);
    }

    public function getLogisticsTrajectory($trackingData)
    {
        $payload = [
            'accountId' => $this->accountId,
            'txlogisticId' => $trackingData->txlogisticId,
            'billCode' => $trackingData->billCode,
        ];

        return $this->makeRequest('/tracking/trajectory', $payload);
    }

    public function cancelOrder($orderData)
    {
        $payload = [
            'accountId' => $this->accountId,
            'txlogisticId' => $orderData->txlogisticId,
            'billCode' => $orderData->billCode,
        ];

        return $this->makeRequest('/order/cancel', $payload);
    }
}
