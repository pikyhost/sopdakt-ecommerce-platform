<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Widgets\Widget;

class RedirectHomePage extends Widget
{
    protected static string $view = 'filament.widgets.redirect-home-page';

    use InteractsWithActions;
    use InteractsWithForms;

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    public function goToMainPage(): Action
    {
        return Action::make('redirectToHomePage')
            ->button()
            ->icon('heroicon-o-home')
            ->color('primary')
            ->label(__('Go to Website Homepage')) // Translated label
            ->url('/')
            ->openUrlInNewTab(false);
    }

    public static function canView(): bool
    {
        return true;
    }
}
