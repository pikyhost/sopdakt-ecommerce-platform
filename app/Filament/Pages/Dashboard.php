<?php

namespace App\Filament\Pages;

use App\Livewire\DashboardFilter;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            DashboardFilter::class,
        ];
    }
}
