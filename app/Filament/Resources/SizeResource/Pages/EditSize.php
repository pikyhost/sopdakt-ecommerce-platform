<?php

namespace App\Filament\Resources\SizeResource\Pages;

use App\Filament\Resources\SizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSize extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = SizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
