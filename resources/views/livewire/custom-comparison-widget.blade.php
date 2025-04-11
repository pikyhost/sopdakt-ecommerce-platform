<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 gap-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $data = $this->getRevenueData();
                    $period1 = $data['period1'];
                    $period2 = $data['period2'];
                    $change = $period1['total'] - $period2['total'];
                    $percentage = $period2['total'] > 0 ? ($change / $period2['total']) * 100 : 0;
                @endphp

                <x-filament::card>
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ __('widgets.comparison.period_label', ['number' => 1]) }}: {{ $period1['label'] }}
                        </h3>
                        <p class="text-2xl font-bold mt-2">${{ number_format($period1['total'] / 100, 2) }}</p>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ __('widgets.comparison.period_label', ['number' => 2]) }}: {{ $period2['label'] }}
                        </h3>
                        <p class="text-2xl font-bold mt-2">${{ number_format($period2['total'] / 100, 2) }}</p>
                    </div>
                </x-filament::card>
            </div>

            <!-- Comparison Summary -->
            <x-filament::card>
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('widgets.comparison.summary_title') }}</h3>
                    <div class="mt-2">
                        @if($change >= 0)
                            <p class="text-green-600 font-bold">
                                <span class="text-xl">+${{ number_format(abs($change) / 100, 2) }}</span>
                                <span>(+{{ number_format(abs($percentage), 2) }}%)</span>
                                {{ __('widgets.comparison.increase_comparison') }}
                            </p>
                        @else
                            <p class="text-red-600 font-bold">
                                <span class="text-xl">-${{ number_format(abs($change) / 100, 2) }}</span>
                                <span>(-{{ number_format(abs($percentage), 2) }}%)</span>
                                {{ __('widgets.comparison.decrease_comparison') }}
                            </p>
                        @endif
                    </div>
                </div>
            </x-filament::card>

            <!-- Spline Area Chart -->
            <div wire:ignore class="bg-white rounded-lg shadow p-4">
                <div id="revenueComparisonChart"></div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script>
                document.addEventListener('livewire:initialized', () => {
                    // Get translated strings for chart
                    const chartTranslations = {
                        revenue: "{{ __('widgets.comparison.chart_revenue') }}",
                        period: (number) => `${number} {{ __('widgets.comparison.period') }}`,
                        dateFormat: "{{ __('widgets.comparison.date_format') }}",
                        currencySymbol: "{{ __('widgets.comparison.currency_symbol') }}"
                    };

                    // Initialize chart variable
                    let revenueComparisonChart;

                    // Function to format date strings to timestamps
                    const formatSeriesData = (series) => {
                        return series.map(item => ({
                            x: new Date(item.x).getTime(),
                            y: item.y
                        }));
                    };

                    // Initialize chart
                    const initChart = (data) => {
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
                                height: 350,
                                type: 'area',
                                toolbar: {
                                    show: true,
                                    tools: {
                                        zoom: chartTranslations.zoom,
                                        zoomin: chartTranslations.zoomin,
                                        zoomout: chartTranslations.zoomout,
                                        pan: chartTranslations.pan,
                                        reset: chartTranslations.reset
                                    }
                                },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800,
                                    animateGradually: {
                                        enabled: true,
                                        delay: 150
                                    },
                                    dynamicAnimation: {
                                        enabled: true,
                                        speed: 350
                                    }
                                }
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
                            xaxis: {
                                type: 'datetime',
                                labels: {
                                    format: chartTranslations.dateFormat
                                }
                            },
                            yaxis: {
                                title: {
                                    text: chartTranslations.revenue
                                },
                                labels: {
                                    formatter: function(value) {
                                        return chartTranslations.currencySymbol + value.toFixed(2);
                                    }
                                }
                            },
                            tooltip: {
                                x: {
                                    format: 'dd MMM yyyy'
                                },
                                y: {
                                    formatter: function(val) {
                                        return chartTranslations.currencySymbol + val.toFixed(2);
                                    }
                                }
                            },
                            colors: ['#3b82f6', '#10b981'],
                            legend: {
                                position: 'top'
                            }
                        };

                        // Destroy existing chart if it exists
                        if (revenueComparisonChart) {
                            revenueComparisonChart.destroy();
                        }

                        // Create new chart
                        revenueComparisonChart = new ApexCharts(
                            document.querySelector("#revenueComparisonChart"),
                            options
                        );
                        revenueComparisonChart.render();
                    };

                    // Initial chart render with current data
                    initChart(@json($this->getRevenueData()));

                    // Listen for update events
                    Livewire.on('updateChart', (event) => {
                        // Update chart with new data
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

                        // Also update x-axis if needed
                        revenueComparisonChart.updateOptions({
                            xaxis: {
                                type: 'datetime'
                            }
                        });
                    });
                });
            </script>
        @endpush
    </x-filament::section>
</x-filament-widgets::widget>
