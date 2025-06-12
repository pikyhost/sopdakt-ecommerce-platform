<?php

namespace App\Filament\Resources\TopNoticeResource\Pages;

use App\Filament\Resources\TopNoticeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTopNotices extends ListRecords
{
    protected static string $resource = TopNoticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
