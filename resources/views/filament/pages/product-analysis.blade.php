<x-filament::page>
    <form wire:submit.prevent="submit" class="mb-6">
        <div>
            {{ $this->form }}
            <br>
            <x-filament::button type="submit">
                {{ __('Apply Filter') }}
            </x-filament::button>
        </div>
    </form>

    <div class="space-y-6">
        <x-filament::card class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden transition-all duration-300 shadow-xs hover:shadow-sm">
            <div class="flex flex-col md:flex-row gap-0">
                <!-- Image Section -->
                <div class="w-full md:w-1/3 p-6 flex items-center justify-center bg-gray-50 dark:bg-gray-800/20">
                    <div class="relative w-full h-64 md:h-full aspect-square rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                        @if($product->getFirstMediaUrl('feature_product_image'))
                            <img
                                src="{{ $product->getFirstMediaUrl('feature_product_image') }}"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-contain p-5 bg-white dark:bg-gray-800"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent pointer-events-none"></div>
                        @else
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Content Section -->
                <div class="w-full md:w-2/3 p-6 md:pl-6">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                        <div class="space-y-4">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
                                {{ $product->name }}
                            </h1>

                            <div class="flex flex-wrap items-center gap-3 mt-2 mb-8">
                                @if($product->sku)
                                    <span class="text-xs font-medium text-gray-700 dark:text-primary-200 bg-gray-100 dark:bg-gray-900/80 px-3 py-1.5 rounded-md border border-gray-200 dark:border-gray-700">
                                <span class="font-semibold">SKU:</span> {{ $product->sku }}
                            </span>
                                @endif

                                @if($product->category?->name)
                                    <span class="text-xs font-medium text-primary-700 dark:text-primary-200 bg-primary-50 dark:bg-primary-900/20 px-3 py-1.5 rounded-full border border-primary-100 dark:border-primary-900/30">
                                {{ $product->category->name }}
                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center text-sm text-gray-600 dark:text-primary-200 bg-gray-50 dark:bg-gray-900/80 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-700">
                            <svg class="w-4 h-4 mr-3 text-gray-500 dark:text-primary-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="whitespace-nowrap">{{ $fromDate->format('M d, Y') }} – {{ $toDate->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <!-- Stats Row -->
                    @php
                        $stats = [
                            ['title' => __('Total Ordered'), 'value' => number_format($totalOrderCount), 'color' => 'blue', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                            ['title' => __('Unique Sizes'), 'value' => count($sizeData), 'color' => 'emerald', 'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
                            ['title' => __('Color Variants'), 'value' => count($colorData), 'color' => 'purple', 'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
                            ['title' => __('Countries'), 'value' => count($countryData), 'color' => 'amber', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 gap-4 mt-8">
                        @foreach($stats as $stat)
                            <div class="flex items-center justify-between w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-6 py-4 shadow-sm hover:shadow-md transition duration-200">
                                <!-- Icon -->
                                <div class="flex items-center gap-4">
                                    <div class="p-3 rounded-full bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/20 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            {{ $stat['title'] }}
                                        </p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $stat['value'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </x-filament::card>

        <!-- Size Distribution - Updated Table Text Styles -->
        <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ __('Size Distribution') }}
                    </h3>
                    <span class="text-xs px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                {{ count($sizeData) }} {{ __('Sizes') }}
            </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Table --}}
                    <div class="order-2 md:order-1">
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-start text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-2/4">
                                        {{ __('Name') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Total Units') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Share (%)') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($sizeData as $size)
                                    @php
                                        $sizeName = is_array($size['size'])
                                            ? ($size['size'][app()->getLocale()] ?? $size['size']['en'])
                                            : $size['size'];
                                        $totalQty = array_sum(array_column($sizeData, 'total'));
                                        $percentage = $totalQty ? round($size['total'] / $totalQty * 100, 1) : 0;
                                    @endphp
                                    <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ $sizeName }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ number_format($size['total']) }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ $percentage }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                            {{ app()->getLocale() === 'ar' ? 'لا تتوفر أحجام مختلفة' : 'No different sizes available' }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Chart --}}
                    <div class="order-1 md:order-2 bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-64">
                        <canvas id="sizeChart"></canvas>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Color Distribution - Updated Table Text Styles -->
        <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Color Distribution') }}</h3>
                    <span class="text-xs px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                {{ count($colorData) }} {{ __('colors') }}
            </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="order-2 md:order-1">
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-start text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-2/4">
                                        {{ __('Color') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Quantity') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Share (%)') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($colorData as $color)
                                    @php
                                        $colorName = is_array($color['color']) ?
                                            ($color['color'][app()->getLocale()] ?? $color['color']['en']) :
                                            $color['color'];
                                        $percentage = round($color['total'] / array_sum(array_column($colorData, 'total')) * 100, 1);
                                    @endphp
                                    <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start group-hover:text-primary-600 dark:group-hover:text-primary-400 flex items-center gap-2">
                                            <span class="w-4 h-4 rounded-full inline-block shadow-sm ring-1 ring-gray-200 dark:ring-gray-600" style="background-color: {{ $color['code'] }}"></span>
                                            {{ $colorName }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ number_format($color['total']) }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ $percentage }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                            {{ app()->getLocale() === 'ar' ? 'لا تتوفر ألوان مختلفة' : 'No different colors available' }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="order-1 md:order-2 bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-64">
                        <canvas id="colorChart"></canvas>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Color-Size Combinations - Updated Table Text Styles -->
        <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Top Color-Size Combinations') }}</h3>
                    <span class="text-xs px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                {{ count($colorSizeData) }} {{ __('combinations') }}
            </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="order-2 md:order-1">
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-start text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">
                                        {{ __('Color') }}
                                    </th>
                                    <th class="px-6 py-3 text-start text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">
                                        {{ __('Size') }}
                                    </th>
                                    <th class="px-6 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">
                                        {{ __('Quantity') }}
                                    </th>
                                    <th class="px-6 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">
                                        {{ __('Orders') }}
                                    </th>
                                    <th class="px-6 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase">
                                        {{ __('Share (%)') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @php
                                    $totalQuantity = max(1, array_sum(array_column($colorSizeData, 'total_quantity')));
                                @endphp
                                @foreach($colorSizeData as $combo)
                                    @php
                                        $colorName = is_array($combo['color']) ?
                                            ($combo['color'][app()->getLocale()] ?? $combo['color']['en']) :
                                            $combo['color'];
                                        $sizeName = is_array($combo['size']) ?
                                            ($combo['size'][app()->getLocale()] ?? $combo['size']['en']) :
                                            $combo['size'];
                                    @endphp
                                    <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 text-start group-hover:text-primary-600 dark:group-hover:text-primary-400 flex items-center gap-2">
                                            <span class="w-4 h-4 rounded-full inline-block shadow-sm ring-1 ring-gray-200 dark:ring-gray-600" style="background-color: {{ $combo['color_code'] }}"></span>
                                            {{ $colorName }}
                                        </td>
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 text-start group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ $sizeName }}
                                        </td>
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ number_format($combo['total_quantity']) }}
                                        </td>
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ $combo['order_count'] }}
                                        </td>
                                        <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ round(($combo['total_quantity'] / $totalQuantity) * 100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="order-1 md:order-2 bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-64">
                        <canvas id="colorSizeChart"></canvas>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Geographical Distribution - Updated Table Text Styles -->
        <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Geographical Distribution') }}</h3>
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 shadow-sm ring-1 ring-primary-200 dark:ring-primary-800">
    <span class="flex items-center gap-1">
        <x-heroicon-o-globe-alt class="w-4 h-4" />
        {{ count($countryData) }} {{ __('countries') }}
    </span>
    <span class="opacity-50">•</span>
    <span class="flex items-center gap-1">
        <x-heroicon-o-map class="w-4 h-4" />
        {{ count($governorateData) }} {{ __('governorates') }}
    </span>
    <span class="opacity-50">•</span>
    <span class="flex items-center gap-1">
        <x-heroicon-o-map-pin class="w-4 h-4" />
        {{ count($cityData) }} {{ __('cities') }}
    </span>
</span>

                </div>
                <hr class="border-gray-200 dark:border-gray-700 mb-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Country Distribution -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ __('By Country') }}</h4>
                            <span class="text-xs px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                        {{ __('Top') }} {{ min(10, count($countryData)) }}
                    </span>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-64">
                            <canvas id="countryChart"></canvas>
                        </div>
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-start text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-2/4">
                                        {{ __('Country') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Qty') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Share (%)') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($countryData as $country)
                                    @php
                                        $countryTotal = max(1, array_sum(array_column($countryData, 'total')));
                                        $countryName = is_array($country['country']) ?
                                            ($country['country'][app()->getLocale()] ?? $country['country']['en']) :
                                            $country['country'];
                                    @endphp
                                    <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group">
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
                                            {{ app()->getLocale() === 'ar' ? 'لا توجد بيانات متاحة للبلدان' : 'No country data available' }}
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
                            <span class="text-xs px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                        {{ __('Top') }} {{ min(10, count($governorateData)) }}
                    </span>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-64">
                            <canvas id="governorateChart"></canvas>
                        </div>
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-start text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-2/4">
                                        {{ __('Governorate') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Qty') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Share (%)') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($governorateData as $governorate)
                                    @php
                                        $governorateTotal = max(1, array_sum(array_column($governorateData, 'total')));
                                        $govName = is_array($governorate['governorate']) ?
                                            ($governorate['governorate'][app()->getLocale()] ?? $governorate['governorate']['en']) :
                                            $governorate['governorate'];
                                    @endphp
                                    <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group">
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
                                            {{ app()->getLocale() === 'ar' ? 'لا توجد بيانات متاحة للمحافظات' : 'No governorate data available' }}
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
                            <span class="text-xs px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200">
                        {{ __('Top') }} {{ min(10, count($cityData)) }}
                    </span>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-64">
                            <canvas id="cityChart"></canvas>
                        </div>
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-start text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-2/4">
                                        {{ __('City') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Qty') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Share (%)') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($cityData as $city)
                                    @php
                                        $cityTotal = max(1, array_sum(array_column($cityData, 'total')));
                                        $cityName = is_array($city['city']) ?
                                            ($city['city'][app()->getLocale()] ?? $city['city']['en']) :
                                            $city['city'];
                                    @endphp
                                    <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group">
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
                                            {{ app()->getLocale() === 'ar' ? 'لا توجد بيانات متاحة للمدن' : 'No city data available' }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Time and Status Distribution Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Time Distribution -->
            <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('Sales Over Time') }}
                        </h3>
                        <span class="text-xs px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                    {{ count($timeData) }} {{ __('days') }}
                </span>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-80">
                        <canvas id="timeChart"></canvas>
                    </div>
                </div>
            </x-filament::card>

            <!-- Status Distribution -->
            <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('Order Status') }}
                        </h3>
                        <span class="text-xs px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                    {{ count($statusData) }} {{ __('statuses') }}
                </span>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700 h-80">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </x-filament::card>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Get current theme
                const isDark = document.documentElement.classList.contains('dark');

                // Chart text colors
                const textColor = isDark ? '#E5E7EB' : '#374151';
                const gridColor = isDark ? '#4B5563' : '#E5E7EB';
                const tooltipBg = isDark ? '#1F2937' : '#FFFFFF';
                const borderColor = isDark ? '#4B5563' : '#E5E7EB';

                // Common chart options
                const commonOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: textColor,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 12
                                },
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: tooltipBg,
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: borderColor,
                            borderWidth: 1,
                            padding: 12,
                            bodyFont: {
                                family: "'Inter', sans-serif",
                                size: 12
                            },
                            titleFont: {
                                family: "'Inter', sans-serif",
                                size: 12,
                                weight: 'bold'
                            },
                            cornerRadius: 8,
                            displayColors: true,
                            usePointStyle: true,
                            boxPadding: 6,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += new Intl.NumberFormat().format(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: gridColor,
                                drawBorder: false
                            },
                            ticks: {
                                color: textColor,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: gridColor,
                                drawBorder: false
                            },
                            ticks: {
                                color: textColor,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                },
                                callback: function(value) {
                                    if (value >= 1000) {
                                        return value / 1000 + 'k';
                                    }
                                    return value;
                                }
                            },
                            beginAtZero: true
                        }
                    },
                    elements: {
                        line: {
                            tension: 0.3,
                            borderWidth: 2
                        },
                        point: {
                            radius: 4,
                            hoverRadius: 6,
                            borderWidth: 2
                        },
                        bar: {
                            borderRadius: 4,
                            borderWidth: 0
                        }
                    },
                    animation: {
                        duration: 800,
                        easing: 'easeOutQuart'
                    }
                };

                // Size Chart
                const sizeChart = new Chart(document.getElementById('sizeChart'), {
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
                            tooltip: {
                                ...commonOptions.plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        return `Quantity: ${context.raw.toLocaleString()}`;
                                    }
                                }
                            },
                            datalabels: {
                                color: textColor
                            }
                        }
                    }
                });

                // Color Chart
                const colorChart = new Chart(document.getElementById('colorChart'), {
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
                    options: {
                        ...commonOptions,
                        cutout: '65%',
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                ...commonOptions.plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        return `Quantity: ${context.raw.toLocaleString()}`;
                                    }
                                }
                            },
                            legend: {
                                ...commonOptions.plugins.legend,
                                position: 'right'
                            }
                        }
                    }
                });

                // Color-Size Combination Chart

                const colorSizeChart = new Chart(document.getElementById('colorSizeChart'), {
                    type: 'bar',
                    data: {
                        labels: @json(collect($colorSizeData)->take(10)->map(function($item) {
            $colorName = is_array($item['color']) ?
                ($item['color'][app()->getLocale()] ?? $item['color']['en']) :
                $item['color'];
            $sizeName = is_array($item['size']) ?
                ($item['size'][app()->getLocale()] ?? $item['size']['en']) :
                $item['size'];
            return $colorName . ' - ' . $sizeName;
        })),
                        datasets: [{
                            label: '{{ __("Quantity") }}',
                            data: @json(collect($colorSizeData)->take(10)->pluck('total_quantity')),
                            backgroundColor: @json(collect($colorSizeData)->take(10)->map(function($item) {
                return $item['color_code'];
            })),
                            borderColor: isDark ? '#1F2937' : '#FFFFFF',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        ...commonOptions,
                        indexAxis: 'y',
                        plugins: {
                            ...commonOptions.plugins,
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return '{{ __("Quantity") }}: ' + context.parsed.x.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ...commonOptions.scales.x,
                                grid: {
                                    display: false
                                },
                                title: {
                                    display: true,
                                    text: '{{ __("Quantity") }}',
                                    color: textColor
                                }
                            },
                            y: {
                                ...commonOptions.scales.y,
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: textColor,
                                    font: {
                                        size: 11
                                    },
                                    callback: function(value, index, values) {
                                        const label = this.getLabelForValue(value);
                                        return label.length > 20 ? label.substring(0, 20) + '...' : label;
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                right: 20
                            }
                        }
                    }
                });
                // Country Chart
                const countryChart = new Chart(document.getElementById('countryChart'), {
                    type: 'bar',
                    data: {
                        labels: @json(collect($countryData)->take(5)->map(function($item) {
            return is_array($item['country'])
                ? ($item['country'][app()->getLocale()] ?? $item['country']['en'])
                : $item['country'];
        })),
                        datasets: [{
                            label: @json(__('charts.quantity')),
                            data: @json(collect($countryData)->take(5)->pluck('total')->map(function($value) {
                return (int)$value;
            })),
                            backgroundColor: '#3B82F6',
                            borderColor: '#2563EB',
                            borderWidth: 0
                        }]
                    },
                    options: {
                        ...commonOptions,
                        indexAxis: 'y',
                        plugins: {
                            ...commonOptions.plugins,
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `{{ __('charts.quantity') }}: ${context.raw.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            ...commonOptions.scales,
                            x: {
                                ...commonOptions.scales.x,
                                display: false
                            },
                            y: {
                                ...commonOptions.scales.y,
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

// Governorate Chart
                const governorateChart = new Chart(document.getElementById('governorateChart'), {
                    type: 'bar',
                    data: {
                        labels: @json(collect($governorateData)->take(5)->map(function($item) {
            return is_array($item['governorate'])
                ? ($item['governorate'][app()->getLocale()] ?? $item['governorate']['en'])
                : $item['governorate'];
        })),
                        datasets: [{
                            label: @json(__('charts.quantity')),
                            data: @json(collect($governorateData)->take(5)->pluck('total')->map(function($value) {
                return (int)$value;
            })),
                            backgroundColor: '#10B981',
                            borderColor: '#059669',
                            borderWidth: 0
                        }]
                    },
                    options: {
                        ...commonOptions,
                        indexAxis: 'y',
                        plugins: {
                            ...commonOptions.plugins,
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `{{ __('charts.quantity') }}: ${context.raw.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            ...commonOptions.scales,
                            x: {
                                ...commonOptions.scales.x,
                                display: false
                            },
                            y: {
                                ...commonOptions.scales.y,
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

// City Chart
                const cityChart = new Chart(document.getElementById('cityChart'), {
                    type: 'bar',
                    data: {
                        labels: @json(collect($cityData)->take(5)->map(function($item) {
            return is_array($item['city'])
                ? ($item['city'][app()->getLocale()] ?? $item['city']['en'])
                : $item['city'];
        })),
                        datasets: [{
                            label: @json(__('charts.quantity')),
                            data: @json(collect($cityData)->take(5)->pluck('total')->map(function($value) {
                return (int)$value;
            })),
                            backgroundColor: '#F59E0B',
                            borderColor: '#D97706',
                            borderWidth: 0
                        }]
                    },
                    options: {
                        ...commonOptions,
                        indexAxis: 'y',
                        plugins: {
                            ...commonOptions.plugins,
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `{{ __('charts.quantity') }}: ${context.raw.toLocaleString()}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            ...commonOptions.scales,
                            x: {
                                ...commonOptions.scales.x,
                                display: false
                            },
                            y: {
                                ...commonOptions.scales.y,
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

// Time Chart
                const timeChart = new Chart(document.getElementById('timeChart'), {
                    type: 'line',
                    data: {
                        labels: @json(array_column($timeData, 'date')),
                        datasets: [{
                            label: @json(__('charts.quantity_sold')),
                            data: @json(array_column($timeData, 'total')),
                            borderColor: '#3B82F6',
                            backgroundColor: isDark ? '#1E40AF30' : '#3B82F620',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 2,
                            pointBackgroundColor: '#3B82F6',
                            pointBorderColor: isDark ? '#1F2937' : '#FFFFFF',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: '#FFFFFF',
                            pointHoverBorderColor: '#3B82F6',
                            pointHoverBorderWidth: 2,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                ...commonOptions.plugins.tooltip,
                                callbacks: {
                                    title: function(context) {
                                        return `{{ __('charts.date') }}: ${context[0].label}`;
                                    },
                                    label: function(context) {
                                        return `{{ __('charts.quantity') }}: ${context.parsed.y.toLocaleString()}`;
                                    }
                                }
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            ...commonOptions.scales,
                            x: {
                                ...commonOptions.scales.x,
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Status Chart
                const statusChart = new Chart(document.getElementById('statusChart'), {
                    type: 'polarArea',
                    data: {
                        labels: @json(array_map(fn($status) => \App\Enums\OrderStatus::from($status)->getLabel(), array_column($statusData, 'status'))),
                        datasets: [{
                            label: @json(__('charts.quantity')),
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
                            tooltip: {
                                ...commonOptions.plugins.tooltip,
                                callbacks: {
                                    title: function(context) {
                                        return `{{ __('charts.status') }}: ${context[0].label}`;
                                    },
                                    label: function(context) {
                                        return `{{ __('charts.quantity') }}: ${context.raw.toLocaleString()}`;
                                    }
                                }
                            },
                            legend: {
                                position: 'right'
                            }
                        },
                        scales: {
                            r: {
                                grid: {
                                    color: gridColor
                                },
                                ticks: {
                                    display: false,
                                    backdropColor: 'transparent'
                                },
                                pointLabels: {
                                    display: false
                                }
                            }
                        }
                    }
                });

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
                    const newBorderColor = isDark ? '#4B5563' : '#E5E7EB';

                    // Update all charts
                    const charts = {
                        sizeChart,
                        colorChart,
                        countryChart,
                        governorateChart,
                        cityChart,
                        timeChart,
                        statusChart,
                        colorSizeChart
                    };

                    Object.values(charts).forEach(chart => {
                        if (chart) {
                            // Update options
                            chart.options.plugins.legend.labels.color = newTextColor;
                            chart.options.plugins.tooltip.backgroundColor = newTooltipBg;
                            chart.options.plugins.tooltip.titleColor = newTextColor;
                            chart.options.plugins.tooltip.bodyColor = newTextColor;
                            chart.options.plugins.tooltip.borderColor = newBorderColor;

                            // Update scales
                            if (chart.options.scales) {
                                if (chart.options.scales.x) {
                                    chart.options.scales.x.grid.color = newGridColor;
                                    chart.options.scales.x.ticks.color = newTextColor;
                                }
                                if (chart.options.scales.y) {
                                    chart.options.scales.y.grid.color = newGridColor;
                                    chart.options.scales.y.ticks.color = newTextColor;
                                }
                                if (chart.options.scales.r) {
                                    chart.options.scales.r.grid.color = newGridColor;
                                }
                            }

                            // For line chart, update point border color
                            if (chart.config.type === 'line') {
                                chart.data.datasets[0].pointBorderColor = isDark ? '#1F2937' : '#FFFFFF';
                            }

                            // For polar area, update border color
                            if (chart.config.type === 'polarArea') {
                                chart.data.datasets[0].borderColor = isDark ? '#1F2937' : '#FFFFFF';
                            }

                            chart.update();
                        }
                    });
                }
            });
        </script>
        <style>
            /* Custom scrollbar for tables */
            .overflow-y-auto::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            .overflow-y-auto::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.05);
                border-radius: 3px;
            }
            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.2);
                border-radius: 3px;
            }
            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: rgba(0, 0, 0, 0.3);
            }
            .dark .overflow-y-auto::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.05);
            }
            .dark .overflow-y-auto::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.2);
            }
            .dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            /* Smooth transitions */
            .transition-colors {
                transition-property: background-color, border-color, color, fill, stroke;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 150ms;
            }

            /* Card hover effects */
            .hover\\:shadow-sm:hover {
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            .dark .hover\\:shadow-sm:hover {
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2);
            }

            /* Table row hover */
            .hover\\:bg-gray-50:hover {
                background-color: rgba(249, 250, 251, 0.8);
            }
            .dark .hover\\:bg-gray-700\\/50:hover {
                                               background-color: rgba(55, 65, 81, 0.5);
                                           }
        </style>
    @endpush
</x-filament::page>
