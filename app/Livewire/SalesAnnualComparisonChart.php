<?php

namespace App\Livewire;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\On;

class SalesAnnualComparisonChart extends ChartWidget
{
    public int $year1;
    public int $year2;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    public function mount(): void
    {
        $this->year1 = now()->year;
        $this->year2 = now()->subYear()->year;
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('Sales Annual Comparison');
    }

    protected function getData(): array
    {
        $sales1 = $this->getYearlySalesData($this->year1);
        $sales2 = $this->getYearlySalesData($this->year2);

        // X-axis will represent days (1–31)
        $labels = range(1, 31);
        $months = collect(range(1, 12))->map(fn ($m) => Carbon::create()->month($m)->format('F'));

        return [
            'datasets' => [
                [
                    'label' => $this->year1,
                    'data' => $this->structureChartData($sales1),
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                ],
                [
                    'label' => $this->year2,
                    'data' => $this->structureChartData($sales2),
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels, // Days 1-31
            'yLabels' => $months->toArray(), // Custom label suggestion — needs chart lib config
        ];
    }

    protected function getYearlySalesData(int $year): array
    {
        $start = Carbon::create($year, 1, 1);
        $end = Carbon::create($year, 12, 31);

        $data = Trend::model(Order::class)
            ->between(start: $start, end: $end)
            ->perDay()
            ->sum('total');

        $sales = [];

        foreach ($data as $entry) {
            $day = (int) Carbon::parse($entry->date)->format('d');
            $month = (int) Carbon::parse($entry->date)->format('m');

            $sales[$month][$day] = $entry->aggregate;
        }

        return $sales;
    }

    protected function structureChartData(array $sales): array
    {
        // Create one line for each month stacked vertically by day
        $values = [];

        for ($day = 1; $day <= 31; $day++) {
            $monthlyTotals = [];

            for ($month = 1; $month <= 12; $month++) {
                $monthlyTotals[] = $sales[$month][$day] ?? null;
            }

            // For each day, return average of all month values that exist
            $values[] = collect($monthlyTotals)->filter()->avg() ?? 0;
        }

        return $values;
    }

    #[On('updateYear1')]
    public function updateYear1(int $year): void
    {
        $this->year1 = $year;
        $this->dispatch('$refresh');
    }

    #[On('updateYear2')]
    public function updateYear2(int $year): void
    {
        $this->year2 = $year;
        $this->dispatch('$refresh');
    }

    protected function getType(): string
    {
        return 'line';
    }
}
