<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use App\Models\Faq;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFaq extends CreateRecord
{
    protected static string $resource = FaqResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        $existingLocales = Faq::pluck('locale')->toArray();

        // You allow only "en" and "ar"
        $allowedLocales = ['en', 'ar'];

        // If all allowed locales already exist in the table, block access
        return count(array_diff($allowedLocales, $existingLocales)) > 0;
    }
}
