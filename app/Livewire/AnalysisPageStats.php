<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\User;
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
        $this->fromDate = now()->subMonth();
        $this->toDate = now();
    }
    #[On('updateFromDate1')]
    public function updateFromDate(?string $from): void
    {
        if ($from) {
            $this->fromDate = Carbon::parse($from)->startOfDay();
        }
        $this->dispatch('$refresh');
    }

    #[On('updateToDate1')]
    public function updateToDate(?string $to): void
    {
        if ($to) {
            $this->toDate = Carbon::parse($to)->endOfDay();
        }
        $this->dispatch('$refresh');
    }

    protected function getStats(): array
    {
        $locale = App::getLocale();
        $startDate = $this->fromDate;
        $endDate = $this->toDate;

        $encodedFrom = urlencode($startDate->toDateString());
        $encodedUntil = urlencode($endDate->toDateString());

        $base = "/admin/orders?tableFilters[created_at][created_from]={$encodedFrom}&tableFilters[created_at][created_until]={$encodedUntil}";

        $usersURL = "/admin/users?tableFilters[created_at][clause]=between&tableFilters[created_at][from]={$encodedFrom}&tableFilters[created_at][until]={$encodedUntil}&tableFilters[created_at][period]=days";

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $ordersForSelectedPeriod = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $pendingOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', OrderStatus::Pending)->count();
        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', OrderStatus::Completed)->count();
        $cancelledOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', OrderStatus::Cancelled)->count();
        $refundedOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', OrderStatus::Refund)->count();
        $uniqueCustomers = Order::whereBetween('created_at', [$startDate, $endDate])->distinct('user_id')->count('user_id');

        $totalLosses = $cancelledOrders + $refundedOrders;
        $winLossRatio = $totalLosses > 0 ? round($completedOrders / $totalLosses, 2) : 'N/A';
        $averageSale = $ordersForSelectedPeriod > 0 ? round($totalRevenue / $ordersForSelectedPeriod, 2) : 0;
        $customerExpense = $uniqueCustomers > 0 ? round($totalRevenue / $uniqueCustomers, 2) : 0;

        $extraAttributes = [
            'class' => 'cursor-pointer transition transform duration-200 ease-in-out rounded-md hover:bg-gray-100 hover:scale-[1.01] dark:hover:bg-white/10 dark:hover:shadow-lg',
        ];

        return [
            Stat::make($locale === 'ar' ? 'إجمالي المبيعات' : 'Total Sales', number_format($totalRevenue))
                ->color('primary')
                ->description($locale === 'ar' ? 'إجمالي المبيعات خلال الفترة المحددة.' : 'Total sales within the selected time range.')
                ->descriptionIcon('heroicon-m-banknotes')
                ->url($base)
                ->extraAttributes($extraAttributes),

            Stat::make($locale === 'ar' ? 'إجمالي الطلبات' : 'Total Orders', $ordersForSelectedPeriod)
                ->color('info')
                ->description($locale === 'ar' ? 'إجمالي عدد الطلبات خلال الفترة المحددة.' : 'Total number of orders within the selected time range.')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->url($base)
                ->extraAttributes($extraAttributes),

            Stat::make($locale === 'ar' ? 'الطلبات المعلقة' : 'Pending Orders', $pendingOrders)
                ->color('warning')
                ->description($locale === 'ar' ? 'عدد الطلبات التي لا تزال قيد المعالجة.' : 'Number of orders still in processing.')
                ->descriptionIcon('heroicon-m-clock')
                ->url($base . '&tableFilters[status][values][0]=pending')
                ->extraAttributes($extraAttributes),

            Stat::make($locale === 'ar' ? 'الطلبات المكتملة' : 'Completed Orders', $completedOrders)
                ->color('success')
                ->description($locale === 'ar' ? 'عدد الطلبات التي تم إكمالها بنجاح.' : 'Number of successfully completed orders.')
                ->descriptionIcon('heroicon-m-check-badge')
                ->url($base . '&tableFilters[status][values][0]=completed')
                ->extraAttributes($extraAttributes),

            Stat::make($locale === 'ar' ? 'الطلبات الملغاة' : 'Cancelled Orders', $cancelledOrders)
                ->color('danger')
                ->description($locale === 'ar' ? 'عدد الطلبات التي تم إلغاؤها أو استردادها.' : 'Number of orders that were cancelled or refunded.')
                ->descriptionIcon('heroicon-o-x-circle')
                ->url($base . '&tableFilters[status][values][0]=cancelled&tableFilters[status][values][1]=refund')
                ->extraAttributes($extraAttributes),

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

            Stat::make($locale === 'ar' ? 'المستخدمون الجدد' : 'New Users Joined', $newUsers)
                ->color('primary')
                ->url($usersURL)
                ->description($locale === 'ar' ? 'عدد المستخدمين الجدد الذين انضموا خلال الفترة المحددة.' : 'Number of new users who joined within the selected time range.')
                ->descriptionIcon('heroicon-m-user-plus')
                ->extraAttributes($extraAttributes),
        ];
    }

}
