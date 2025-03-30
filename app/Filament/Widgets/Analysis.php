<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\App;

class Analysis extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $locale = App::getLocale(); // Get the current language

        // Get the start and end date from filters
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        // Total Products Count
        $totalProducts = Product::count();

        // Total Orders Count
        $totalOrders = Order::count();

        // Total Revenue (Sum of 'total' column in orders)
        $totalRevenueWithoutPeriod = Order::sum('total');

        // Total Revenue for selected period
        $totalRevenueQuery = Order::query();
        $ordersForSelectedPeriodQuery = Order::query();

        if ($startDate) {
            $totalRevenueQuery->whereBetween('created_at', [$startDate, $endDate]);
            $ordersForSelectedPeriodQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $totalRevenue = $totalRevenueQuery->sum('total');
        $ordersForSelectedPeriod = $ordersForSelectedPeriodQuery->count();

        // Processing Orders Count (Orders in pending, preparing, shipping)
        $processingOrders = Order::whereIn('status', ['pending', 'preparing', 'shipping'])->count();

        return [
            // Total Products
            Stat::make(
                $locale === 'ar' ? 'إجمالي عدد المنتجات' : 'Total Products',
                $totalProducts
            )
                ->color('success')
                ->description($locale === 'ar' ? 'إجمالي عدد المنتجات المتاحة في المتجر.' : 'Total available products in the store.')
                ->descriptionIcon('heroicon-m-archive-box'),

            // Total Orders
            Stat::make(
                $locale === 'ar' ? 'إجمالي عدد الطلبات' : 'Total Orders',
                $totalOrders
            )
                ->color('success')
                ->description($locale === 'ar' ? 'إجمالي عدد الطلبات التي تمت في المتجر.' : 'Total completed orders in the store.')
                ->descriptionIcon('heroicon-o-shopping-bag'),

            // Total Revenue
            Stat::make(
                $locale === 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue',
                number_format($totalRevenueWithoutPeriod)
            )
                ->color('primary')
                ->description($locale === 'ar' ? 'إجمالي الإيرادات المكتسبة من المتجر.' : 'Total revenue earned from the store.')
                ->descriptionIcon('heroicon-m-banknotes'),

            // Revenue for selected period
            Stat::make(
                $locale === 'ar' ? 'إيرادات الفترة المحددة' : 'Revenue for Selected Period',
                number_format($totalRevenue)
            )
                ->color('primary')
                ->description($locale === 'ar' ? 'الإيرادات ضمن النطاق الزمني المحدد.' : 'Revenue within the selected time range.')
                ->descriptionIcon('heroicon-m-banknotes'),

            // Orders for selected period
            Stat::make(
                $locale === 'ar' ? 'عدد الطلبات لفترة زمنية مختارة' : 'Orders for Selected Period',
                $ordersForSelectedPeriod
            )
                ->color('info')
                ->description($locale === 'ar' ? 'عدد الطلبات ضمن النطاق الزمني المحدد.' : 'Number of orders within the selected time range.')
                ->descriptionIcon('heroicon-o-shopping-bag'),

            // Processing Orders
            Stat::make(
                $locale === 'ar' ? 'الطلبات قيد المعالجة' : 'Processing Orders',
                $processingOrders
            )
                ->color('warning')
                ->description($locale === 'ar' ? 'الطلبات التي لا تزال في حالة المعالجة.' : 'Orders that are still being processed.')
                ->descriptionIcon('heroicon-m-cog'),
        ];
    }
}
