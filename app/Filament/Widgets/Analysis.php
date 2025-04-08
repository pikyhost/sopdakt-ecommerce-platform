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
        $locale = App::getLocale();

        // Default to last month if no filters
        $startDate = !is_null($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : now()->subMonth()->startOfMonth();

        $endDate = !is_null($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : now()->subMonth()->endOfMonth();

        // Total Products Count (not date-filtered)
        $totalProducts = Product::count();

        // Orders + Revenue WITHIN selected period (or last month by default)
        $ordersQuery = Order::whereBetween('created_at', [$startDate, $endDate]);

        $totalOrders = $ordersQuery->count();
        $totalRevenue = $ordersQuery->sum('total');

        // All-time total revenue (outside filter)
        $totalRevenueWithoutPeriod = Order::sum('total');

        // Processing Orders in selected period
        $processingOrders = $ordersQuery->whereIn('status', ['pending', 'preparing', 'shipping'])->count();

        return [
            // Total Products
            Stat::make(
                $locale === 'ar' ? 'إجمالي عدد المنتجات' : 'Total Products',
                $totalProducts
            )
                ->color('success')
                ->description($locale === 'ar' ? 'إجمالي عدد المنتجات المتاحة في المتجر.' : 'Total available products in the store.')
                ->descriptionIcon('heroicon-m-archive-box'),

            // Orders in selected period (or last month)
            Stat::make(
                $locale === 'ar' ? 'عدد الطلبات (الفترة)' : 'Orders (Selected Period)',
                $totalOrders
            )
                ->color('info')
                ->description($locale === 'ar' ? 'عدد الطلبات ضمن النطاق الزمني المحدد.' : 'Orders within the selected time range.')
                ->descriptionIcon('heroicon-o-shopping-bag'),

            // Revenue in selected period
            Stat::make(
                $locale === 'ar' ? 'الإيرادات (الفترة)' : 'Revenue (Selected Period)',
                number_format($totalRevenue)
            )
                ->color('primary')
                ->description($locale === 'ar' ? 'الإيرادات ضمن النطاق الزمني المحدد.' : 'Revenue within the selected time range.')
                ->descriptionIcon('heroicon-m-banknotes'),

            // All-time Revenue
            Stat::make(
                $locale === 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue',
                number_format($totalRevenueWithoutPeriod)
            )
                ->color('primary')
                ->description($locale === 'ar' ? 'إجمالي الإيرادات المكتسبة من المتجر.' : 'Total revenue earned from the store.')
                ->descriptionIcon('heroicon-m-banknotes'),

            // Processing Orders in selected period
            Stat::make(
                $locale === 'ar' ? 'الطلبات قيد المعالجة' : 'Processing Orders',
                $processingOrders
            )
                ->color('warning')
                ->description($locale === 'ar' ? 'الطلبات التي لا تزال في حالة المعالجة ضمن الفترة.' : 'Orders still being processed in selected period.')
                ->descriptionIcon('heroicon-m-cog'),
        ];
    }

}
