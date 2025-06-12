<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use App\Models\Faq;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqs extends ListRecords
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make() ->visible(function () {
                $existingLocales = Faq::pluck('locale')->toArray();
                $allowedLocales = ['en', 'ar'];

                return count(array_diff($allowedLocales, $existingLocales)) > 0;
            }),
        ];
    }
}
