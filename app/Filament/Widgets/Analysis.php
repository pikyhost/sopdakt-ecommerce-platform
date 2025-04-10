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

    #[On('updateFromDate')]
    public function updateFromDate(string $from): void
    {
        $this->fromDate = Carbon::make($from);
        $this->updateChartData();
    }

    #[On('updateToDate')]
    public function updateToDate(string $to): void
    {
        $this->toDate = Carbon::make($to);
        $this->updateChartData();
    }
    public function goto(string $url): void
    {
        redirect()->to($url);
    }

    protected function getStats(): array
    {
        $locale = App::getLocale();

        $startDate = $this->fromDate ??= now()->subMonth();
        $endDate = $this->toDate ??= now();

        // Stats calculations
        $totalProducts = Product::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $processingOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['pending', 'preparing', 'shipping'])
            ->count();

        // Build URLs
        $encodedFrom = urlencode($startDate);
        $encodedUntil = urlencode($endDate);

        $productUrl = "/admin/products?tableFilters[created_at][clause]=between&tableFilters[created_at][from]={$encodedFrom}&tableFilters[created_at][period]=days&tableFilters[created_at][until]={$encodedUntil}&tableFilters[is_published][isActive]=false&tableFilters[is_featured][isActive]=false";

        $ordersUrl = "/admin/orders?tableFilters[created_at][created_from]={$encodedFrom}&tableFilters[created_at][created_until]={$encodedUntil}";

        $revenueUrl = $productUrl;

        $processingUrl = "/admin/orders?tableFilters[status][values][0]=pending&tableFilters[status][values][1]=preparing&tableFilters[status][values][2]=shipping";

        return [
            Stat::make(
                $locale === 'ar' ? 'عدد المنتجات في الفترة' : 'Products in Period',
                $totalProducts
            )
                ->color('success')
                ->description($locale === 'ar' ? 'عدد المنتجات المُضافة في النطاق الزمني المحدد.' : 'Products added within selected time range.')
                ->icon('heroicon-m-archive-box')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href = '{$productUrl}'",
                ]),

            Stat::make(
                $locale === 'ar' ? 'عدد الطلبات في الفترة' : 'Orders in Period',
                $totalOrders
            )
                ->color('success')
                ->description($locale === 'ar' ? 'عدد الطلبات في النطاق الزمني المحدد.' : 'Orders placed within selected time range.')
                ->icon('heroicon-o-shopping-bag')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href = '{$ordersUrl}'",
                ]),

            Stat::make(
                $locale === 'ar' ? 'إجمالي المبيعات' : 'Total Sales',
                number_format($totalRevenue)
            )
                ->color('primary')
                ->description($locale === 'ar' ? 'إجمالي المبيعات في النطاق الزمني المحدد.' : 'Total sales in selected time range.')
                ->icon('heroicon-m-banknotes')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href = '{$revenueUrl}'",
                ]),

            Stat::make(
                $locale === 'ar' ? 'الطلبات قيد المعالجة' : 'Processing Orders',
                $processingOrders
            )
                ->color('warning')
                ->description($locale === 'ar' ? 'الطلبات التي لا تزال قيد المعالجة في الفترة المحددة.' : 'Orders still processing in selected time range.')
                ->icon('heroicon-m-cog')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href = '{$processingUrl}'",
                ]),
        ];

    }
}
