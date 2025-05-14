<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = SettingResource::class;

    protected function getRedirectUrl(): string
    {
        return '/admin/settings/1/edit';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
