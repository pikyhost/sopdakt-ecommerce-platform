<?php

namespace App\Filament\Resources\ServiceFeatureResource\Pages;

use App\Filament\Resources\ServiceFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceFeature extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = ServiceFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
