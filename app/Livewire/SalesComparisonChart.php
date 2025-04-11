<?php

namespace App\Livewire;

use App\Models\Order;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;

class SalesComparisonChart extends ChartWidget
{
    public ?Carbon $fromDate1 = null;
    public ?Carbon $toDate1 = null;
    public ?Carbon $fromDate2 = null;
    public ?Carbon $toDate2 = null;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';

    public function mount(): void
    {
        $this->fromDate1 = now()->startOfMonth();
        $this->toDate1 = now()->endOfMonth();
        $this->fromDate2 = now()->subMonth()->startOfMonth();
        $this->toDate2 = now()->subMonth()->endOfMonth();
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('Sales Comparison: Day vs Month');
    }

    protected function getData(): array
    {
        $labels = range(1, 31); // Days of month

        // First period data
        $data1 = $this->getSalesDataGroupedByDay($this->fromDate1, $this->toDate1);
        $month1Label = $this->fromDate1->format('F');

        // Second period data
        $data2 = $this->getSalesDataGroupedByDay($this->fromDate2, $this->toDate2);
        $month2Label = $this->fromDate2->format('F');

        return [
            'datasets' => [
                [
                    'label' => $month1Label,
                    'data' => $this->fillMissingDays($data1),
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'tension' => 0.4,
                    'borderWidth' => 2,
                    'fill' => false,
                ],
                [
                    'label' => $month2Label,
                    'data' => $this->fillMissingDays($data2),
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'tension' => 0.4,
                    'borderWidth' => 2,
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getSalesDataGroupedByDay(Carbon $start, Carbon $end): array
    {
        $trend = Trend::model(Order::class)
            ->between(start: $start, end: $end)
            ->perDay()
            ->sum('total');

        $grouped = [];

        foreach ($trend as $value) {
            $day = Carbon::parse($value->date)->day;
            $grouped[$day] = $value->aggregate;
        }

        return $grouped;
    }

    protected function fillMissingDays(array $data): array
    {
        $filled = [];

        for ($i = 1; $i <= 31; $i++) {
            $filled[] = $data[$i] ?? null;
        }

        return $filled;
    }

    #[\Livewire\Attributes\On('updateFromDate1')]
    public function updateFromDate1(?string $from): void
    {
        if ($from) {
            $this->fromDate1 = Carbon::parse($from)->startOfDay();
            $this->dispatch('$refresh');
        }
    }

    #[\Livewire\Attributes\On('updateToDate1')]
    public function updateToDate1(?string $to): void
    {
        if ($to) {
            $this->toDate1 = Carbon::parse($to)->endOfDay();
            $this->dispatch('$refresh');
        }
    }

    #[\Livewire\Attributes\On('updateFromDate2')]
    public function updateFromDate2(?string $from): void
    {
        if ($from) {
            $this->fromDate2 = Carbon::parse($from)->startOfDay();
            $this->dispatch('$refresh');
        }
    }

    #[\Livewire\Attributes\On('updateToDate2')]
    public function updateToDate2(?string $to): void
    {
        if ($to) {
            $this->toDate2 = Carbon::parse($to)->endOfDay();
            $this->dispatch('$refresh');
        }
    }

    protected function getType(): string
    {
        return 'line';
    }
}
