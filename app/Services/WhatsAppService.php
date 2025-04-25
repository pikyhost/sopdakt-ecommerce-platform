<?php
// app/Services/WhatsAppService.php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $token;
    protected $phoneId;
    protected $version = 'v18.0'; // Current API version

    public function __construct()
    {
        $this->token = env('WHATSAPP_TOKEN', 'EAAIVzTSFj3YBOwtjhz8Kn1lJu1xalBq1w6dz9MSZBigZAKXoFYeymgQsbxBKqTgJpaPhVZB29PXHZCkYPNXiukvuVYraLBmejjpOjby7rLU8eCKvg1zRIl3tyWmIoqMCLU6dag4traDlroABElVFnQIBu7v83OZCxuIOG8O8U1uPwfbiU7ghnuf6eFQlZCAWtgbW1RJMcdQuZBXQu8ivNZBpgtRzB4mZB');
        $this->phoneId = env('WHATSAPP_PHONE_ID', '670056442847865');
        $this->client = new Client([
            'base_uri' => 'https://graph.facebook.com/' . $this->version . '/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Send a text message
     */
    public function sendTextMessage(string $to, string $message)
    {
        try {
            $response = $this->client->post($this->phoneId . '/messages', [
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $message
                    ]
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('WhatsApp API Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Send a template message
     */
    public function sendTemplateMessage(string $to, string $templateName, array $components = [])
    {
        try {
            $response = $this->client->post($this->phoneId . '/messages', [
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => ['code' => 'en_US'],
                        'components' => $components
                    ]
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('WhatsApp Template Error: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
