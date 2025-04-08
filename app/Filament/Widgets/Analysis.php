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

        // Default: last month
        $startDate = !is_null($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : now()->subMonth()->startOfDay();

        $endDate = !is_null($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : now()->endOfDay();

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
                $locale === 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue',
                number_format($totalRevenue)
            )
                ->color('primary')
                ->description($locale === 'ar' ? 'إجمالي الإيرادات في النطاق الزمني المحدد.' : 'Total revenue in selected time range.')
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
