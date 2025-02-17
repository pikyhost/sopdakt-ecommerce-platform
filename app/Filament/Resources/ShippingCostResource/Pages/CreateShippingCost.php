<?php

namespace App\Filament\Resources\ShippingCostResource\Pages;

use App\Filament\Resources\ShippingCostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateShippingCost extends CreateRecord
{
    protected static string $resource = ShippingCostResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
