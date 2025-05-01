<?php

namespace App\Filament\Resources\WheelSpinResource\Pages;

use App\Filament\Resources\WheelSpinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWheelSpin extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = WheelSpinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
