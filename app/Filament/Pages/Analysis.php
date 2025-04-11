<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TopProducts;
use App\Livewire\AnalysisPageStats;
use App\Livewire\Filters;
use App\Livewire\LastOrders;
use App\Livewire\LocationsAnalysisWidget;
use App\Livewire\OrdersChart;
use App\Livewire\SalesAnnualComparisonChart;
use App\Livewire\SalesComparisonChart;
use App\Livewire\UsersChart;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Analysis extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.analysis';



    public static function getNavigationLabel(): string
    {
        return __('Website Analysis');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Analysis');
    }

    public function getHeading(): string|Htmlable
    {
        return __('Website Analysis');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Filters::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AnalysisPageStats::class,
            UsersChart::class,
            OrdersChart::class,
            SalesAnnualComparisonChart::class,
            SalesComparisonChart::class,
            LocationsAnalysisWidget::class,
            TopProducts::class,
            LastOrders::class,
        ];
    }
}
