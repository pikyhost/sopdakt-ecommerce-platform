<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AramexService
{
    protected $baseUrl;
    protected $auth;

    public function __construct()
    {
        $this->baseUrl = config('aramex.base_url');
        $this->auth = config('aramex.auth');
    }

    protected function makeRequest(string $endpoint, array $data)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . $endpoint, $data);

        return $response->json();
    }

    public function createShipment(array $shipmentDetails)
    {
        $payload = array_merge([
            'ClientInfo' => $this->auth,
            'LabelInfo' => [
                'ReportID' => 9729,
                'ReportType' => 'URL',
            ]
        ], $shipmentDetails);

        return $this->makeRequest('CreateShipments', ['Shipments' => [$payload]]);
    }

    public function trackShipment(string $waybillNumber)
    {
        return $this->makeRequest('Tracking/Service_1_0.svc/json/TrackShipments', [
            'ClientInfo' => $this->auth,
            'Shipments' => [$waybillNumber],
        ]);
    }
}
