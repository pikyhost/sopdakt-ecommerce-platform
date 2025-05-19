<?php

namespace App\Filament\Resources\ServiceFeatureResource\Pages;

use App\Filament\Resources\ServiceFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceFeature extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = ServiceFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
