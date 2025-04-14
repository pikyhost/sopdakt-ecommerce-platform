<?php

namespace App\Filament\Resources\BundleResource\Pages;

use App\Filament\Resources\BundleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBundles extends ManageRecords
{
    use ManageRecords\Concerns\Translatable;

    protected static string $resource = BundleResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
