<?php

namespace App\Filament\Resources\ServiceFeatureResource\Pages;

use App\Filament\Resources\ServiceFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceFeatures extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    
    protected static string $resource = ServiceFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
