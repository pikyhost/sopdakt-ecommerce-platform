<?php

namespace App\Filament\Client\Pages;

use Filament\Pages\Page;

class TermsAndConditions extends Page
{
    protected static ?int $navigationSort = 101;

    protected static string $view = 'filament.pages.terms-and-conditions';

    protected static ?string $slug = 'terms-and-conditions';

    protected static ?string $navigationIcon = 'heroicon-o-scale'; // Scales for terms and conditions

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('policy.terms_of_service'); // Arabic for "Terms and Conditions"
    }
}
