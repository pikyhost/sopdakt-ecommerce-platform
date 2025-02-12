<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Traits\HasPdfDownloadAction;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    use HasPdfDownloadAction;
    use ViewRecord\Concerns\Translatable;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('View User');
    }
}
