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
                        <h3 class="text-lg font-medium text-gray-900">Period 1: {{ $period1['label'] }}</h3>
                        <p class="text-2xl font-bold mt-2">${{ number_format($period1['total'] / 100, 2) }}</p>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-gray-900">Period 2: {{ $period2['label'] }}</h3>
                        <p class="text-2xl font-bold mt-2">${{ number_format($period2['total'] / 100, 2) }}</p>
                    </div>
                </x-filament::card>
            </div>

            <!-- Comparison Summary -->
            <x-filament::card>
                <div class="p-4">
                    <h3 class="text-lg font-medium text-gray-900">Comparison Summary</h3>
                    <div class="mt-2">
                        @if($change >= 0)
                            <p class="text-green-600 font-bold">
                                <span class="text-xl">+${{ number_format(abs($change) / 100, 2) }}</span>
                                <span>(+{{ number_format(abs($percentage), 2) }}%)</span>
                                increase compared to Period 2
                            </p>
                        @else
                            <p class="text-red-600 font-bold">
                                <span class="text-xl">-${{ number_format(abs($change) / 100, 2) }}</span>
                                <span>(-{{ number_format(abs($percentage), 2) }}%)</span>
                                decrease compared to Period 2
                            </p>
                        @endif
                    </div>
                </div>
            </x-filament::card>

            <!-- Chart -->
            <div wire:ignore class="bg-white rounded-lg shadow p-4">
                <div id="revenueComparisonChart"></div>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script>
                document.addEventListener('livewire:initialized', () => {
                    const initChart = () => {
                        const data = @json($this->getRevenueData());

                        // Prepare series data
                        const period1Dates = data.period1.daily.map(item => item.date);
                        const period2Dates = data.period2.daily.map(item => item.date);

                        // Combine all unique dates
                        const allDates = [...new Set([...period1Dates, ...period2Dates])].sort();

                        // Map data to consistent dates
                        const period1Series = allDates.map(date => {
                            const found = data.period1.daily.find(item => item.date === date);
                            return found ? (found.revenue / 100) : 0;
                        });

                        const period2Series = allDates.map(date => {
                            const found = data.period2.daily.find(item => item.date === date);
                            return found ? (found.revenue / 100) : 0;
                        });

                        const options = {
                            series: [
                                {
                                    name: data.period1.label,
                                    data: period1Series
                                },
                                {
                                    name: data.period2.label,
                                    data: period2Series
                                }
                            ],
                            chart: {
                                type: 'bar',
                                height: 350,
                                stacked: false,
                                toolbar: {
                                    show: true
                                },
                                zoom: {
                                    enabled: true
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    borderRadius: 4,
                                    columnWidth: '55%',
                                },
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            xaxis: {
                                categories: allDates.map(date => new Date(date).toLocaleDateString()),
                                title: {
                                    text: 'Date'
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Revenue ($)'
                                },
                                labels: {
                                    formatter: function(value) {
                                        return '$' + value.toFixed(2);
                                    }
                                }
                            },
                            fill: {
                                opacity: 1
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return "$" + val.toFixed(2);
                                    }
                                }
                            },
                            colors: ['#3b82f6', '#10b981'],
                            legend: {
                                position: 'top'
                            }
                        };

                        const chart = new ApexCharts(document.querySelector("#revenueComparisonChart"), options);
                        chart.render();

                        // Store chart instance for updates
                        window.revenueComparisonChart = chart;
                    };

                    initChart();

                    // Listen for update events
                    Livewire.on('updateChart', () => {
                        const data = @this.getRevenueData();

                        // Prepare series data
                        const period1Dates = data.period1.daily.map(item => item.date);
                        const period2Dates = data.period2.daily.map(item => item.date);
                        const allDates = [...new Set([...period1Dates, ...period2Dates])].sort();

                        const period1Series = allDates.map(date => {
                            const found = data.period1.daily.find(item => item.date === date);
                            return found ? (found.revenue / 100) : 0;
                        });

                        const period2Series = allDates.map(date => {
                            const found = data.period2.daily.find(item => item.date === date);
                            return found ? (found.revenue / 100) : 0;
                        });

                        window.revenueComparisonChart.updateOptions({
                            series: [
                                {
                                    name: data.period1.label,
                                    data: period1Series
                                },
                                {
                                    name: data.period2.label,
                                    data: period2Series
                                }
                            ],
                            xaxis: {
                                categories: allDates.map(date => new Date(date).toLocaleDateString())
                            }
                        });
                    });
                });
            </script>
        @endpush
    </x-filament::section>
</x-filament-widgets::widget>
