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
        $completedBase = $base . "&tableFilters[status][values][0]=completed";

        $usersURL = "/admin/users?tableFilters[created_at][clause]=between&tableFilters[created_at][from]={$encodedFrom}&tableFilters[created_at][until]={$encodedUntil}&tableFilters[created_at][period]=days";

        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $ordersForSelectedPeriod = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $pendingOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', OrderStatus::Pending)->count();
        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', OrderStatus::Completed)->count();
        $cancelledOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', OrderStatus::Cancelled)->count();
        $refundedOrders = Order::whereBetween('created_at', [$startDate, $endDate])->where('status', OrderStatus::Refund)->count();
        $uniqueCustomers = Order::whereBetween('created_at', [$startDate, $endDate])->distinct('user_id')->count('user_id');

        // Get most and least frequent countries (all orders)
        $countryStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('country')
            ->selectRaw('country_id, count(*) as order_count')
            ->groupBy('country_id')
            ->orderBy('order_count', 'desc')
            ->get();

        $mostFrequentCountry = $countryStats->first();
        $leastFrequentCountry = $countryStats->where('order_count', '>', 0)->last();

        // Get most and least frequent governorates (all orders)
        $governorateStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('governorate')
            ->selectRaw('governorate_id, count(*) as order_count')
            ->groupBy('governorate_id')
            ->orderBy('order_count', 'desc')
            ->get();

        $mostFrequentGovernorate = $governorateStats->first();
        $leastFrequentGovernorate = $governorateStats->where('order_count', '>', 0)->last();

        // Get most and least frequent cities (all orders)
        $cityStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('city')
            ->selectRaw('city_id, count(*) as order_count, MAX(created_at) as last_order_date')
            ->groupBy('city_id')
            ->orderBy('order_count', 'desc')
            ->orderBy('last_order_date', 'asc')
            ->get()
            ->filter(function ($order) {
                return $order->city_id !== null;
            });

        $mostFrequentCity = $cityStats->first();

        $cityStatsForLeast = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('city')
            ->selectRaw('city_id, count(*) as order_count, MAX(created_at) as last_order_date')
            ->groupBy('city_id')
            ->having('order_count', '>', 0)
            ->orderBy('order_count', 'asc')
            ->orderBy('last_order_date', 'desc')
            ->get()
            ->filter(function ($order) {
                return $order->city_id !== null;
            });

        $leastFrequentCity = $cityStatsForLeast->first();

        // Get most and least frequent countries (completed orders only)
        $completedCountryStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', OrderStatus::Completed)
            ->with('country')
            ->selectRaw('country_id, count(*) as order_count')
            ->groupBy('country_id')
            ->orderBy('order_count', 'desc')
            ->get();

        $mostFrequentCompletedCountry = $completedCountryStats->first();
        $leastFrequentCompletedCountry = $completedCountryStats->where('order_count', '>', 0)->last();

        // Get most and least frequent governorates (completed orders only)
        $completedGovernorateStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', OrderStatus::Completed)
            ->with('governorate')
            ->selectRaw('governorate_id, count(*) as order_count')
            ->groupBy('governorate_id')
            ->orderBy('order_count', 'desc')
            ->get();

        $mostFrequentCompletedGovernorate = $completedGovernorateStats->first();
        $leastFrequentCompletedGovernorate = $completedGovernorateStats->where('order_count', '>', 0)->last();

        // Get most and least frequent cities (completed orders only)
        $completedCityStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', OrderStatus::Completed)
            ->with('city')
            ->selectRaw('city_id, count(*) as order_count, MAX(created_at) as last_order_date')
            ->groupBy('city_id')
            ->orderBy('order_count', 'desc')
            ->orderBy('last_order_date', 'asc')
            ->get()
            ->filter(function ($order) {
                return $order->city_id !== null;
            });

        $mostFrequentCompletedCity = $completedCityStats->first();

        $completedCityStatsForLeast = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', OrderStatus::Completed)
            ->with('city')
            ->selectRaw('city_id, count(*) as order_count, MAX(created_at) as last_order_date')
            ->groupBy('city_id')
            ->having('order_count', '>', 0)
            ->orderBy('order_count', 'asc')
            ->orderBy('last_order_date', 'desc')
            ->get()
            ->filter(function ($order) {
                return $order->city_id !== null;
            });

        $leastFrequentCompletedCity = $completedCityStatsForLeast->first();

        $totalLosses = $cancelledOrders + $refundedOrders;
        $winLossRatio = $totalLosses > 0 ? round($completedOrders / $totalLosses, 2) : 'N/A';
        $averageSale = $ordersForSelectedPeriod > 0 ? round($totalRevenue / $ordersForSelectedPeriod, 2) : 0;
        $customerExpense = $uniqueCustomers > 0 ? round($totalRevenue / $uniqueCustomers, 2) : 0;

// Get all product stats
        $productStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with('items.product')
            ->get()
            ->flatMap(function ($order) {
                return $order->items->filter(fn($item) => $item->product_id)->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => optional($item->product)->name ?? 'Unknown',
                        'slug' => optional($item->product)->slug ?? null, // ✅ Include slug
                        'revenue' => $item->subtotal,
                        'quantity' => $item->quantity,
                    ];
                });
            })
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                return [
                    'product_id' => $productId,
                    'product_name' => $items->first()['product_name'],
                    'slug' => $items->first()['slug'], // ✅ Include slug
                    'total_revenue' => $items->sum('revenue'),
                    'total_quantity' => $items->sum('quantity'),
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        $mostProfitableProduct = $productStats->first();
        $leastProfitableProduct = $productStats->where('total_revenue', '>', 0)->last();

// Get product stats for completed orders only
        $completedProductStats = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', OrderStatus::Completed)
            ->with('items.product')
            ->get()
            ->flatMap(function ($order) {
                return $order->items->filter(fn($item) => $item->product_id)->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => optional($item->product)->name ?? 'Unknown',
                        'slug' => optional($item->product)->slug ?? null, // ✅ Include slug
                        'revenue' => $item->subtotal,
                        'quantity' => $item->quantity,
                    ];
                });
            })
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                return [
                    'product_id' => $productId,
                    'product_name' => $items->first()['product_name'],
                    'slug' => $items->first()['slug'], // ✅ Include slug
                    'total_revenue' => $items->sum('revenue'),
                    'total_quantity' => $items->sum('quantity'),
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        $mostProfitableCompletedProduct = $completedProductStats->first();
        $leastProfitableCompletedProduct = $completedProductStats->where('total_revenue', '>', 0)->last();

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

        // Add location-based stats if there are orders with location data (all orders)
        if ($mostFrequentCountry) {
            $mostCountryUrl = $mostFrequentCountry->country_id ? "/admin/countries/{$mostFrequentCountry->country_id}" : null;
            $leastCountryUrl = $leastFrequentCountry->country_id ? "/admin/countries/{$leastFrequentCountry->country_id}" : null;

            $stats[] = Stat::make($locale === 'ar' ? 'الدولة الأكثر طلباً' : 'Most Orders Country',
                $mostFrequentCountry->country?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
                ->color('success')
                ->description($locale === 'ar' ?
                    "عدد الطلبات: {$mostFrequentCountry->order_count}" :
                    "Orders: {$mostFrequentCountry->order_count}")
                ->icon('heroicon-m-globe-alt')
                ->url($mostCountryUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);

            $stats[] = Stat::make($locale === 'ar' ? 'الدولة الأقل طلباً' : 'Least Orders Country',
                $leastFrequentCountry->country?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
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
                $mostFrequentGovernorate->governorate?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
                ->color('success')
                ->description($locale === 'ar' ?
                    "عدد الطلبات: {$mostFrequentGovernorate->order_count}" :
                    "Orders: {$mostFrequentGovernorate->order_count}")
                ->icon('heroicon-m-map')
                ->url($mostGovernorateUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);

            $stats[] = Stat::make($locale === 'ar' ? 'المحافظة الأقل طلباً' : 'Least Orders Governorate',
                $leastFrequentGovernorate->governorate?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
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
                $mostFrequentCity->city?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
                ->color('success')
                ->description($locale === 'ar' ?
                    "عدد الطلبات: {$mostFrequentCity->order_count}" :
                    "Orders: {$mostFrequentCity->order_count}")
                ->icon('heroicon-m-building-office')
                ->url($mostCityUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);

            $stats[] = Stat::make($locale === 'ar' ? 'المدينة الأقل طلباً' : 'Least Orders City',
                $leastFrequentCity->city?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
                ->color('warning')
                ->description($locale === 'ar' ?
                    "عدد الطلبات: {$leastFrequentCity->order_count}" :
                    "Orders: {$leastFrequentCity->order_count}")
                ->icon('heroicon-m-building-office')
                ->url($leastCityUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);
        }

// Add location-based stats for completed orders only
        if ($mostFrequentCompletedCountry) {
            $mostCompletedCountryUrl = $mostFrequentCompletedCountry->country_id ?
                "/admin/orders?tableFilters[location][country_ids][0]={$mostFrequentCompletedCountry->country_id}&tableFilters[status][values][0]=completed&tableFilters[created_at][created_from]={$encodedFrom}&tableFilters[created_at][created_until]={$encodedUntil}" :
                null;

            $leastCompletedCountryUrl = $leastFrequentCompletedCountry->country_id ?
                "/admin/orders?tableFilters[location][country_ids][0]={$leastFrequentCompletedCountry->country_id}&tableFilters[status][values][0]=completed&tableFilters[created_at][created_from]={$encodedFrom}&tableFilters[created_at][created_until]={$encodedUntil}" :
                null;

            $stats[] = Stat::make($locale === 'ar' ? 'الدولة الأكثر طلباً (مكتملة)' : 'Most Completed Orders Country',
                $mostFrequentCompletedCountry->country?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
                ->color('success')
                ->description($locale === 'ar' ?
                    "عدد الطلبات المكتملة: {$mostFrequentCompletedCountry->order_count}" :
                    "Completed orders: {$mostFrequentCompletedCountry->order_count}")
                ->icon('heroicon-m-globe-alt')
                ->url($mostCompletedCountryUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);

            $stats[] = Stat::make($locale === 'ar' ? 'الدولة الأقل طلباً (مكتملة)' : 'Least Completed Orders Country',
                $leastFrequentCompletedCountry->country?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
                ->color('warning')
                ->description($locale === 'ar' ?
                    "عدد الطلبات المكتملة: {$leastFrequentCompletedCountry->order_count}" :
                    "Completed orders: {$leastFrequentCompletedCountry->order_count}")
                ->icon('heroicon-m-globe-alt')
                ->url($leastCompletedCountryUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);
        }

        if ($mostFrequentCompletedGovernorate) {
            $mostCompletedGovernorateUrl = $mostFrequentCompletedGovernorate->governorate_id ?
                "/admin/orders?tableFilters[location][governorate_ids][0]={$mostFrequentCompletedGovernorate->governorate_id}&tableFilters[status][values][0]=completed&tableFilters[created_at][created_from]={$encodedFrom}&tableFilters[created_at][created_until]={$encodedUntil}" :
                null;

            $leastCompletedGovernorateUrl = $leastFrequentCompletedGovernorate->governorate_id ?
                "/admin/orders?tableFilters[location][governorate_ids][0]={$leastFrequentCompletedGovernorate->governorate_id}&tableFilters[status][values][0]=completed&tableFilters[created_at][created_from]={$encodedFrom}&tableFilters[created_at][created_until]={$encodedUntil}" :
                null;

            $stats[] = Stat::make($locale === 'ar' ? 'المحافظة الأكثر طلباً (مكتملة)' : 'Most Completed Orders Governorate',
                $mostFrequentCompletedGovernorate->governorate?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
                ->color('success')
                ->description($locale === 'ar' ?
                    "عدد الطلبات المكتملة: {$mostFrequentCompletedGovernorate->order_count}" :
                    "Completed orders: {$mostFrequentCompletedGovernorate->order_count}")
                ->icon('heroicon-m-map')
                ->url($mostCompletedGovernorateUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);

            $stats[] = Stat::make($locale === 'ar' ? 'المحافظة الأقل طلباً (مكتملة)' : 'Least Completed Orders Governorate',
                $leastFrequentCompletedGovernorate->governorate?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
                ->color('warning')
                ->description($locale === 'ar' ?
                    "عدد الطلبات المكتملة: {$leastFrequentCompletedGovernorate->order_count}" :
                    "Completed orders: {$leastFrequentCompletedGovernorate->order_count}")
                ->icon('heroicon-m-map')
                ->url($leastCompletedGovernorateUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);
        }

        if ($mostFrequentCompletedCity) {
            $mostCompletedCityUrl = $mostFrequentCompletedCity->city_id ?
                "/admin/orders?tableFilters[location][city_ids][0]={$mostFrequentCompletedCity->city_id}&tableFilters[status][values][0]=completed&tableFilters[created_at][created_from]={$encodedFrom}&tableFilters[created_at][created_until]={$encodedUntil}" :
                null;

            $leastCompletedCityUrl = $leastFrequentCompletedCity->city_id ?
                "/admin/orders?tableFilters[location][city_ids][0]={$leastFrequentCompletedCity->city_id}&tableFilters[status][values][0]=completed&tableFilters[created_at][created_from]={$encodedFrom}&tableFilters[created_at][created_until]={$encodedUntil}" :
                null;

            $stats[] = Stat::make($locale === 'ar' ? 'المدينة الأكثر طلباً (مكتملة)' : 'Most Completed Orders City',
                $mostFrequentCompletedCity->city?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
                ->color('success')
                ->description($locale === 'ar' ?
                    "عدد الطلبات المكتملة: {$mostFrequentCompletedCity->order_count}" :
                    "Completed orders: {$mostFrequentCompletedCity->order_count}")
                ->icon('heroicon-m-building-office')
                ->url($mostCompletedCityUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);

            $stats[] = Stat::make($locale === 'ar' ? 'المدينة الأقل طلباً (مكتملة)' : 'Least Completed Orders City',
                $leastFrequentCompletedCity->city?->name ?? ($locale === 'ar' ? 'غير معروف' : 'Unknown'))
                ->color('warning')
                ->description($locale === 'ar' ?
                    "عدد الطلبات المكتملة: {$leastFrequentCompletedCity->order_count}" :
                    "Completed orders: {$leastFrequentCompletedCity->order_count}")
                ->icon('heroicon-m-building-office')
                ->url($leastCompletedCityUrl)
                ->openUrlInNewTab()
                ->extraAttributes($extraAttributes);

// Stats array updates (Arabic or English based on $locale)
            if ($mostProfitableProduct) {
                $mostProductUrl = $mostProfitableProduct['slug']
                    ? "/admin/products/{$mostProfitableProduct['slug']}/edit"
                    : null;

                $stats[] = Stat::make(
                    $locale === 'ar' ? 'المنتج الأكثر ربحية' : 'Most Profitable Product',
                    $mostProfitableProduct['product_name']
                )
                    ->color('success')
                    ->description($locale === 'ar'
                        ? "إجمالي الإيرادات: " . number_format($mostProfitableProduct['total_revenue'], 2) . " (" . $mostProfitableProduct['total_quantity'] . " وحدة)"
                        : "Total revenue: " . number_format($mostProfitableProduct['total_revenue'], 2) . " (" . $mostProfitableProduct['total_quantity'] . " units)")
                    ->icon('heroicon-m-currency-dollar')
                    ->url($mostProductUrl)
                    ->openUrlInNewTab()
                    ->extraAttributes($extraAttributes);
            }

            if ($leastProfitableProduct && $leastProfitableProduct['total_revenue'] > 0) {
                $leastProductUrl = $leastProfitableProduct['slug']
                    ? "/admin/products/{$leastProfitableProduct['slug']}/edit"
                    : null;

                $stats[] = Stat::make(
                    $locale === 'ar' ? 'المنتج الأقل ربحية' : 'Least Profitable Product',
                    $leastProfitableProduct['product_name']
                )
                    ->color('warning')
                    ->description($locale === 'ar'
                        ? "إجمالي الإيرادات: " . number_format($leastProfitableProduct['total_revenue'], 2) . " (" . $leastProfitableProduct['total_quantity'] . " وحدة)"
                        : "Total revenue: " . number_format($leastProfitableProduct['total_revenue'], 2) . " (" . $leastProfitableProduct['total_quantity'] . " units)")
                    ->icon('heroicon-m-currency-dollar')
                    ->url($leastProductUrl)
                    ->openUrlInNewTab()
                    ->extraAttributes($extraAttributes);
            }

            if ($mostProfitableCompletedProduct) {
                $mostCompletedProductUrl = $mostProfitableCompletedProduct['slug']
                    ? "/admin/products/{$mostProfitableCompletedProduct['slug']}/edit"
                    : null;

                $stats[] = Stat::make(
                    $locale === 'ar' ? 'المنتج الأكثر ربحية (مكتملة)' : 'Most Profitable Product (Completed)',
                    $mostProfitableCompletedProduct['product_name']
                )
                    ->color('success')
                    ->description($locale === 'ar'
                        ? "إجمالي الإيرادات: " . number_format($mostProfitableCompletedProduct['total_revenue'], 2) . " (" . $mostProfitableCompletedProduct['total_quantity'] . " وحدة)"
                        : "Total revenue: " . number_format($mostProfitableCompletedProduct['total_revenue'], 2) . " (" . $mostProfitableCompletedProduct['total_quantity'] . " units)")
                    ->icon('heroicon-m-currency-dollar')
                    ->url($mostCompletedProductUrl)
                    ->openUrlInNewTab()
                    ->extraAttributes($extraAttributes);
            }

            if ($leastProfitableCompletedProduct && $leastProfitableCompletedProduct['total_revenue'] > 0) {
                $leastCompletedProductUrl = $leastProfitableCompletedProduct['slug']
                    ? "/admin/products/{$leastProfitableCompletedProduct['slug']}/edit"
                    : null;

                $stats[] = Stat::make(
                    $locale === 'ar' ? 'المنتج الأقل ربحية (مكتملة)' : 'Least Profitable Product (Completed)',
                    $leastProfitableCompletedProduct['product_name']
                )
                    ->color('warning')
                    ->description($locale === 'ar'
                        ? "إجمالي الإيرادات: " . number_format($leastProfitableCompletedProduct['total_revenue'], 2) . " (" . $leastProfitableCompletedProduct['total_quantity'] . " وحدة)"
                        : "Total revenue: " . number_format($leastProfitableCompletedProduct['total_revenue'], 2) . " (" . $leastProfitableCompletedProduct['total_quantity'] . " units)")
                    ->icon('heroicon-m-currency-dollar')
                    ->url($leastCompletedProductUrl)
                    ->openUrlInNewTab()
                    ->extraAttributes($extraAttributes);
            }

        }
        return $stats;
    }
}
