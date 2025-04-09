<x-filament::page>
    <form wire:submit.prevent="submit" class="mb-6">
        <div>
            {{ $this->form }}
            <br>
            <x-filament::button type="submit">
                Apply Filter
            </x-filament::button>
        </div>
    </form>
    <div class="space-y-4">
        <!-- Product Summary Card -->
        <div class="mb-4 text-right" dir="rtl">
            <div class="flex items-center text-blue-600 hover:text-blue-800 transition duration-200 ease-in-out">
                <a href="{{ url()->previous() }}" class="flex items-center hover:underline go-back-link" onclick="disableLink(this)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19l7-7-7-7" />
                    </svg>
                    <span class="font-medium go-back-text">
                {{ \Illuminate\Support\Facades\App::getLocale() == 'ar' ? 'العودة للخلف' : "Go Back" }}
            </span>
                </a>
            </div>
        </div>

        <!-- The Product Card -->
        <x-filament::card class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-md border-0 overflow-hidden">
            <div class="relative">
                <!-- Decorative Background Pattern -->
                <div class="absolute inset-0 opacity-10 dark:opacity-5 pointer-events-none select-none">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxjaXJjbGUgZmlsbD0iIzAwMCIgZmlsbC1vcGFjaXR5PSIuMDgyIiBjeD0iMjAiIGN5PSIyMCIgcj0iMSIvPjwvZz48L3N2Zz4=')]"></div>
                </div>

                <div class="relative z-10 flex flex-col md:flex-row gap-6 p-6 md:p-8">
                    <!-- Product Image -->
                    <div class="flex-shrink-0 self-center md:self-start">
                        <div class="relative h-36 w-36 md:h-40 md:w-40 rounded-xl overflow-hidden shadow-lg ring-2 ring-white/50 dark:ring-gray-700/50">
                            <img
                                src="{{ $product->getFirstMediaUrl('feature_product_image') }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/60 to-transparent">
                                <p class="text-xs font-medium text-white truncate">{{ $product->sku }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="flex-1 flex flex-col justify-between">
                        <!-- Title and Meta -->
                        <div class="mb-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white leading-tight">
                                    {{ $product->name }}
                                </h2>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200 whitespace-nowrap">
                            {{ $product->category->name }}
                        </span>
                            </div>

                            <!-- Date with Better Icon and Spacing -->
                            <div class="flex items-center gap-2 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 2v2m8-2v2M3 8h18M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z" />
                                </svg>
                                <span class="font-medium tracking-wide">
                            {{ $fromDate->format('M d, Y') }} – {{ $toDate->format('M d, Y') }}
                        </span>
                            </div>
                        </div>

                        <!-- Stats Section -->
                        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @php
                                $stats = [
                                    [
                                        'label' => 'Total Ordered',
                                        'value' => number_format(array_sum(array_column($sizeData, 'total'))),
                                        'color' => 'from-blue-500 to-blue-300',
                                    ],
                                    [
                                        'label' => 'Unique Sizes',
                                        'value' => count($sizeData),
                                        'color' => 'from-green-500 to-green-300',
                                    ],
                                    [
                                        'label' => 'Unique Colors',
                                        'value' => count($colorData),
                                        'color' => 'from-purple-500 to-purple-300',
                                    ],
                                    [
                                        'label' => 'Countries',
                                        'value' => count($countryData),
                                        'color' => 'from-amber-500 to-amber-300',
                                    ],
                                ];
                            @endphp

                            @foreach ($stats as $stat)
                                <div class="bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm rounded-xl p-4 shadow-sm border border-gray-200/50 dark:border-gray-600/30 transition-shadow hover:shadow-md">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ $stat['label'] }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                                    <div class="h-1 mt-2 bg-gradient-to-r {{ $stat['color'] }} rounded-full"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::card>

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
                                        {{ __('Label') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Total Units') }}
                                    </th>
                                    <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                        {{ __('Share (%)') }}
                                    </th>
                                </tr>

                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($sizeData as $size)
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
                                @endforeach
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
                                @foreach($colorData as $color)
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
                                @endforeach
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
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Geographical Distribution') }}</h3>
                    <span class="text-xs px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                {{ count($countryData) }} {{ __('countries') }},
                {{ count($governorateData) }} {{ __('governorates') }},
                {{ count($cityData) }} {{ __('cities') }}
            </span>
                </div>

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
                                @php
                                    $countryTotal = max(1, array_sum(array_column($countryData, 'total')));
                                @endphp
                                @foreach($countryData as $country)
                                    <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            @php
                                                $countryName = is_array($country['country']) ?
                                                    ($country['country'][app()->getLocale()] ?? $country['country']['en']) :
                                                    $country['country'];
                                            @endphp
                                            {{ $countryName }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ number_format($country['total']) }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ round(($country['total'] / $countryTotal) * 100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
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
                                @php
                                    $governorateTotal = max(1, array_sum(array_column($governorateData, 'total')));
                                @endphp
                                @foreach($governorateData as $governorate)
                                    <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            @php
                                                $govName = is_array($governorate['governorate']) ?
                                                    ($governorate['governorate'][app()->getLocale()] ?? $governorate['governorate']['en']) :
                                                    $governorate['governorate'];
                                            @endphp
                                            {{ $govName }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ number_format($governorate['total']) }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ round(($governorate['total'] / $governorateTotal) * 100, 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
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
                                @php
                                    $cityTotal = max(1, array_sum(array_column($cityData, 'total')));
                                @endphp
                                @foreach($cityData as $city)
                                    <tr class="transition-colors hover:bg-gray-100 dark:hover:bg-gray-700 group">
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-start group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            @php
                                                $cityName = is_array($city['city']) ?
                                                    ($city['city'][app()->getLocale()] ?? $city['city']['en']) :
                                                    $city['city'];
                                            @endphp
                                            {{ $cityName }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ number_format($city['total']) }}
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-end group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                            {{ round(($city['total'] / $cityTotal) * 100, 1) }}%
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

        <!-- Time and Status Distribution Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Time Distribution -->
            <x-filament::card class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sales Over Time</h3>
                        <span class="text-xs px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                            {{ count($timeData) }} days
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
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Status</h3>
                        <span class="text-xs px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                            {{ count($statusData) }} statuses
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
            // Create label combining color and size
            $colorName = is_array($item['color']) ?
                ($item['color'][app()->getLocale()] ?? $item['color']['en']) :
                $item['color'];
            $sizeName = is_array($item['size']) ?
                ($item['size'][app()->getLocale()] ?? $item['size']['en']) :
                $item['size'];
            return $colorName . ' - ' . $sizeName; // Use PHP concatenation (.) instead of JS (+)
        })),
                        datasets: [{
                            label: 'Quantity',
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
                                        return `Quantity: ${context.parsed.x.toLocaleString()}`;
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
                                    text: 'Quantity',
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
                                        // Truncate long labels to prevent overlapping
                                        const label = this.getLabelForValue(value);
                                        return label.length > 20 ? label.substring(0, 20) + '...' : label;
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                right: 20 // Add padding to prevent label cutoff
                            }
                        }
                    }
                });

                // Country Chart
                const countryChart = new Chart(document.getElementById('countryChart'), {
                    type: 'bar',
                    data: {
                        labels: @json(collect($countryData)->take(5)->map(function($item) {
            return is_array($item['country']) ?
                ($item['country'][app()->getLocale()] ?? $item['country']['en']) :
                $item['country'];
        })),
                        datasets: [{
                            label: 'Quantity',
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
                                        return `Quantity: ${context.raw.toLocaleString()}`;
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
            return is_array($item['governorate']) ?
                ($item['governorate'][app()->getLocale()] ?? $item['governorate']['en']) :
                $item['governorate'];
        })),
                        datasets: [{
                            label: 'Quantity',
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
                                        return `Quantity: ${context.raw.toLocaleString()}`;
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
            return is_array($item['city']) ?
                ($item['city'][app()->getLocale()] ?? $item['city']['en']) :
                $item['city'];
        })),
                        datasets: [{
                            label: 'Quantity',
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
                                        return `Quantity: ${context.raw.toLocaleString()}`;
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
                            label: 'Quantity Sold',
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
                            pointHoverBorderWidth: 2
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
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
                        labels: @json(array_column($statusData, 'status')),
                        datasets: [{
                            label: 'Quantity',
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
