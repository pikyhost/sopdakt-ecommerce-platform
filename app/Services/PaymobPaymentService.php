<?php

namespace App\Services;

use App\Interfaces\PaymentGatewayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymobPaymentService extends BasePaymentService implements PaymentGatewayInterface
{
    /**
     * Create a new class instance.
     */
    protected $api_key;
    protected $integrations_id;

    public function __construct()
    {
        $this->base_url = env("BAYMOB_BASE_URL");
        $this->api_key = env("BAYMOB_API_KEY");
        $this->header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $this->integrations_id = [5059981, 5059766];
    }

//first generate token to access api
    protected function generateToken()
    {
        $response = $this->buildRequest('POST', '/api/auth/tokens', ['api_key' => $this->api_key]);
        return $response->getData(true)['data']['token'];
    }

    public function sendPayment(Request $request): array
    {
        $authToken = $this->generateToken();
        Log::info('Auth Token Generated', ['token' => $authToken]);
        $this->header['Authorization'] = 'Bearer ' . $authToken;

        // Create Order
        $orderData = [
            'auth_token' => $authToken,
            'delivery_needed' => false,
            'amount_cents' => $request->input('amount_cents'),
            'items' => [],
        ];
        $orderResponse = $this->buildRequest('POST', '/api/ecommerce/orders', $orderData);
        Log::info('Order Creation Response', ['response' => $orderResponse->getData(true)]);
        $orderId = $orderResponse->getData(true)['data']['id'] ?? null;

        if (!$orderId) {
            Log::error('Order creation failed', ['response' => $orderResponse->getData(true)]);
            return ['success' => false, 'message' => 'Order creation failed.'];
        }

        // Billing Data
        $billingData = [
            "apartment" => "NA", "email" => $request->input('contact_email'),
            "floor" => "NA", "first_name" => $request->input('name'),
            "street" => "NA", "building" => "NA", "phone_number" => "0123456789",
            "shipping_method" => "NA", "postal_code" => "NA",
            "city" => "NA", "country" => "NA", "last_name" => "NA", "state" => "NA",
        ];

        // Generate Payment Key
        $paymentToken = $this->generatePaymentKey($authToken, $orderId, $request->input('amount_cents'), $billingData);
        Log::info('Payment Key Response', ['token' => $paymentToken]);

        if (!$paymentToken) {
            Log::error('Payment key generation failed');
            return ['success' => false, 'message' => 'Failed to generate payment key.'];
        }

        $iframeId = env('PAYMOB_IFRAME_ID');
        $iframeUrl = "{$this->base_url}/api/acceptance/iframes/{$iframeId}?payment_token={$paymentToken}";
        Log::info('Iframe URL Generated', ['url' => $iframeUrl]);

        return [
            'success' => true,
            'iframe_url' => $iframeUrl,
        ];
    }

    public function callBack(Request $request): bool
    {
        $response = $request->all();
        Storage::put('paymob_response.json', json_encode($request->all()));

        if (isset($response['success']) && $response['success'] === 'true') {

            return true;
        }
        return false;

    }

    protected function generatePaymentKey($authToken, $orderId, $amountCents, $billingData)
    {
        $data = [
            'auth_token' => $authToken,
            'amount_cents' => $amountCents,
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => $billingData,
            'currency' => 'EGP',
            'integration_id' => $this->integrations_id[0], // use iframe integration id
        ];

        $response = $this->buildRequest('POST', '/api/acceptance/payment_keys', $data);

        return $response->getData(true)['data']['token'] ?? null;
    }

}
