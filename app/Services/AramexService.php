<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AramexService
{
    public function createShipment(array $shipmentData)
    {
        $url = config('services.aramex.url') . '/CreateShipments';

        $payload = [
            'ClientInfo' => [
                'UserName' => config('services.aramex.username'),
                'Password' => config('services.aramex.password'),
                'Version' => 'v1',
                'AccountNumber' => config('services.aramex.account_number'),
                'AccountPin' => config('services.aramex.account_pin'),
                'AccountEntity' => config('services.aramex.account_entity'),
                'AccountCountryCode' => config('services.aramex.account_country_code'),
            ],
            'LabelInfo' => [
                'ReportID' => 9729,
                'ReportType' => 'URL',
            ],
            'Shipments' => [$shipmentData],
        ];

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Aramex API error', ['response' => $response->body()]);
            return ['error' => 'Aramex API returned an error.'];
        } catch (\Throwable $e) {
            Log::error('Aramex shipment failed', [
                'error' => $e->getMessage(),
                'request' => $payload,
            ]);

            return ['error' => $e->getMessage()];
        }
    }
}
