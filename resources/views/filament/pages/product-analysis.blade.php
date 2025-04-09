<x-filament::page>
    <div class="space-y-6" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
        <!-- Enhanced Product Summary Card -->
        <x-filament::card class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl shadow-sm overflow-hidden border-0">
            <div class="relative">
                <!-- Decorative Elements -->
                <div class="absolute inset-0 opacity-10 dark:opacity-5">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxjaXJjbGUgZmlsbD0iIzAwMCIgZmlsbC1vcGFjaXR5PSIuMDgyIiBjeD0iMjAiIGN5PSIyMCIgcj0iMSIvPjwvZz48L3N2Zz4=')]"></div>
                </div>
                <div class="absolute top-0 {{ app()->isLocale('ar') ? 'left-0' : 'right-0 }} w-32 h-32 bg-blue-500/10 rounded-full filter blur-3xl"></div>

                <div class="relative z-10 flex flex-col md:flex-row gap-6 p-6">
                    <!-- Product Image -->
                    <div class="flex-shrink-0">
                        <div class="relative h-32 w-32 rounded-xl overflow-hidden shadow-lg ring-2 ring-white dark:ring-gray-700">
                            <img
                                src="{{ $product->getFirstMediaUrl('feature_product_image') }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/60 to-transparent">
                                <span class="text-xs font-semibold text-white">{{ __('product_analysis.product_id') }}: {{ $product->id }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="flex-1">
                        <div class="flex flex-col h-full">
                            <!-- Title and Date -->
                            <div class="mb-4">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $product->name }}
                                </h2>
                                <div class="flex items-center mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center bg-white/80 dark:bg-gray-700/80 px-3 py-1 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ app()->isLocale('ar') ? 'ml-1' : 'mr-1' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $fromDate->format('M d, Y') }} - {{ $toDate->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Stats Grid -->
                            <div class="mt-auto grid grid-cols-2 md:grid-cols-4 gap-3">
                                <!-- Total Ordered -->
                                <div class="bg-white/90 dark:bg-gray-700/90 backdrop-blur-sm rounded-xl p-4 shadow-xs border border-gray-200/50 dark:border-gray-600/30 hover:shadow-sm transition-shadow">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('product_analysis.total_ordered') }}
                                        </p>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ array_sum(array_column($sizeData, 'total')) }}
                                    </p>
                                    <div class="h-1 mt-2 bg-gradient-to-r from-blue-500 to-blue-300 rounded-full"></div>
                                </div>

                                <!-- Unique Sizes -->
                                <div class="bg-white/90 dark:bg-gray-700/90 backdrop-blur-sm rounded-xl p-4 shadow-xs border border-gray-200/50 dark:border-gray-600/30 hover:shadow-sm transition-shadow">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('product_analysis.unique_sizes') }}
                                        </p>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                        </svg>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ count($sizeData) }}
                                    </p>
                                    <div class="h-1 mt-2 bg-gradient-to-r from-green-500 to-green-300 rounded-full"></div>
                                </div>

                                <!-- Unique Colors -->
                                <div class="bg-white/90 dark:bg-gray-700/90 backdrop-blur-sm rounded-xl p-4 shadow-xs border border-gray-200/50 dark:border-gray-600/30 hover:shadow-sm transition-shadow">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('product_analysis.unique_colors') }}
                                        </p>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                        </svg>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ count($colorData) }}
                                    </p>
                                    <div class="h-1 mt-2 bg-gradient-to-r from-purple-500 to-purple-300 rounded-full"></div>
                                </div>

                                <!-- Countries -->
                                <div class="bg-white/90 dark:bg-gray-700/90 backdrop-blur-sm rounded-xl p-4 shadow-xs border border-gray-200/50 dark:border-gray-600/30 hover:shadow-sm transition-shadow">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            {{ __('product_analysis.countries') }}
                                        </p>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ count($countryData) }}
                                    </p>
                                    <div class="h-1 mt-2 bg-gradient-to-r from-amber-500 to-amber-300 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Size Distribution -->
        <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-0 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('product_analysis.size_distribution') }}
                    </h3>
                    <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full">
                        {{ __('product_analysis.total') }}: {{ array_sum(array_column($sizeData, 'total')) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('product_analysis.size') }}
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('product_analysis.quantity') }}
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        %
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($sizeData as $size)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            @php
                                                $sizeName = is_array($size['size']) ?
                                                    ($size['size'][app()->getLocale()] ?? $size['size']['en']) :
                                                    $size['size'];
                                            @endphp
                                            {{ $sizeName }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ $size['total'] }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ round($size['total']/array_sum(array_column($sizeData, 'total'))*100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-full">
                        <canvas id="sizeChart"></canvas>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Color Distribution -->
        <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-0 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('product_analysis.color_distribution') }}
                    </h3>
                    <span class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 px-2 py-1 rounded-full">
                        {{ __('product_analysis.total') }}: {{ array_sum(array_column($colorData, 'total')) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('product_analysis.color') }}
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('product_analysis.quantity') }}
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        %
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($colorData as $color)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                            <span class="w-4 h-4 rounded-full inline-block shadow-sm ring-1 ring-gray-200 dark:ring-gray-700" style="background-color: {{ $color['code'] }}"></span>
                                            @php
                                                $colorName = is_array($color['color']) ?
                                                    ($color['color'][app()->getLocale()] ?? $color['color']['en']) :
                                                    $color['color'];
                                            @endphp
                                            {{ $colorName }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ $color['total'] }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ round($color['total']/array_sum(array_column($colorData, 'total'))*100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-full">
                        <canvas id="colorChart"></canvas>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Geographical Distribution -->
        <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-0 overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-white">
                    {{ __('product_analysis.geographical_distribution') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Country Distribution -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('product_analysis.by_country') }}
                        </h4>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-64">
                            <canvas id="countryChart"></canvas>
                        </div>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('product_analysis.country') }}
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('product_analysis.qty') }}
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        %
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($countryData as $country)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            @php
                                                $countryName = is_array($country['country']) ?
                                                    ($country['country'][app()->getLocale()] ?? $country['country']['en']) :
                                                    $country['country'];
                                            @endphp
                                            {{ $countryName }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ $country['total'] }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ round($country['total']/array_sum(array_column($countryData, 'total'))*100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Governorate Distribution -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ __('product_analysis.by_governorate') }}
                        </h4>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-64">
                            <canvas id="governorateChart"></canvas>
                        </div>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('product_analysis.governorate') }}
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('product_analysis.qty') }}
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        %
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($governorateData as $governorate)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            @php
                                                $govName = is_array($governorate['governorate']) ?
                                                    ($governorate['governorate'][app()->getLocale()] ?? $governorate['governorate']['en']) :
                                                    $governorate['governorate'];
                                            @endphp
                                            {{ $govName }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ $governorate['total'] }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ round($governorate['total']/array_sum(array_column($governorateData, 'total'))*100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- City Distribution -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900 dark:text-white flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            {{ __('product_analysis.by_city') }}
                        </h4>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-64">
                            <canvas id="cityChart"></canvas>
                        </div>
                        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700 max-h-60 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('product_analysis.city') }}
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('product_analysis.qty') }}
                                    </th>
                                    <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        %
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($cityData as $city)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            @php
                                                $cityName = is_array($city['city']) ?
                                                    ($city['city'][app()->getLocale()] ?? $city['city']['en']) :
                                                    $city['city'];
                                            @endphp
                                            {{ $cityName }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ $city['total'] }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ round($city['total']/array_sum(array_column($cityData, 'total'))*100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Time Distribution -->
        <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-0 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('product_analysis.sales_over_time') }}
                    </h3>
                    <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full">
                        {{ __('product_analysis.daily_sales') }}
                    </span>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <canvas id="timeChart"></canvas>
                </div>
            </div>
        </x-filament::card>

        <!-- Status Distribution -->
        <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-0 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ __('product_analysis.order_status') }}
                    </h3>
                    <span class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200 px-2 py-1 rounded-full">
                        {{ __('product_analysis.status_distribution') }}
                    </span>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </x-filament::card>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Set RTL if Arabic
                const isRTL = document.documentElement.getAttribute('dir') === 'rtl';

                // Get current theme
                const isDark = document.documentElement.classList.contains('dark');

                // Chart text colors
                const textColor = isDark ? '#E5E7EB' : '#374151';
                const gridColor = isDark ? '#4B5563' : '#E5E7EB';
                const tooltipBg = isDark ? '#1F2937' : '#FFFFFF';
                const fontFamily = isRTL ? "'Tajawal', sans-serif" : "'Inter', sans-serif";

                // Common chart options
                const commonOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: textColor,
                                font: {
                                    family: fontFamily
                                },
                                rtl: isRTL
                            }
                        },
                        tooltip: {
                            backgroundColor: tooltipBg,
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: gridColor,
                            borderWidth: 1,
                            titleFont: {
                                family: fontFamily
                            },
                            bodyFont: {
                                family: fontFamily
                            },
                            rtl: isRTL
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor },
                            ticks: {
                                color: textColor,
                                font: {
                                    family: fontFamily
                                }
                            }
                        },
                        y: {
                            grid: { color: gridColor },
                            ticks: {
                                color: textColor,
                                font: {
                                    family: fontFamily
                                }
                            },
                            beginAtZero: true
                        }
                    }
                };

                // Initialize charts
                const sizeChart = initSizeChart();
                const colorChart = initColorChart();
                const countryChart = initCountryChart();
                const governorateChart = initGovernorateChart();
                const cityChart = initCityChart();
                const timeChart = initTimeChart();
                const statusChart = initStatusChart();

                function initSizeChart() {
                    return new Chart(document.getElementById('sizeChart'), {
                        type: 'pie',
                        data: {
                            labels: @json(collect($sizeData)->map(function($item) {
                                return is_array($item['size']) ?
                                    ($item['size'][app()->getLocale()] ?? $item['size']['en']) :
                                    $item['size'];
                            })),
                            datasets: [{
                                data: @json(array_column($sizeData, 'total')),
                                backgroundColor: [
                                    '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                                    '#EC4899', '#14B8A6', '#F97316', '#64748B', '#84CC16'
                                ],
                                borderColor: isDark ? '#1F2937' : '#FFFFFF',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            ...commonOptions,
                            plugins: {
                                ...commonOptions.plugins,
                                datalabels: {
                                    color: textColor,
                                    font: {
                                        family: fontFamily
                                    }
                                }
                            }
                        }
                    });
                }

                function initColorChart() {
                    return new Chart(document.getElementById('colorChart'), {
                        type: 'doughnut',
                        data: {
                            labels: @json(collect($colorData)->map(function($item) {
                                return is_array($item['color']) ?
                                    ($item['color'][app()->getLocale()] ?? $item['color']['en']) :
                                    $item['color'];
                            })),
                            datasets: [{
                                data: @json(array_column($colorData, 'total')),
                                backgroundColor: @json(array_column($colorData, 'code')),
                                borderColor: isDark ? '#1F2937' : '#FFFFFF',
                                borderWidth: 1
                            }]
                        },
                        options: commonOptions
                    });
                }

                function initCountryChart() {
                    return new Chart(document.getElementById('countryChart'), {
                        type: 'bar',
                        data: {
                            labels: @json(collect($countryData)->map(function($item) {
                                return is_array($item['country']) ?
                                    ($item['country'][app()->getLocale()] ?? $item['country']['en']) :
                                    $item['country'];
                            })),
                            datasets: [{
                                label: '{{ __('product_analysis.quantity') }}',
                                data: @json(array_column($countryData, 'total')),
                                backgroundColor: '#3B82F6',
                                borderColor: '#2563EB',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            ...commonOptions,
                            indexAxis: 'y',
                            plugins: {
                                ...commonOptions.plugins,
                                title: {
                                    display: true,
                                    text: '{{ __('product_analysis.by_country') }}',
                                    color: textColor,
                                    font: {
                                        family: fontFamily,
                                        size: 14
                                    }
                                }
                            }
                        }
                    });
                }

                function initGovernorateChart() {
                    return new Chart(document.getElementById('governorateChart'), {
                        type: 'bar',
                        data: {
                            labels: @json(collect($governorateData)->map(function($item) {
                                return is_array($item['governorate']) ?
                                    ($item['governorate'][app()->getLocale()] ?? $item['governorate']['en']) :
                                    $item['governorate'];
                            })),
                            datasets: [{
                                label: '{{ __('product_analysis.quantity') }}',
                                data: @json(array_column($governorateData, 'total')),
                                backgroundColor: '#10B981',
                                borderColor: '#059669',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            ...commonOptions,
                            indexAxis: 'y',
                            plugins: {
                                ...commonOptions.plugins,
                                title: {
                                    display: true,
                                    text: '{{ __('product_analysis.by_governorate') }}',
                                    color: textColor,
                                    font: {
                                        family: fontFamily,
                                        size: 14
                                    }
                                }
                            }
                        }
                    });
                }

                function initCityChart() {
                    return new Chart(document.getElementById('cityChart'), {
                        type: 'bar',
                        data: {
                            labels: @json(collect($cityData)->map(function($item) {
                                return is_array($item['city']) ?
                                    ($item['city'][app()->getLocale()] ?? $item['city']['en']) :
                                    $item['city'];
                            })),
                            datasets: [{
                                label: '{{ __('product_analysis.quantity') }}',
                                data: @json(array_column($cityData, 'total')),
                                backgroundColor: '#F59E0B',
                                borderColor: '#D97706',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            ...commonOptions,
                            indexAxis: 'y',
                            plugins: {
                                ...commonOptions.plugins,
                                title: {
                                    display: true,
                                    text: '{{ __('product_analysis.by_city') }}',
                                    color: textColor,
                                    font: {
                                        family: fontFamily,
                                        size: 14
                                    }
                                }
                            }
                        }
                    });
                }

                function initTimeChart() {
                    return new Chart(document.getElementById('timeChart'), {
                        type: 'line',
                        data: {
                            labels: @json(array_column($timeData, 'date')),
                            datasets: [{
                                label: '{{ __('product_analysis.quantity_sold') }}',
                                data: @json(array_column($timeData, 'total')),
                                borderColor: '#3B82F6',
                                backgroundColor: isDark ? '#1E40AF30' : '#3B82F620',
                                fill: true,
                                tension: 0.1,
                                borderWidth: 2,
                                pointBackgroundColor: '#3B82F6',
                                pointBorderColor: '#FFFFFF',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            ...commonOptions,
                            plugins: {
                                ...commonOptions.plugins,
                                title: {
                                    display: true,
                                    text: '{{ __('product_analysis.daily_sales') }}',
                                    color: textColor,
                                    font: {
                                        family: fontFamily,
                                        size: 14
                                    }
                                }
                            }
                        }
                    });
                }

                function initStatusChart() {
                    return new Chart(document.getElementById('statusChart'), {
                        type: 'polarArea',
                        data: {
                            labels: @json(array_column($statusData, 'status')),
                            datasets: [{
                                label: '{{ __('product_analysis.quantity') }}',
                                data: @json(array_column($statusData, 'total')),
                                backgroundColor: [
                                    '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                                    '#EC4899', '#14B8A6', '#F97316', '#64748B', '#84CC16'
                                ],
                                borderColor: isDark ? '#1F2937' : '#FFFFFF',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            ...commonOptions,
                            plugins: {
                                ...commonOptions.plugins,
                                title: {
                                    display: true,
                                    text: '{{ __('product_analysis.order_status_distribution') }}',
                                    color: textColor,
                                    font: {
                                        family: fontFamily,
                                        size: 14
                                    }
                                }
                            }
                        }
                    });
                }

                // Watch for theme changes
                const observer = new MutationObserver(() => {
                    const darkMode = document.documentElement.classList.contains('dark');
                    updateChartColors(darkMode);
                });

                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });

                function updateChartColors(isDark) {
                    const newTextColor = isDark ? '#E5E7EB' : '#374151';
                    const newGridColor = isDark ? '#4B5563' : '#E5E7EB';
                    const newTooltipBg = isDark ? '#1F2937' : '#FFFFFF';

                    // Update all charts
                    [sizeChart, colorChart, countryChart, governorateChart, cityChart, timeChart, statusChart].forEach(chart => {
                        if (chart) {
                            chart.options.plugins.legend.labels.color = newTextColor;
                            chart.options.plugins.tooltip.backgroundColor = newTooltipBg;
                            chart.options.plugins.tooltip.titleColor = newTextColor;
                            chart.options.plugins.tooltip.bodyColor = newTextColor;
                            chart.options.scales.x.grid.color = newGridColor;
                            chart.options.scales.x.ticks.color = newTextColor;
                            chart.options.scales.y.grid.color = newGridColor;
                            chart.options.scales.y.ticks.color = newTextColor;
                            chart.update();
                        }
                    });
                }
            });
        </script>
    @endpush
</x-filament::page>
