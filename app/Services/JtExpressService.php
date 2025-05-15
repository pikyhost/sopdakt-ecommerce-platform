<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JtExpressService
{
    protected $baseUrl;
    protected $apiAccount;
    protected $privateKey;
    protected $customerCode;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = env('JT_EXPRESS_BASE_URL', 'https://openapi.jtjms-eg.com/webopenplatformapi/api');
        $this->apiAccount = env('JT_EXPRESS_API_ACCOUNT', '789507402494906427');
        $this->privateKey = env('JT_EXPRESS_PRIVATE_KEY', '85e13bc999a6466f9b5d4e2c6015a35b');
        $this->customerCode = env('JT_EXPRESS_CUSTOMER_CODE', 'J0086004385'); // Fixed to match .env
        $this->password = env('JT_EXPRESS_PASSWORD', 'Hanyhelmy10');
    }

    protected function generateDigest(array $requestBody): string
    {
        $dataString = http_build_query($requestBody);
        return base64_encode(md5($dataString . $this->privateKey, true));
    }

    protected function getAuthHeaders(array $requestBody): array
    {
        return [
            'apiAccount' => $this->apiAccount,
            'digest' => $this->generateDigest($requestBody),
            'timestamp' => time(),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
    }

    protected function getBusinessDigest()
    {
        $pwd = md5($this->password . 'jadada236t2');
        $pwd = strtoupper($pwd);
        $signatureString = $this->customerCode . $pwd . $this->privateKey;
        $digest = base64_encode(md5($signatureString, true));
        return $digest;
    }

    protected function makeRequest(string $endpoint, array $payload)
    {
        try {
            $response = Http::withHeaders($this->getAuthHeaders($payload))
                ->asForm()
                ->post($this->baseUrl . $endpoint, $payload);

            $responseData = $response->json();
            Log::info("J&T Express Request to $endpoint", ['payload' => $payload, 'response' => $responseData]);
            return $responseData;
        } catch (Exception $e) {
            Log::error("J&T Express Request Error to $endpoint", [
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
            'customerCode' => $this->customerCode,
            'txlogisticId' => $orderData['tracking_number'] ?? ('EGY' . time() . rand(1000, 9999)),
            'serviceType' => $orderData['service_type'] ?? '02',
            'orderType' => $orderData['order_type'] ?? '2',
            'deliveryType' => $orderData['delivery_type'] ?? '04',
            'payType' => $orderData['pay_type'] ?? 'PP_PM',
            'expressType' => $orderData['express_type'] ?? 'EZ',
            'length' => $orderData['length'] ?? 30,
            'width' => $orderData['width'] ?? 10,
            'height' => $orderData['height'] ?? 60,
            'weight' => $orderData['weight'] ?? 1.02,
            'totalQuantity' => $orderData['quantity'] ?? 1,
            'remark' => $orderData['remark'] ?? '',
            'operateType' => $orderData['operate_type'] ?? 1,
            'goodsType' => $orderData['goods_type'] ?? 'ITN1',
            'sendStartTime' => $orderData['send_start_time'] ?? now()->format('Y-m-d H:i:s'),
            'sendEndTime' => $orderData['send_end_time'] ?? now()->addDay()->format('Y-m-d H:i:s'),
            'sender' => [
                'name' => $orderData['sender']['name'] ?? 'Default Sender',
                'company' => $orderData['sender']['company'] ?? '',
                'city' => $orderData['sender']['city'] ?? '',
                'address' => $orderData['sender']['address'] ?? '',
                'mobile' => $orderData['sender']['mobile'] ?? '',
                'countryCode' => $orderData['sender']['countryCode'] ?? 'EG',
            ],
            'receiver' => [
                'name' => $orderData['receiver']['name'] ?? 'Default Receiver',
                'city' => $orderData['receiver']['city'] ?? '',
                'address' => $orderData['receiver']['address'] ?? '',
                'mobile' => $orderData['receiver']['mobile'] ?? '',
                'countryCode' => $orderData['receiver']['countryCode'] ?? 'EG',
            ],
            'items' => $orderData['items'] ?? [
                    ['itemName' => 'Default Item', 'number' => 1, 'itemValue' => '0', 'priceCurrency' => 'EGP']
                ],
        ];

        // Generate fresh digest for each request
        $payload['digest'] = $this->generateDigest($payload);

        return $this->makeRequest('/order/addOrder', $payload);
    }

    public function trackLogistics($trackingData)
    {
        $payload = [
            'customerCode' => $this->customerCode,
            'digest' => $this->getBusinessDigest(),
            'txlogisticId' => $trackingData->txlogisticId,
            'billCode' => $trackingData->billCode,
        ];

        return $this->makeRequest('/logistics/trace', $payload);
    }

    public function checkingOrder($orderData)
    {
        $payload = [
            'customerCode' => $this->customerCode,
            'digest' => $this->getBusinessDigest(),
            'txlogisticId' => $orderData->txlogisticId,
            'billCode' => $orderData->billCode,
            'command' => 2,
        ];

        return $this->makeRequest('/order/getOrders', $payload);
    }

    public function getOrderStatus($orderData)
    {
        $payload = [
            'customerCode' => $this->customerCode,
            'digest' => $this->getBusinessDigest(),
            'txlogisticId' => $orderData->txlogisticId,
            'billCode' => $orderData->billCode,
        ];

        return $this->makeRequest('/logistics/trace', $payload);
    }

    public function getLogisticsTrajectory($trackingData)
    {
        $payload = [
            'customerCode' => $this->customerCode,
            'digest' => $this->getBusinessDigest(),
            'txlogisticId' => $trackingData->txlogisticId,
            'billCode' => $trackingData->billCode,
        ];

        return $this->makeRequest('/logistics/trajectoryReturn', $payload);
    }

    public function cancelOrder($orderData, string $reason = '')
    {
        $payload = [
            'customerCode' => $this->customerCode,
            'digest' => $this->getBusinessDigest(),
            'txlogisticId' => $orderData->txlogisticId,
            'billCode' => $orderData->billCode,
            'reason' => $reason ?: 'Cancellation requested by customer',
        ];

        return $this->makeRequest('/order/cancelOrder', $payload);
    }
}
