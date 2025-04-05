<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;

class HomePageRedirect extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.home-page-redirect';

    protected static ?string $pollingInterval = null;

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
        return Filament::auth()->check();
    }
}
