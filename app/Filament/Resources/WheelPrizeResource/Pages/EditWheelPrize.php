<?php

namespace App\Filament\Resources\WheelPrizeResource\Pages;

use App\Filament\Resources\WheelPrizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWheelPrize extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = WheelPrizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
