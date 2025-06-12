<?php

namespace App\Filament\Resources\HomePageSettingResource\Pages;

use App\Filament\Resources\HomePageSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomePageSetting extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = HomePageSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
