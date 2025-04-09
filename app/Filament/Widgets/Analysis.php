<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\On;

class Analysis extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;

    public Carbon $fromDate;
    public Carbon $toDate;

    public function mount(): void
    {
        $this->fromDate = now()->subMonth();
        $this->toDate = now();
    }

    #[On('updateFromDateDashboard')]
    public function updateFromDate(string $from): void
    {
        $this->fromDate = Carbon::parse($from)->startOfDay();
        $this->dispatch('$refresh');
    }

    #[On('updateToDateDashboard')]
    public function updateToDate(string $to): void
    {
        $this->toDate = Carbon::parse($to)->endOfDay();
        $this->dispatch('$refresh');
    }

    protected function getStats(): array
    {
        $locale = App::getLocale();

        // Use the date properties instead of filters
        $startDate = $this->fromDate;
        $endDate = $this->toDate;

        // Get filtered products
        $totalProducts = Product::whereBetween('created_at', [$startDate, $endDate])->count();

        // Get filtered orders
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        // Total Revenue for selected period
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total');

        // Processing Orders within period
        $processingOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['pending', 'preparing', 'shipping'])
            ->count();

        return [
            Stat::make(
                $locale === 'ar' ? 'عدد المنتجات في الفترة' : 'Products in Period',
                $totalProducts
            )
                ->color('success')
                ->description($locale === 'ar' ? 'عدد المنتجات المُضافة في النطاق الزمني المحدد.' : 'Products added within selected time range.')
                ->descriptionIcon('heroicon-m-archive-box'),

            Stat::make(
                $locale === 'ar' ? 'عدد الطلبات في الفترة' : 'Orders in Period',
                $totalOrders
            )
                ->color('success')
                ->description($locale === 'ar' ? 'عدد الطلبات في النطاق الزمني المحدد.' : 'Orders placed within selected time range.')
                ->descriptionIcon('heroicon-o-shopping-bag'),

            Stat::make(
                $locale === 'ar' ? 'إجمالي المبيعات' : 'Total Sales',
                number_format($totalRevenue)
            )
                ->color('primary')
                ->description($locale === 'ar' ? 'إجمالي المبيعات في النطاق الزمني المحدد.' : 'Total sales in selected time range.')
                ->descriptionIcon('heroicon-m-banknotes'),

            Stat::make(
                $locale === 'ar' ? 'الطلبات قيد المعالجة' : 'Processing Orders',
                $processingOrders
            )
                ->color('warning')
                ->description($locale === 'ar' ? 'الطلبات التي لا تزال قيد المعالجة في الفترة المحددة.' : 'Orders still processing in selected time range.')
                ->descriptionIcon('heroicon-m-cog'),
        ];
    }
}
