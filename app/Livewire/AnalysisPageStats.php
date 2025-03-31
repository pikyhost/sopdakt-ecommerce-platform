<?php

namespace App\Livewire;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\On;
use App\Enums\OrderStatus;

class AnalysisPageStats extends BaseWidget
{
    public Carbon $fromDate;
    public Carbon $toDate;

    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;

    public function mount(): void
    {
        $this->fromDate = now()->subWeek();
        $this->toDate = now();
    }

    #[On('updateFromDate')]
    public function updateFromDate(string $from): void
    {
        $this->fromDate = Carbon::parse($from);
        $this->dispatch('$refresh');
    }

    #[On('updateToDate')]
    public function updateToDate(string $to): void
    {
        $this->toDate = Carbon::parse($to);
        $this->dispatch('$refresh');
    }

    protected function getStats(): array
    {
        $locale = App::getLocale();
        $startDate = $this->fromDate;
        $endDate = $this->toDate;

        // Query for total revenue and total orders in the selected period
        $totalRevenueQuery = Order::whereBetween('created_at', [$startDate, $endDate]);
        $ordersForSelectedPeriodQuery = Order::whereBetween('created_at', [$startDate, $endDate]);

        // Fetch counts
        $totalRevenue = $totalRevenueQuery->sum('total');
        $ordersForSelectedPeriod = $ordersForSelectedPeriodQuery->count();
        $pendingOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', OrderStatus::Pending)->count();
        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', OrderStatus::Completed)->count();
        $cancelledOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', OrderStatus::Cancelled)->count();
        $refundedOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', OrderStatus::Refund)->count();

        // Get unique customer count
        $uniqueCustomers = Order::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user_id')->count('user_id');

        // Calculate Win/Loss Ratio
        $totalLosses = $cancelledOrders + $refundedOrders;
        $winLossRatio = $totalLosses > 0 ? round($completedOrders / $totalLosses, 2) : 'N/A';

        // Calculate Average Sale
        $averageSale = $ordersForSelectedPeriod > 0 ? round($totalRevenue / $ordersForSelectedPeriod, 2) : 0;

        // Calculate Customer Expense
        $customerExpense = $uniqueCustomers > 0 ? round($totalRevenue / $uniqueCustomers, 2) : 0;

        return [
            Stat::make($locale === 'ar' ? 'إيرادات الفترة المحددة' : 'Revenue for Selected Period', number_format($totalRevenue))
                ->color('primary')
                ->description($locale === 'ar' ? 'الإيرادات ضمن النطاق الزمني المحدد.' : 'Revenue within the selected time range.')
                ->descriptionIcon('heroicon-m-banknotes'),

            Stat::make($locale === 'ar' ? 'عدد الطلبات لفترة زمنية مختارة' : 'Orders for Selected Period', $ordersForSelectedPeriod)
                ->color('info')
                ->description($locale === 'ar' ? 'عدد الطلبات ضمن النطاق الزمني المحدد.' : 'Number of orders within the selected time range.')
                ->descriptionIcon('heroicon-o-shopping-bag'),

            Stat::make($locale === 'ar' ? 'الطلبات قيد الانتظار' : 'Pending Orders', $pendingOrders)
                ->color('warning')
                ->description($locale === 'ar' ? 'الطلبات التي لا تزال في حالة انتظار.' : 'Orders that are still pending.')
                ->descriptionIcon('heroicon-m-clock'),

            Stat::make($locale === 'ar' ? 'الطلبات المكتملة' : 'Completed Orders', $completedOrders)
                ->color('success')
                ->description($locale === 'ar' ? 'عدد الطلبات المكتملة فقط.' : 'Number of successfully completed orders.')
                ->descriptionIcon('heroicon-m-check-badge'),

            Stat::make($locale === 'ar' ? 'الطلبات الملغاة' : 'Cancelled Orders', $cancelledOrders)
                ->color('danger')
                ->description($locale === 'ar' ? 'عدد الطلبات الملغاة فقط.' : 'Number of cancelled orders.')
                ->descriptionIcon('heroicon-o-x-circle'),

            Stat::make($locale === 'ar' ? 'نسبة الطلبات الناجحة مقابل الخاسرة' : 'Win/Loss Ratio', $winLossRatio)
                ->color('success')
                ->description($locale === 'ar' ? 'نسبة الطلبات الناجحة مقابل الملغاة أو المستردة.' : 'Ratio of successful to cancelled/refunded orders.')
                ->descriptionIcon('heroicon-m-chart-pie'),

            Stat::make($locale === 'ar' ? 'متوسط قيمة الطلب' : 'Average Sale', number_format($averageSale, 2))
                ->color('info')
                ->description($locale === 'ar' ? 'متوسط قيمة الطلب الواحد في الفترة المحددة.' : 'Average order value in the selected time range.')
                ->descriptionIcon('heroicon-m-currency-dollar'),

            Stat::make($locale === 'ar' ? 'متوسط إنفاق العميل' : 'Customer Expense', number_format($customerExpense, 2))
                ->color('info')
                ->description($locale === 'ar' ? 'متوسط الإنفاق لكل عميل في الفترة المختارة.' : 'Average spending per customer in the selected period.')
                ->descriptionIcon('heroicon-m-user-group'),
        ];
    }
}
