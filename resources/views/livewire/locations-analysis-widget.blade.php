<x-filament-widgets::widget>
    <x-filament::section class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Geographical Distribution') }}</h3>
                <span class="text-xs px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                    {{ $countryData->count() }} {{ __('countries') }},
                    {{ $governorateData->count() }} {{ __('governorates') }},
                    {{ $cityData->count() }} {{ __('cities') }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Country Distribution -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ __('By Country') }}</h4>
                        <span class="text-xs px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                            {{ __('Top') }} {{ min(10, $countryData->count()) }}
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
                                    {{ __('Orders') }}
                                </th>
                                <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                    {{ __('Share (%)') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($countryData->take(10) as $country)
                                @php
                                    $countryTotal = max(1, $countryData->sum('total'));
                                    $countryName = is_array($country['country'])
                                        ? ($country['country'][app()->getLocale()] ?? $country['country']['en'])
                                        : $country['country'];
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
                        <span class="text-xs px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                            {{ __('Top') }} {{ min(10, $governorateData->count()) }}
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
                                    {{ __('Orders') }}
                                </th>
                                <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                    {{ __('Share (%)') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($governorateData->take(10) as $governorate)
                                @php
                                    $governorateTotal = max(1, $governorateData->sum('total'));
                                    $govName = is_array($governorate['governorate'])
                                        ? ($governorate['governorate'][app()->getLocale()] ?? $governorate['governorate']['en'])
                                        : $governorate['governorate'];
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
                        <span class="text-xs px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200">
                            {{ __('Top') }} {{ min(10, $cityData->count()) }}
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
                                    {{ __('Orders') }}
                                </th>
                                <th class="px-4 py-3 text-end text-xs font-semibold tracking-wide text-gray-700 dark:text-gray-200 uppercase w-1/4">
                                    {{ __('Share (%)') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($cityData->take(10) as $city)
                                @php
                                    $cityTotal = max(1, $cityData->sum('total'));
                                    $cityName = is_array($city['city'])
                                        ? ($city['city'][app()->getLocale()] ?? $city['city']['en'])
                                        : $city['city'];
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

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
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
                                    return `${context.dataset.label || ''}: ${context.raw.toLocaleString()}`;
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

                // Country Chart
                const countryChart = new Chart(document.getElementById('countryChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($countryData->take(5)->map(function($item) {
                            return is_array($item['country'])
                                ? ($item['country'][app()->getLocale()] ?? $item['country']['en'])
                                : $item['country'];
                        })),
                        datasets: [{
                            label: '{{ __("Orders") }}',
                            data: @json($countryData->take(5)->pluck('total')),
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
                        labels: @json($governorateData->take(5)->map(function($item) {
                            return is_array($item['governorate'])
                                ? ($item['governorate'][app()->getLocale()] ?? $item['governorate']['en'])
                                : $item['governorate'];
                        })),
                        datasets: [{
                            label: '{{ __("Orders") }}',
                            data: @json($governorateData->take(5)->pluck('total')),
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
                        labels: @json($cityData->take(5)->map(function($item) {
                            return is_array($item['city'])
                                ? ($item['city'][app()->getLocale()] ?? $item['city']['en'])
                                : $item['city'];
                        })),
                        datasets: [{
                            label: '{{ __("Orders") }}',
                            data: @json($cityData->take(5)->pluck('total')),
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

                    const charts = [countryChart, governorateChart, cityChart];

                    charts.forEach(chart => {
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
                            }

                            chart.update();
                        }
                    });
                }

                // Livewire event listeners for chart updates
                Livewire.on('updateCharts', () => {
                    // Update country chart
                    countryChart.data.labels = @json($countryData->take(5)->map(function($item) {
                        return is_array($item['country'])
                            ? ($item['country'][app()->getLocale()] ?? $item['country']['en'])
                            : $item['country'];
                    }));
                    countryChart.data.datasets[0].data = @json($countryData->take(5)->pluck('total'));
                    countryChart.update();

                    // Update governorate chart
                    governorateChart.data.labels = @json($governorateData->take(5)->map(function($item) {
                        return is_array($item['governorate'])
                            ? ($item['governorate'][app()->getLocale()] ?? $item['governorate']['en'])
                            : $item['governorate'];
                    }));
                    governorateChart.data.datasets[0].data = @json($governorateData->take(5)->pluck('total'));
                    governorateChart.update();

                    // Update city chart
                    cityChart.data.labels = @json($cityData->take(5)->map(function($item) {
                        return is_array($item['city'])
                            ? ($item['city'][app()->getLocale()] ?? $item['city']['en'])
                            : $item['city'];
                    }));
                    cityChart.data.datasets[0].data = @json($cityData->take(5)->pluck('total'));
                    cityChart.update();
                });
            });
        </script>
    @endpush
</x-filament-widgets::widget>
