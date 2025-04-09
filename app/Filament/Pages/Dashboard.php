<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RedirectHomePage;
use App\Livewire\DashboardFilter;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;

class Dashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
            RedirectHomePage::class,
            DashboardFilter::class,
        ];
    }
}
