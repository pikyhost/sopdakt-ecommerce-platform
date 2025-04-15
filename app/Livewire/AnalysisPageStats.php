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

        // Get most and least frequent countries
        $countryStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('country')
            ->selectRaw('country_id, count(*) as order_count')
            ->groupBy('country_id')
            ->orderBy('order_count', 'desc')
            ->get();

        $mostFrequentCountry = $countryStats->first();
        $leastFrequentCountry = $countryStats->where('order_count', '>', 0)->last();

        // Get most and least frequent governorates
        $governorateStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('governorate')
            ->selectRaw('governorate_id, count(*) as order_count')
            ->groupBy('governorate_id')
            ->orderBy('order_count', 'desc')
            ->get();

        $mostFrequentGovernorate = $governorateStats->first();
        $leastFrequentGovernorate = $governorateStats->where('order_count', '>', 0)->last();

        // Get most and least frequent cities
        $cityStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('city')
            ->selectRaw('city_id, count(*) as order_count')
            ->groupBy('city_id')
            ->orderBy('order_count', 'desc')
            ->get();

        $mostFrequentCity = $cityStats->first();
        $leastFrequentCity = $cityStats->where('order_count', '>', 0)->last();

        $totalLosses = $cancelledOrders + $refundedOrders;
        $winLossRatio = $totalLosses > 0 ? round($completedOrders / $totalLosses, 2) : 'N/A';
        $averageSale = $ordersForSelectedPeriod > 0 ? round($totalRevenue / $ordersForSelectedPeriod, 2) : 0;
        $customerExpense = $uniqueCustomers > 0 ? round($totalRevenue / $uniqueCustomers, 2) : 0;

        $extraAttributes = [
            'class' => 'cursor-pointer transition transform duration-200 ease-in-out rounded-md hover:bg-gray-100 hover:scale-[1.01] dark:hover:bg-white/10 dark:hover:shadow-lg',
        ];

        $stats = [
            Stat::make($locale === 'ar' ? 'إجمالي المبيعات' : 'Total Sales', number_format($totalRevenue))
                ->color('primary')
                ->description($locale === 'ar' ? 'إجمالي المبيعات خلال الفترة المحددة.' : 'Total sales within the selected time range.')
                ->icon('heroicon-m-banknotes')
                ->url($base)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes),

            Stat::make($locale === 'ar' ? 'إجمالي الطلبات' : 'Total Orders', $ordersForSelectedPeriod)
                ->color('info')
                ->description($locale === 'ar' ? 'إجمالي عدد الطلبات خلال الفترة المحددة.' : 'Total number of orders within the selected time range.')
                ->icon('heroicon-o-shopping-bag')
                ->url($base)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes),

            Stat::make($locale === 'ar' ? 'الطلبات المعلقة' : 'Pending Orders', $pendingOrders)
                ->color('warning')
                ->description($locale === 'ar' ? 'عدد الطلبات التي لا تزال قيد المعالجة.' : 'Number of orders still in processing.')
                ->icon('heroicon-m-clock')
                ->url($base . '&tableFilters[status][values][0]=pending')
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes),

            Stat::make($locale === 'ar' ? 'الطلبات المكتملة' : 'Completed Orders', $completedOrders)
                ->color('success')
                ->description($locale === 'ar' ? 'عدد الطلبات التي تم إكمالها بنجاح.' : 'Number of successfully completed orders.')
                ->icon('heroicon-m-check-badge')
                ->url($base . '&tableFilters[status][values][0]=completed')
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes),

            Stat::make($locale === 'ar' ? 'الطلبات الملغاة' : 'Cancelled Orders', $cancelledOrders)
                ->color('danger')
                ->description($locale === 'ar' ? 'عدد الطلبات التي تم إلغاؤها أو استردادها.' : 'Number of orders that were cancelled or refunded.')
                ->icon('heroicon-o-x-circle')
                ->url($base . '&tableFilters[status][values][0]=cancelled&tableFilters[status][values][1]=refund')
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes),

            Stat::make($locale === 'ar' ? 'نسبة الطلبات الناجحة مقابل الخاسرة' : 'Win/Loss Ratio', $winLossRatio)
                ->color('success')
                ->description($locale === 'ar' ? 'نسبة الطلبات الناجحة مقابل الملغاة أو المستردة.' : 'Ratio of successful to cancelled/refunded orders.')
                ->icon('heroicon-m-chart-pie'),

            Stat::make($locale === 'ar' ? 'متوسط قيمة الطلب' : 'Average Sale', number_format($averageSale, 2))
                ->color('info')
                ->description($locale === 'ar' ? 'متوسط قيمة الطلب الواحد في الفترة المحددة.' : 'Average order value in the selected time range.')
                ->icon('heroicon-m-currency-dollar'),

            Stat::make($locale === 'ar' ? 'متوسط إنفاق العميل' : 'Customer Expense', number_format($customerExpense, 2))
                ->color('info')
                ->description($locale === 'ar' ? 'متوسط الإنفاق لكل عميل في الفترة المختارة.' : 'Average spending per customer in the selected period.')
                ->icon('heroicon-m-user-group'),

            Stat::make($locale === 'ar' ? 'المستخدمون الجدد' : 'New Users Joined', $newUsers)
                ->color('primary')
                ->url($usersURL)
                ->openUrlInNewTab()
                ->description($locale === 'ar' ? 'عدد المستخدمين الجدد الذين انضموا خلال الفترة المحددة.' : 'Number of new users who joined within the selected time range.')
                ->icon('heroicon-m-user-plus')
                ->extraAttributes($extraAttributes),
        ];

        // Add location-based stats if there are orders with location data
        if ($mostFrequentCountry) {
            $mostCountryUrl = $mostFrequentCountry->country_id ? "/admin/countries/{$mostFrequentCountry->country_id}" : null;
            $leastCountryUrl = $leastFrequentCountry->country_id ? "/admin/countries/{$leastFrequentCountry->country_id}" : null;

            $stats[] = Stat::make($locale === 'ar' ? 'الدولة الأكثر طلباً' : 'Most Orders Country',
                $mostFrequentCountry->country?->name ?? 'N/A')
                ->color('success')
                ->description($locale === 'ar' ?
                    "عدد الطلبات: {$mostFrequentCountry->order_count}" :
                    "Orders: {$mostFrequentCountry->order_count}")
                ->icon('heroicon-m-globe-alt')
                ->url($mostCountryUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);

            $stats[] = Stat::make($locale === 'ar' ? 'الدولة الأقل طلباً' : 'Least Orders Country',
                $leastFrequentCountry->country?->name ?? 'N/A')
                ->color('warning')
                ->description($locale === 'ar' ?
                    "عدد الطلبات: {$leastFrequentCountry->order_count}" :
                    "Orders: {$leastFrequentCountry->order_count}")
                ->icon('heroicon-m-globe-alt')
                ->url($leastCountryUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);
        }

        if ($mostFrequentGovernorate) {
            $mostGovernorateUrl = $mostFrequentGovernorate->governorate_id ? "/admin/governorates/{$mostFrequentGovernorate->governorate_id}" : null;
            $leastGovernorateUrl = $leastFrequentGovernorate->governorate_id ? "/admin/governorates/{$leastFrequentGovernorate->governorate_id}" : null;

            $stats[] = Stat::make($locale === 'ar' ? 'المحافظة الأكثر طلباً' : 'Most Orders Governorate',
                $mostFrequentGovernorate->governorate?->name ?? 'N/A')
                ->color('success')
                ->description($locale === 'ar' ?
                    "عدد الطلبات: {$mostFrequentGovernorate->order_count}" :
                    "Orders: {$mostFrequentGovernorate->order_count}")
                ->icon('heroicon-m-map')
                ->url($mostGovernorateUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);

            $stats[] = Stat::make($locale === 'ar' ? 'المحافظة الأقل طلباً' : 'Least Orders Governorate',
                $leastFrequentGovernorate->governorate?->name ?? 'N/A')
                ->color('warning')
                ->description($locale === 'ar' ?
                    "عدد الطلبات: {$leastFrequentGovernorate->order_count}" :
                    "Orders: {$leastFrequentGovernorate->order_count}")
                ->icon('heroicon-m-map')
                ->url($leastGovernorateUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);
        }

        if ($mostFrequentCity) {
            $mostCityUrl = $mostFrequentCity->city_id ? "/admin/cities/{$mostFrequentCity->city_id}" : null;
            $leastCityUrl = $leastFrequentCity->city_id ? "/admin/cities/{$leastFrequentCity->city_id}" : null;

            $stats[] = Stat::make($locale === 'ar' ? 'المدينة الأكثر طلباً' : 'Most Orders City',
                $mostFrequentCity->city?->name ?? 'N/A')
                ->color('success')
                ->description($locale === 'ar' ?
                    "عدد الطلبات: {$mostFrequentCity->order_count}" :
                    "Orders: {$mostFrequentCity->order_count}")
                ->icon('heroicon-m-building-office')
                ->url($mostCityUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);

            $stats[] = Stat::make($locale === 'ar' ? 'المدينة الأقل طلباً' : 'Least Orders City',
                $leastFrequentCity->city?->name ?? 'N/A')
                ->color('warning')
                ->description($locale === 'ar' ?
                    "عدد الطلبات: {$leastFrequentCity->order_count}" :
                    "Orders: {$leastFrequentCity->order_count}")
                ->icon('heroicon-m-building-office')
                ->url($leastCityUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);
        }

        return $stats;
    }
}
