<x-filament-widgets::widget>
    <x-filament::section class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Geographical Distribution') }}</h3>
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 shadow-sm ring-1 ring-primary-200 dark:ring-primary-800">
                    <span class="flex items-center gap-1">
                        <x-heroicon-o-globe-alt class="w-4 h-4" /> {{ $countryData->count() }} {{ __('countries') }}
                    </span>
                    <span class="opacity-50">•</span>
                    <span class="flex items-center gap-1">
                        <x-heroicon-o-map class="w-4 h-4" /> {{ $governorateData->count() }} {{ __('governorates') }}
                    </span>
                    <span class="opacity-50">•</span>
                    <span class="flex items-center gap-1">
                        <x-heroicon-o-map-pin class="w-4 h-4" /> {{ $cityData->count() }} {{ __('cities') }}
                    </span>
                </span>
            </div>

            <hr class="border-gray-200 dark:border-gray-700 mb-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Country Distribution -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ __('By Country') }}</h4>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-start text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-2/4">
                                    {{ __('Country') }}
                                </th>
                                <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                    {{ __('Orders') }}
                                </th>
                                <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                    {{ __('Share (%)') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($countryData as $country)
                                @php
                                    $countryTotal = max(1, $countryData->sum('total'));
                                    $countryName = is_array($country['country'])
                                        ? ($country['country'][app()->getLocale()] ?? $country['country']['en'])
                                        : $country['country'];
                                    $countryUrl = route('filament.admin.resources.countries.orders', [
                                        'record' => $country['id'],
                                        'tableFilters[created_at][created_from]' => $this->fromDate->format('Y-m-d'),
                                        'tableFilters[created_at][created_until]' => $this->toDate->format('Y-m-d'),
                                    ]);
                                @endphp
                                <tr
                                    class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group cursor-pointer"
                                    onclick="window.location.href='{{ $countryUrl }}'"
                                >
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ $countryName }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ number_format($country['total']) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ round(($country['total'] / $countryTotal) * 100, 1) }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                        {{ __('No country data available') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Governorate Distribution -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ __('By Governorate') }}</h4>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-start text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-2/4">
                                    {{ __('Governorate') }}
                                </th>
                                <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                    {{ __('Orders') }}
                                </th>
                                <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                    {{ __('Share (%)') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($governorateData as $governorate)
                                @php
                                    $governorateTotal = max(1, $governorateData->sum('total'));
                                    $govName = is_array($governorate['governorate'])
                                        ? ($governorate['governorate'][app()->getLocale()] ?? $governorate['governorate']['en'])
                                        : $governorate['governorate'];
                                    $governorateUrl = route('filament.admin.resources.governorates.orders', [
                                        'record' => $governorate['id'],
                                        'tableFilters[created_at][created_from]' => $this->fromDate->format('Y-m-d'),
                                        'tableFilters[created_at][created_until]' => $this->toDate->format('Y-m-d'),
                                    ]);
                                @endphp
                                <tr
                                    class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group cursor-pointer"
                                    onclick="window.location.href='{{ $governorateUrl }}'"
                                >
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ $govName }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ number_format($governorate['total']) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ round(($governorate['total'] / $governorateTotal) * 100, 1) }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                        {{ __('No governorate data available') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- City Distribution -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ __('By City') }}</h4>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-start text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-2/4">
                                    {{ __('City') }}
                                </th>
                                <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                    {{ __('Orders') }}
                                </th>
                                <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                    {{ __('Share (%)') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($cityData as $city)
                                @php
                                    $cityTotal = max(1, $cityData->sum('total'));
                                    $cityName = is_array($city['city'])
                                        ? ($city['city'][app()->getLocale()] ?? $city['city']['en'])
                                        : $city['city'];
                                    $cityUrl = route('filament.admin.resources.cities.orders', [
                                        'record' => $city['id'],
                                        'tableFilters[created_at][created_from]' => $this->fromDate->format('Y-m-d'),
                                        'tableFilters[created_at][created_until]' => $this->toDate->format('Y-m-d'),
                                    ]);
                                @endphp
                                <tr
                                    class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group cursor-pointer"
                                    onclick="window.location.href='{{ $cityUrl }}'"
                                >
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ $cityName }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ number_format($city['total']) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                        {{ round(($city['total'] / $cityTotal) * 100, 1) }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                        {{ __('No city data available') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
