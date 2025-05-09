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
        $this->apiAccount = env('JT_EXPRESS_API_ACCOUNT', '765199128979308601');
        $this->privateKey = env('JT_EXPRESS_PRIVATE_KEY', '69024527c19c405a929fec5ae6a6ed46');
        $this->customerCode = env('JT_EXPRESS_CUSTOMER_CODE', 'J0086006967');
        $this->password = env('JT_EXPRESS_PASSWORD', 'Hanyhelmy11');
    }

    protected function getAuthHeaders(array $requestBody)
    {
        $bizContent = json_encode($requestBody, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $digest = base64_encode(md5($bizContent . $this->privateKey, true));

        return [
            'apiAccount' => $this->apiAccount,
            'digest' => $digest,
            'timestamp' => time(),
            'Content-Type' => 'application/json',
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

    protected function validateShippingResponse($shipping_response, array $requiredFields = ['txlogisticId', 'billCode'])
    {
        if (!is_object($shipping_response)) {
            throw new Exception('Invalid shipping response: must be an object');
        }

        foreach ($requiredFields as $field) {
            if (!isset($shipping_response->$field) || empty($shipping_response->$field)) {
                throw new Exception("Missing or empty required field: $field");
            }
        }

        return true;
    }

    public function createOrder(array $orderData)
    {
        try {
            $requestBody = [
                'customerCode' => $this->customerCode,
                'digest' => $this->getBusinessDigest(),
                'txlogisticId' => $orderData['tracking_number'] ?? ('EGY' . time() . rand(1000, 9999)),
                'serviceType' => $orderData['service_type'] ?? '02',
                'orderType' => $orderData['order_type'] ?? '2',
                'deliveryType' => $orderData['delivery_type'] ?? '04',
                'payType' => $orderData['pay_type'] ?? 'PP_PM',
                'expressType' => $orderData['express_type'] ?? 'EZ',
                'network' => $orderData['network'] ?? '',
                'length' => $orderData['length'] ?? 30,
                'width' => $orderData['width'] ?? 10,
                'height' => $orderData['height'] ?? 60,
                'weight' => $orderData['weight'] ?? 5.02,
                'totalQuantity' => $orderData['quantity'] ?? 1,
                'remark' => $orderData['remark'] ?? '',
                'operateType' => $orderData['operate_type'] ?? 1,
                'goodsType' => $orderData['goods_type'] ?? 'ITN1',
                'invoceNumber' => $orderData['invoice_number'] ?? '',
                'packingNumber' => $orderData['packing_number'] ?? '',
                'batchNumber' => $orderData['batch_number'] ?? '',
                'billCode' => $orderData['bill_code'] ?? '',
                'offerFee' => $orderData['offer_fee'] ?? 0,
                'sendStartTime' => $orderData['send_start_time'] ?? now()->format('Y-m-d H:i:s'),
                'sendEndTime' => $orderData['send_end_time'] ?? now()->addDay()->format('Y-m-d H:i:s'),
                'sender' => [
                    'name' => $orderData['sender']['name'] ?? 'Default Sender',
                    'company' => $orderData['sender']['company'] ?? '',
                    'city' => $orderData['sender']['city'] ?? '',
                    'address' => $orderData['sender']['address'] ?? '',
                    'mobile' => $orderData['sender']['mobile'] ?? '',
                    'countryCode' => $orderData['sender']['countryCode'] ?? 'EG',
                    'prov' => $orderData['sender']['prov'] ?? '',
                    'area' => $orderData['sender']['area'] ?? '',
                    'town' => $orderData['sender']['town'] ?? '',
                    'street' => $orderData['sender']['street'] ?? '',
                    'addressBak' => $orderData['sender']['addressBak'] ?? '',
                    'postCode' => $orderData['sender']['postCode'] ?? '',
                    'phone' => $orderData['sender']['phone'] ?? '',
                    'mailBox' => $orderData['sender']['mailBox'] ?? '',
                    'areaCode' => $orderData['sender']['areaCode'] ?? '',
                    'building' => $orderData['sender']['building'] ?? '',
                    'floor' => $orderData['sender']['floor'] ?? '',
                    'flats' => $orderData['sender']['flats'] ?? '',
                    'alternateSenderPhoneNo' => $orderData['sender']['alternateSenderPhoneNo'] ?? '',
                ],
                'receiver' => [
                    'name' => $orderData['receiver']['name'] ?? 'Default Receiver',
                    'prov' => $orderData['receiver']['prov'] ?? '',
                    'city' => $orderData['receiver']['city'] ?? '',
                    'address' => $orderData['receiver']['address'] ?? '',
                    'mobile' => $orderData['receiver']['mobile'] ?? '',
                    'company' => $orderData['receiver']['company'] ?? '',
                    'countryCode' => $orderData['receiver']['countryCode'] ?? 'EG',
                    'area' => $orderData['receiver']['area'] ?? '',
                    'town' => $orderData['receiver']['town'] ?? '',
                    'addressBak' => $orderData['receiver']['addressBak'] ?? '',
                    'street' => $orderData['receiver']['street'] ?? '',
                    'postCode' => $orderData['receiver']['postCode'] ?? '',
                    'phone' => $orderData['receiver']['phone'] ?? '',
                    'mailBox' => $orderData['receiver']['mailBox'] ?? '',
                    'areaCode' => $orderData['receiver']['areaCode'] ?? '',
                    'building' => $orderData['receiver']['building'] ?? '',
                    'floor' => $orderData['receiver']['floor'] ?? '',
                    'flats' => $orderData['receiver']['flats'] ?? '',
                    'alternateReceiverPhoneNo' => $orderData['receiver']['alternateReceiverPhoneNo'] ?? '',
                ],
                'items' => isset($orderData['items']) ? $orderData['items'] : [
                    [
                        'itemName' => $orderData['item_name'] ?? 'Default Item',
                        'number' => $orderData['item_quantity'] ?? 1,
                        'itemType' => $orderData['item_type'] ?? 'ITN1',
                        'itemValue' => $orderData['item_value'] ?? '0',
                        'priceCurrency' => $orderData['item_currency'] ?? 'EGP',
                        'desc' => $orderData['item_description'] ?? '',
                        'englishName' => $orderData['item_english_name'] ?? '',
                        'chineseName' => $orderData['item_chinese_name'] ?? '',
                        'itemUrl' => $orderData['item_url'] ?? '',
                    ]
                ],
            ];

            $response = Http::withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/order/addOrder', $requestBody);

            $responseData = $response->json();
            Log::info('create J&T Express order', ['response' => $responseData]);
            return $responseData;
        } catch (Exception $e) {
            Log::error('create J&T Express order', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw new Exception('Failed to create J&T Express order: ' . $e->getMessage());
        }
    }

    public function checkingOrder($shipping_response)
    {
        try {
            $this->validateShippingResponse($shipping_response, ['txlogisticId', 'billCode']);

            $requestBody = [
                'customerCode' => $this->customerCode,
                'digest' => $this->getBusinessDigest(),
                'txlogisticId' => $shipping_response->txlogisticId,
                'billCode' => $shipping_response->billCode,
                'sortingCode' => $shipping_response->sortingCode ?? '',
                'createOrderTime' => $shipping_response->createOrderTime ?? now()->toDateTimeString(),
                'lastCenterName' => $shipping_response->lastCenterName ?? '',
                'command' => 2,
            ];

            $response = Http::withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/order/getOrders', $requestBody);

            $responseData = $response->json();
            Log::info('check J&T Express order', ['response' => $responseData]);
            return $responseData;
        } catch (Exception $e) {
            Log::error('Failed to check J&T Express order', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw new Exception('Failed to check J&T Express order: ' . $e->getMessage());
        }
    }

    public function cancelOrder($shipping_response, string $reason = '')
    {
        try {
            $this->validateShippingResponse($shipping_response, ['txlogisticId', 'billCode']);

            $requestBody = [
                'customerCode' => $this->customerCode,
                'digest' => $this->getBusinessDigest(),
                'txlogisticId' => $shipping_response->txlogisticId,
                'billCode' => $shipping_response->billCode,
                'sortingCode' => $shipping_response->sortingCode ?? '',
                'createOrderTime' => $shipping_response->createOrderTime ?? now()->toDateTimeString(),
                'lastCenterName' => $shipping_response->lastCenterName ?? '',
                'reason' => $reason ?: 'Cancellation requested by customer',
            ];

            $response = Http::withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/order/cancelOrder', $requestBody);

            $responseData = $response->json();
            Log::info('cancel J&T Express order', ['response' => $responseData]);
            return $responseData;
        } catch (Exception $e) {
            Log::error('Failed to cancel J&T Express order', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw new Exception('Failed to cancel J&T Express order: ' . $e->getMessage());
        }
    }

    public function getOrderStatus($shipping_response)
    {
        try {
            $this->validateShippingResponse($shipping_response, ['txlogisticId', 'billCode']);

            $requestBody = [
                'customerCode' => $this->customerCode,
                'digest' => $this->getBusinessDigest(),
                'txlogisticId' => $shipping_response->txlogisticId,
                'billCode' => $shipping_response->billCode,
                'sortingCode' => $shipping_response->sortingCode ?? '',
                'createOrderTime' => $shipping_response->createOrderTime ?? now()->toDateTimeString(),
                'lastCenterName' => $shipping_response->lastCenterName ?? '',
            ];

            $response = Http::withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/logistics/trace', $requestBody);

            $responseData = $response->json();
            Log::info('get J&T Express order status', ['response' => $responseData]);
            return $responseData;
        } catch (Exception $e) {
            Log::error('Failed to get J&T Express order status', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw new Exception('Failed to get J&T Express order status: ' . $e->getMessage());
        }
    }

    public function trackLogistics($shipping_response)
    {
        try {
            $this->validateShippingResponse($shipping_response, ['txlogisticId', 'billCode']);

            $requestBody = [
                'customerCode' => $this->customerCode,
                'digest' => $this->getBusinessDigest(),
                'txlogisticId' => $shipping_response->txlogisticId,
                'billCode' => $shipping_response->billCode,
                'sortingCode' => $shipping_response->sortingCode ?? '',
                'createOrderTime' => $shipping_response->createOrderTime ?? now()->toDateTimeString(),
                'lastCenterName' => $shipping_response->lastCenterName ?? '',
            ];

            $response = Http::withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/logistics/trace', $requestBody);

            $responseData = $response->json();
            Log::info('track J&T Express shipment', ['response' => $responseData]);
            return $responseData;
        } catch (Exception $e) {
            Log::error('Failed to track J&T Express shipment', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw new Exception('Failed to track J&T Express shipment: ' . $e->getMessage());
        }
    }

    public function getLogisticsTrajectory($shipping_response)
    {
        try {
            $this->validateShippingResponse($shipping_response, ['txlogisticId', 'billCode']);

            $requestBody = [
                'customerCode' => $this->customerCode,
                'digest' => $this->getBusinessDigest(),
                'txlogisticId' => $shipping_response->txlogisticId,
                'billCode' => $shipping_response->billCode,
                'sortingCode' => $shipping_response->sortingCode ?? '',
                'createOrderTime' => $shipping_response->createOrderTime ?? now()->toDateTimeString(),
                'lastCenterName' => $shipping_response->lastCenterName ?? '',
            ];

            $response = Http::withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/logistics/trajectoryReturn', $requestBody);

            $responseData = $response->json();
            Log::info('get J&T Express trajectory', ['response' => $responseData]);
            return $responseData;
        } catch (Exception $e) {
            Log::error('Failed to get J&T Express trajectory', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw new Exception('Failed to get J&T Express trajectory: ' . $e->getMessage());
        }
    }

    public function searchThreeSegmentCode($shipping_response)
    {
        try {
            $this->validateShippingResponse($shipping_response, ['txlogisticId', 'billCode']);

            $requestBody = [
                'customerCode' => $this->customerCode,
                'digest' => $this->getBusinessDigest(),
                'txlogisticId' => $shipping_response->txlogisticId,
                'billCode' => $shipping_response->billCode,
                'sortingCode' => $shipping_response->sortingCode ?? '',
                'createOrderTime' => $shipping_response->createOrderTime ?? now()->toDateTimeString(),
                'lastCenterName' => $shipping_response->lastCenterName ?? '',
            ];

            $response = Http::withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/other/threeSegmentCodeSearch', $requestBody);

            $responseData = $response->json();
            Log::info('search three-segment code', ['response' => $responseData]);
            return $responseData;
        } catch (Exception $e) {
            Log::error('Failed to search three-segment code', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw new Exception('Failed to search three-segment code: ' . $e->getMessage());
        }
    }
}
