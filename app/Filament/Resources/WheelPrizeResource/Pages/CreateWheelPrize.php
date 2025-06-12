<?php

namespace App\Filament\Resources\WheelPrizeResource\Pages;

use App\Filament\Resources\WheelPrizeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWheelPrize extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = WheelPrizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
