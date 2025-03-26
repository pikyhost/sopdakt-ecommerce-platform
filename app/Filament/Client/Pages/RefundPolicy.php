<?php

namespace App\Filament\Client\Pages;

use App\Models\Policy;
use Filament\Pages\Page;
use Illuminate\Support\Facades\App;

class RefundPolicy extends Page
{
    protected static ?int $navigationSort = 102;

    protected static string $view = 'filament.pages.refund-policy';

    protected static ?string $slug = 'refund-policy';

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund'; // Shield with a checkmark for privacy

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return __('policy.refund_policy');
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
        return __('policy.refund_policy');
    }

    public function getRefundPolicy(): string
    {
        $locale = App::getLocale();
        return Policy::first()?->{"refund_policy_{$locale}"} ?? __('policy.no_policy_found');
    }
}
