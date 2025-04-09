<x-filament::page>
    <div class="space-y-6">
        <!-- Product Summary Card -->
        <x-filament::card>
            <div class="flex items-start gap-4">
                <x-filament::avatar
                    src="{{ $product->getFirstMediaUrl('feature_product_image') }}"
                    alt="{{ $product->name }}"
                    size="xl"
                />
                <div class="flex-1">
                    <h2 class="text-2xl font-bold">{{ $product->name }}</h2>
                    <p class="text-gray-500">
                        Analysis period: {{ $fromDate->format('M d, Y') }} - {{ $toDate->format('M d, Y') }}
                    </p>
                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Total Ordered</p>
                            <p class="text-2xl font-bold">
                                {{ array_sum(array_column($sizeData, 'total')) }}
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Unique Sizes</p>
                            <p class="text-2xl font-bold">{{ count($sizeData) }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Unique Colors</p>
                            <p class="text-2xl font-bold">{{ count($colorData) }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-500">Countries</p>
                            <p class="text-2xl font-bold">{{ count($locationData) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::card>

        <!-- Size Distribution -->
        <x-filament::card>
            <h3 class="text-lg font-semibold mb-4">Size Distribution</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <table class="w-full">
                        <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Size</th>
                            <th class="text-right py-2">Quantity</th>
                            <th class="text-right py-2">%</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sizeData as $size)
                            <tr class="border-b">
                                <td class="py-2">{{ $size['size'] }}</td>
                                <td class="text-right py-2">{{ $size['total'] }}</td>
                                <td class="text-right py-2">
                                    {{ round($size['total']/array_sum(array_column($sizeData, 'total'))*100, 1) }}%
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div>
                    <canvas id="sizeChart"></canvas>
                </div>
            </div>
        </x-filament::card>

        <!-- Color Distribution -->
        <x-filament::card>
            <h3 class="text-lg font-semibold mb-4">Color Distribution</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <table class="w-full">
                        <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Color</th>
                            <th class="text-right py-2">Quantity</th>
                            <th class="text-right py-2">%</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($colorData as $color)
                            <tr class="border-b">
                                <td class="py-2 flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full inline-block"
                                          style="background-color: {{ $color['code'] }}"></span>
                                    {{ $color['color'] }}
                                </td>
                                <td class="text-right py-2">{{ $color['total'] }}</td>
                                <td class="text-right py-2">
                                    {{ round($color['total']/array_sum(array_column($colorData, 'total'))*100, 1) }}%
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div>
                    <canvas id="colorChart"></canvas>
                </div>
            </div>
        </x-filament::card>

        <!-- Additional Analysis Sections -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Time Distribution -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">Sales Over Time</h3>
                <canvas id="timeChart"></canvas>
            </x-filament::card>

            <!-- Location Distribution -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">Top Countries</h3>
                <canvas id="locationChart"></canvas>
            </x-filament::card>

            <!-- Status Distribution -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">Order Status</h3>
                <canvas id="statusChart"></canvas>
            </x-filament::card>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:init', () => {
                // Size Distribution Chart
                new Chart(document.getElementById('sizeChart'), {
                    type: 'pie',
                    data: {
                        labels: @json(array_column($sizeData, 'size')),
                        datasets: [{
                            data: @json(array_column($sizeData, 'total')),
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                                '#9966FF', '#FF9F40', '#8AC249', '#EA5545'
                            ]
                        }]
                    }
                });

                // Color Distribution Chart
                new Chart(document.getElementById('colorChart'), {
                    type: 'doughnut',
                    data: {
                        labels: @json(array_column($colorData, 'color')),
                        datasets: [{
                            data: @json(array_column($colorData, 'total')),
                            backgroundColor: @json(array_column($colorData, 'code'))
                        }]
                    }
                });

                // Time Distribution Chart
                new Chart(document.getElementById('timeChart'), {
                    type: 'line',
                    data: {
                        labels: @json(array_column($timeData, 'date')),
                        datasets: [{
                            label: 'Daily Sales',
                            data: @json(array_column($timeData, 'total')),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true
                        }]
                    },
                    options: {
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                // Location Distribution Chart
                new Chart(document.getElementById('locationChart'), {
                    type: 'bar',
                    data: {
                        labels: @json(array_column($locationData, 'country')),
                        datasets: [{
                            label: 'Orders by Country',
                            data: @json(array_column($locationData, 'total')),
                            backgroundColor: '#10B981'
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        scales: {
                            x: { beginAtZero: true }
                        }
                    }
                });

                // Status Distribution Chart
                new Chart(document.getElementById('statusChart'), {
                    type: 'polarArea',
                    data: {
                        labels: @json(array_column($statusData, 'status')),
                        datasets: [{
                            data: @json(array_column($statusData, 'total')),
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
                            ]
                        }]
                    }
                });
            });
        </script>
    @endpush
</x-filament::page>
