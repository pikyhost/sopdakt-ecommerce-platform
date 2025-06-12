<?php

namespace App\Filament\Resources\TopNoticeResource\Pages;

use App\Filament\Resources\TopNoticeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTopNotice extends CreateRecord
{
    protected static string $resource = TopNoticeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
