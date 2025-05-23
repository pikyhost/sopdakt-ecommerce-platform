<?php

namespace App\Filament\Client\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Widgets\Widget;

class RedirectHomePageClient extends Widget
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.client.widgets.redirect-home-page-client';

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    public function goToMainPage(): Action
    {
        return Action::make('redirectToHomePage')
            ->button()
            ->icon('heroicon-o-home')
            ->color('primary')
            ->label(__('Go to Website Homepage')) // Translated label
            ->url(config('app.frontend_url')) // Dynamically fetched
            ->openUrlInNewTab(false);
    }

    public static function canView(): bool
    {
        return true;
    }
}
