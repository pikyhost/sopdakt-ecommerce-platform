<?php

namespace App\Filament\Client\Pages;

use App\Models\Policy;
use Filament\Pages\Page;
use Illuminate\Support\Facades\App;

class PrivacyPolicy extends Page
{
    protected static ?int $navigationSort = 102;

    protected static string $view = 'filament.pages.privacy-policy';

    protected static ?string $slug = 'privacy-and-policy';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check'; // Shield with a checkmark for privacy

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('policy.privacy_policy');
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
        return __('policy.privacy_policy');
    }

    public function getPolicy(): string
    {
        $locale = App::getLocale();
        return Policy::first()?->{"privacy_policy_{$locale}"} ?? __('policy.no_policy_found');
    }
}
