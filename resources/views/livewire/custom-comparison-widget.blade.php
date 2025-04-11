<x-filament-widgets::widget>
    <x-filament::section class="!p-0 !border-0 !shadow-none dark:!bg-gray-900/50">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Sales Comparison') }}</h3>
        </div>

        <hr class="border-gray-200 dark:border-gray-700 mb-6">
        <div class="grid grid-cols-1 gap-4 lg:gap-5">
            <!-- Summary Cards - Compact Layout -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 px-4 sm:px-5">
                @php
                    $data = $this->getRevenueData();
                    $period1 = $data['period1'] ?? [];
                    $period2 = $data['period2'] ?? [];
                    $change = ($period1['total'] ?? 0) - ($period2['total'] ?? 0);

                    // Handle division by zero and cases where previous period is zero
                    $percentage = 0;
                    if (($period2['total'] ?? 0) != 0) {
                        $percentage = ($change / $period2['total']) * 100;
                    } elseif (($period1['total'] ?? 0) != 0) {
                        // If previous period was 0 and current has value, show infinite growth
                        $percentage = 100; // or any other value you want to represent "new" growth
                    }

                    $currency = \App\Models\Setting::getCurrency();
                    $currencyCode = $currency ? $currency->code : 'USD';
                    $isPositive = $change >= 0;
                    $dailyAvg = isset($period1['daily']) ? array_sum(array_column($period1['daily'], 'y')) / max(1, count($period1['daily'])) : 0;
                @endphp

                    <!-- Current Period Card -->
                <x-filament::card class="h-full !rounded-lg !shadow-sm hover:shadow transition-shadow duration-200 dark:!bg-gray-800 dark:!border-gray-700">
                    <div class="p-3 sm:p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-300">Current Period</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                                    {{ $currencyCode }} {{ isset($period1['total']) ? number_format($period1['total'], 2) : '0.00' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $period1['label'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-1.5 rounded-full bg-gray-100 dark:bg-gray-700/80">
                                @if ($isPositive)
                                    <x-heroicon-o-arrow-trending-up class="w-4 h-4 text-gray-600 dark:text-blue-400" />
                                @else
                                    <x-heroicon-o-arrow-trending-down class="w-4 h-4 text-gray-600 dark:text-red-400" />
                                @endif
                            </div>
                        </div>
                    </div>
                </x-filament::card>

                <!-- Growth Rate Card -->
                <x-filament::card class="h-full !rounded-lg !shadow-sm hover:shadow transition-shadow duration-200 dark:!bg-gray-800 dark:!border-gray-700">
                    <div class="p-3 sm:p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-300">Growth Rate</p>
                                <p class="text-lg font-semibold {{ $isPositive ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                                    {{ $isPositive ? '+' : '' }}{{ number_format($percentage, 2) }}%
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">vs previous period</p>
                            </div>
                            <div class="p-1.5 rounded-full bg-gray-50 dark:bg-gray-700/80">
                                <x-heroicon-o-chart-bar class="w-4 h-4 text-gray-600 dark:text-gray-300" />
                            </div>
                        </div>
                    </div>
                </x-filament::card>

                <!-- Previous Period Card -->
                <x-filament::card class="h-full !rounded-lg !shadow-sm hover:shadow transition-shadow duration-200 dark:!bg-gray-800 dark:!border-gray-700">
                    <div class="p-3 sm:p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-300">Previous Period</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
                                    {{ $currencyCode }} {{ isset($period2['total']) ? number_format($period2['total'], 2) : '0.00' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $period2['label'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-1.5 rounded-full bg-gray-50 dark:bg-gray-700/80">
                                <x-heroicon-o-clock class="w-4 h-4 text-gray-600 dark:text-gray-300" />
                            </div>
                        </div>
                    </div>
                </x-filament::card>
            </div>

            <!-- Enhanced Chart Section -->
            <div wire:ignore class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="flex justify-between items-center px-4 py-3 border-b border-gray-200 dark:border-gray-600 bg-gray-50/80 dark:bg-gray-700/70 backdrop-blur-sm">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 tracking-tight">
                        Sales ({{ $currencyCode }})
                    </h3>
                </div>
                <div id="revenueComparisonChart" class="px-2 pt-2 pb-1 min-h-[350px]"></div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script>
                document.addEventListener('livewire:initialized', () => {
                    let revenueComparisonChart;
                    let resizeTimer;

                    const formatSeriesData = (series) => {
                        return series.map(item => ({
                            x: new Date(item.x).getTime(),
                            y: item.y
                        }));
                    };

                    const initChart = (data) => {
                        const currencyCode = '{{ $currencyCode }}';
                        const isDark = document.documentElement.classList.contains('dark');

                        const options = {
                            series: [
                                {
                                    name: data.period1.label,
                                    data: formatSeriesData(data.period1.daily)
                                },
                                {
                                    name: data.period2.label,
                                    data: formatSeriesData(data.period2.daily)
                                }
                            ],
                            chart: {
                                height: '100%',
                                type: 'area',
                                toolbar: {
                                    show: true,
                                    tools: {
                                        zoom: true,
                                        zoomin: true,
                                        zoomout: true,
                                        pan: true,
                                        reset: true
                                    },
                                    autoSelected: 'zoom'
                                },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                },
                                redrawOnParentResize: true,
                                redrawOnWindowResize: true,
                                foreColor: isDark ? '#9CA3AF' : '#6B7280',
                                background: 'transparent',
                                toolbar: {
                                    tools: {
                                        download: true,
                                        selection: true,
                                        zoom: true,
                                        zoomin: true,
                                        zoomout: true,
                                        pan: true,
                                        reset: true
                                    }
                                }
                            },
                            theme: {
                                mode: isDark ? 'dark' : 'light'
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                                width: 2
                            },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.7,
                                    opacityTo: 0.3,
                                    stops: [0, 90, 100]
                                }
                            },
                            grid: {
                                borderColor: isDark ? '#374151' : '#E5E7EB',
                                strokeDashArray: 4,
                                xaxis: {
                                    lines: {
                                        show: false
                                    }
                                },
                                yaxis: {
                                    lines: {
                                        show: true
                                    }
                                }
                            },
                            xaxis: {
                                type: 'datetime',
                                labels: {
                                    format: 'dd MMM',
                                    style: {
                                        colors: isDark ? '#9CA3AF' : '#6B7280',
                                        fontSize: '10px',
                                        fontFamily: 'inherit'
                                    }
                                },
                                axisBorder: {
                                    show: true,
                                    color: isDark ? '#374151' : '#E5E7EB'
                                },
                                axisTicks: {
                                    color: isDark ? '#374151' : '#E5E7EB'
                                }
                            },
                            yaxis: {
                                labels: {
                                    formatter: function(value) {
                                        return `${currencyCode} ` + value.toFixed(2);
                                    },
                                    style: {
                                        colors: isDark ? '#9CA3AF' : '#6B7280',
                                        fontSize: '10px',
                                        fontFamily: 'inherit'
                                    }
                                },
                                axisBorder: {
                                    show: true,
                                    color: isDark ? '#374151' : '#E5E7EB'
                                }
                            },
                            tooltip: {
                                theme: isDark ? 'dark' : 'light',
                                x: {
                                    format: 'dd MMM yyyy'
                                },
                                y: {
                                    formatter: function(val) {
                                        return `${currencyCode} ` + val.toFixed(2);
                                    }
                                },
                                style: {
                                    fontSize: '12px',
                                    fontFamily: 'inherit'
                                }
                            },
                            colors: isDark ? ['#60A5FA', '#34D399'] : ['#3B82F6', '#10B981'],
                            legend: {
                                position: 'top',
                                labels: {
                                    colors: isDark ? '#E5E7EB' : '#111827',
                                    useSeriesColors: false
                                },
                                itemMargin: {
                                    horizontal: 10,
                                    vertical: 5
                                },
                                markers: {
                                    width: 10,
                                    height: 10,
                                    radius: 4
                                }
                            },
                            responsive: [{
                                breakpoint: 640,
                                options: {
                                    chart: {
                                        toolbar: {
                                            show: true,
                                            tools: {
                                                zoom: false,
                                                zoomin: false,
                                                zoomout: false,
                                                pan: false,
                                                reset: true
                                            }
                                        }
                                    },
                                    legend: {
                                        position: 'bottom',
                                        horizontalAlign: 'center'
                                    }
                                }
                            }]
                        };

                        if (revenueComparisonChart) {
                            revenueComparisonChart.destroy();
                        }

                        revenueComparisonChart = new ApexCharts(
                            document.querySelector("#revenueComparisonChart"),
                            options
                        );
                        revenueComparisonChart.render();
                    };

                    // Initial render
                    initChart(@json($this->getRevenueData()));

                    // Handle chart updates
                    Livewire.on('updateChart', (event) => {
                        revenueComparisonChart.updateSeries([
                            {
                                name: event.data.period1.label,
                                data: formatSeriesData(event.data.period1.daily)
                            },
                            {
                                name: event.data.period2.label,
                                data: formatSeriesData(event.data.period2.daily)
                            }
                        ]);
                    });

                    // Handle dark mode changes
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.attributeName === 'class') {
                                initChart(@json($this->getRevenueData()));
                            }
                        });
                    });

                    observer.observe(document.documentElement, {
                        attributes: true,
                        attributeFilter: ['class']
                    });

                    // Handle window resize with debounce
                    window.addEventListener('resize', function() {
                        clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(function() {
                            if (revenueComparisonChart) {
                                revenueComparisonChart.updateOptions({
                                    chart: {
                                        width: '100%'
                                    }
                                });
                            }
                        }, 200);
                    });
                });
            </script>
        @endpush
    </x-filament::section>
</x-filament-widgets::widget>
