<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AramexService
{
    protected array $clientInfo;

    public function __construct()
    {
        $this->clientInfo = [
            'UserName'          => env('ARAMEX_USERNAME'),
            'Password'          => env('ARAMEX_PASSWORD'),
            'Version'           => env('ARAMEX_VERSION'),
            'AccountNumber'     => env('ARAMEX_ACCOUNT_NUMBER'),
            'AccountPin'        => env('ARAMEX_ACCOUNT_PIN'),
            'AccountEntity'     => env('ARAMEX_ENTITY'),
            'AccountCountryCode'=> env('ARAMEX_COUNTRY_CODE'),
            'Source'            => (int)env('ARAMEX_SOURCE'),
        ];
    }

    protected function post(string $endpoint, array $payload)
    {
        $url = env('ARAMEX_API_BASE_URL') . '/' . $endpoint;
        return Http::acceptJson()
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload)
            ->json();
    }

    public function getLastShipmentsNumbersRange()
    {
        return $this->post('GetLastShipmentsNumbersRange', [
            'ClientInfo' => $this->clientInfo,
            'Entity' => env('ARAMEX_ENTITY'),
            'ProductGroup' => 'EXP',
            'Transaction' => $this->emptyTransaction(),
        ]);
    }

    public function reserveShipmentNumberRange(int $count = 1000)
    {
        return $this->post('ReserveShipmentNumberRange', [
            'ClientInfo' => $this->clientInfo,
            'Count' => $count,
            'Entity' => env('ARAMEX_ENTITY'),
            'ProductGroup' => 'EXP',
            'Transaction' => $this->emptyTransaction(),
        ]);
    }

    public function printLabel(string $shipmentNumber)
    {
        return $this->post('PrintLabel', [
            'ClientInfo' => $this->clientInfo,
            'LabelInfo' => [
                'ReportID' => 9201,
                'ReportType' => 'URL',
            ],
            'OriginEntity' => env('ARAMEX_ENTITY'),
            'ProductGroup' => 'EXP',
            'ShipmentNumber' => $shipmentNumber,
            'Transaction' => $this->emptyTransaction(),
        ]);
    }

    public function createPickup(array $pickupData)
    {
        return $this->post('CreatePickup', array_merge([
            'ClientInfo' => $this->clientInfo,
            'Transaction' => $this->emptyTransaction(),
        ], $pickupData));
    }

    protected function emptyTransaction(): array
    {
        return [
            'Reference1' => '',
            'Reference2' => '',
            'Reference3' => '',
            'Reference4' => '',
            'Reference5' => '',
        ];
    }
}
