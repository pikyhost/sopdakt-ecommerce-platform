<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Filters;
use App\Filament\Widgets\RedirectHomePage;
use App\Filament\Widgets\TopCustomers;
use App\Filament\Widgets\TopProducts;
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
            Filters::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\Analysis::class,
            TopProducts::class,
            TopCustomers::class,
        ];
    }
}
