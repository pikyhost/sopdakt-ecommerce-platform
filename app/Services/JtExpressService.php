<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class JtExpressService
{
    protected $baseUrl;
    protected $apiAccount;
    protected $privateKey;
    protected $customerCode;

    public function __construct()
    {
        $this->baseUrl = env('JT_EXPRESS_BASE_URL', 'https://openapi.jtjms-eg.com/webopenplatformapi/api');
        $this->apiAccount = env('JT_EXPRESS_API_ACCOUNT', '29250815308379141');
        $this->privateKey = env('JT_EXPRESS_PRIVATE_KEY', 'afa1047cce70493c9d5d29704f05d0d9');
        $this->customerCode = env('JT_EXPRESS_CUSTOMER_CODE', 'J0086024138');
    }

    protected function getAuthHeaders(array $requestBody)
    {
        $jsonData = json_encode($requestBody, JSON_UNESCAPED_UNICODE);
        $md5Hash  = md5($jsonData . $this->privateKey, true);
        $digest   = base64_encode($md5Hash);

        return [
            'apiAccount'   => $this->apiAccount,
            'digest'       => $digest,
            'timestamp'    => time(),
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
    }

    public function createOrder(array $orderData)
    {
        try {
            $requestBody = [
                'customerCode'   => $this->customerCode,
                'txlogisticId'   => $orderData['tracking_number'] ?? ('EGY' . time() . rand(1000, 9999)),
                'serviceType'    => $orderData['service_type'] ?? '02',
                'orderType'      => $orderData['order_type'] ?? '2',
                'deliveryType'   => $orderData['delivery_type'] ?? '04',
                'payType'        => $orderData['pay_type'] ?? 'PP_PM',
                'expressType'    => $orderData['express_type'] ?? 'EZ',
                'network'        => $orderData['network'] ?? '',
                'length'         => $orderData['length'] ?? 30,
                'width'          => $orderData['width'] ?? 10,
                'height'         => $orderData['height'] ?? 60,
                'weight'         => $orderData['weight'] ?? 5.02,
                'totalQuantity'  => $orderData['quantity'] ?? '1',
                'remark'         => $orderData['remark'] ?? '',
                'operateType'    => $orderData['operate_type'] ?? 1,
                'goodsType'      => $orderData['goods_type'] ?? 'ITN1',
                'invoceNumber'   => $orderData['invoice_number'] ?? '',
                'packingNumber'  => $orderData['packing_number'] ?? '',
                'batchNumber'    => $orderData['batch_number'] ?? '',
                'billCode'       => $orderData['bill_code'] ?? '',
                'offerFee'       => $orderData['offer_fee'] ?? 0,
                'sendStartTime'  => $orderData['send_start_time'] ?? date('Y-m-d H:i:s'),
                'sendEndTime'    => $orderData['send_end_time'] ?? date('Y-m-d H:i:s', strtotime('+1 day')),
                'sender' => [
                    'name'                   => $orderData['sender_name'],
                    'company'                => $orderData['sender_company'] ?? '',
                    'countryCode'            => $orderData['sender_country_code'] ?? 'EGY',
                    'prov'                   => $orderData['sender_province'],
                    'city'                   => $orderData['sender_city'],
                    'area'                   => $orderData['sender_area'] ?? '',
                    'town'                   => $orderData['sender_town'] ?? '',
                    'street'                 => $orderData['sender_street'] ?? '',
                    'address'                => $orderData['sender_address'],
                    'addressBak'             => $orderData['sender_address_bak'] ?? $orderData['sender_address'],
                    'postCode'               => $orderData['sender_postal_code'] ?? '',
                    'mobile'                 => $orderData['sender_mobile'],
                    'phone'                  => $orderData['sender_phone'] ?? $orderData['sender_mobile'],
                    'mailBox'                => $orderData['sender_email'] ?? '',
                    'alternateSenderPhoneNo' => $orderData['sender_alternate_phone'] ?? '',
                    'areaCode'               => $orderData['sender_area_code'] ?? '',
                    'building'               => $orderData['sender_building'] ?? '',
                    'floor'                  => $orderData['sender_floor'] ?? '',
                    'flats'                  => $orderData['sender_flats'] ?? '',
                ],
                'receiver' => [
                    'name'                      => $orderData['receiver_name'],
                    'company'                   => $orderData['receiver_company'] ?? '',
                    'countryCode'               => $orderData['receiver_country_code'] ?? 'EGY',
                    'prov'                      => $orderData['receiver_province'],
                    'city'                      => $orderData['receiver_city'],
                    'area'                      => $orderData['receiver_area'] ?? '',
                    'town'                      => $orderData['receiver_town'] ?? '',
                    'address'                   => $orderData['receiver_address'],
                    'addressBak'                => $orderData['receiver_address_bak'] ?? $orderData['receiver_address'],
                    'postCode'                  => $orderData['receiver_postal_code'] ?? '',
                    'mobile'                    => $orderData['receiver_mobile'],
                    'phone'                     => $orderData['receiver_phone'] ?? $orderData['receiver_mobile'],
                    'mailBox'                   => $orderData['receiver_email'] ?? '',
                    'alternateReceiverPhoneNo'  => $orderData['receiver_alternate_phone'] ?? '',
                    'areaCode'                  => $orderData['receiver_area_code'] ?? '',
                    'building'                  => $orderData['receiver_building'] ?? '',
                    'floor'                     => $orderData['receiver_floor'] ?? '',
                    'flats'                     => $orderData['receiver_flats'] ?? '',
                ],
                'items' => isset($orderData['items']) ? $orderData['items'] : [
                    [
                        'itemName'      => $orderData['item_name'] ?? 'Default Item',
                        'number'        => $orderData['item_quantity'] ?? 1,
                        'itemType'      => $orderData['item_type'] ?? 'ITN1',
                        'itemValue'     => $orderData['item_value'] ?? '0',
                        'priceCurrency' => $orderData['item_currency'] ?? 'EGP',
                        'desc'          => $orderData['item_description'] ?? '',
                        'englishName'   => $orderData['item_english_name'] ?? '',
                        'chineseName'   => $orderData['item_chinese_name'] ?? '',
                        'itemUrl'       => $orderData['item_url'] ?? '',
                    ]
                ],
            ];

            $response = Http::asForm()
                ->withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/order/addOrder', $requestBody);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to create J&T Express order: ' . $e->getMessage());
        }
    }

    public function checkingOrder(string $orderNumber)
    {
        try {
            $requestBody = [
                'customerCode' => $this->customerCode,
                'txlogisticId' => $orderNumber
            ];

            $response = Http::asForm()
                ->withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/order/checkOrder', $requestBody);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to check J&T Express order: ' . $e->getMessage());
        }
    }

    public function cancelOrder(string $orderNumber)
    {
        try {
            $requestBody = [
                'customerCode' => $this->customerCode,
                'txlogisticId' => $orderNumber
            ];

            $response = Http::asForm()
                ->withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/order/cancelOrder', $requestBody);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to cancel J&T Express order: ' . $e->getMessage());
        }
    }

    public function getOrderStatus(string $orderNumber)
    {
        try {
            $requestBody = [
                'customerCode' => $this->customerCode,
                'txlogisticId' => $orderNumber
            ];

            $response = Http::asForm()
                ->withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/order/statusReturn', $requestBody);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to get J&T Express order status: ' . $e->getMessage());
        }
    }

    public function trackLogistics(string $trackingNumber)
    {
        try {
            $requestBody = [
                'customerCode' => $this->customerCode,
                'billCode'     => $trackingNumber
            ];

            $response = Http::asForm()
                ->withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/logistics/trackQuery', $requestBody);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to track J&T Express shipment: ' . $e->getMessage());
        }
    }

    public function getLogisticsTrajectory(string $trackingNumber)
    {
        try {
            $requestBody = [
                'customerCode' => $this->customerCode,
                'billCode'     => $trackingNumber
            ];

            $response = Http::asForm()
                ->withHeaders($this->getAuthHeaders($requestBody))
                ->post($this->baseUrl . '/logistics/trajectoryReturn', $requestBody);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to get J&T Express trajectory: ' . $e->getMessage());
        }
    }

    public function searchThreeSegmentCode(array $parameters)
    {
        try {
            $requestParams = array_merge(
                ['customerCode' => $this->customerCode],
                $parameters
            );

            $response = Http::asForm()
                ->withHeaders($this->getAuthHeaders($requestParams))
                ->post($this->baseUrl . '/other/threeSegmentCodeSearch', $requestParams);

            return $response->json();
        } catch (Exception $e) {
            throw new Exception('Failed to search three-segment code: ' . $e->getMessage());
        }
    }
}
