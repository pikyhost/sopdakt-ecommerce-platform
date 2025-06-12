<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WAPilotWhatsAppService
{
    protected $apiToken;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiToken = config('services.wapilot.api_token');
        $this->apiUrl = config('services.wapilot.api_url');
    }

    public function sendMessage(string $phone, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                return true;
            }

            Log::error('Wapilot API error', [
                'phone' => $phone,
                'response' => $response->json(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to send WhatsApp message', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
