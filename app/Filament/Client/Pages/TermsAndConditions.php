<?php

namespace App\Filament\Client\Pages;

use App\Models\Policy;
use Filament\Pages\Page;
use Illuminate\Support\Facades\App;

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

    public static function getNavigationGroup(): ?string
    {
        return __('policy.pages_group'); //Products Attributes Management
    }

    /**
     * @return string|\Illuminate\Contracts\Support\Htmlable
     */
    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('policy.terms_of_service'); // Arabic for "Terms and Conditions"
    }

    public static function getNavigationLabel(): string
    {
        return __('policy.terms_of_service'); // Arabic for "Terms and Conditions"
    }

    public function getTermsAndConditions(): string
    {
        $locale = App::getLocale();
        return Policy::first()?->{"terms_of_service_{$locale}"} ?? __('policy.no_policy_found');
    }
}
