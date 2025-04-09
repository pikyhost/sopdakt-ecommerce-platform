<?php

namespace App\Livewire;

use App\Models\Order;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;

class SalesComparisonChart extends ChartWidget
{
    public Carbon $fromDate1;
    public Carbon $toDate1;
    public Carbon $fromDate2;
    public Carbon $toDate2;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string|Htmlable|null
    {
        return __('Sales Chart Comparison');
    }

    protected function getData(): array
    {
        // Default values if not set
        $fromDate1 = $this->fromDate1 ??= now()->subMonth();
        $toDate1 = $this->toDate1 ??= now();

        $fromDate2 = $this->fromDate2 ??= now()->subMonths(2)->startOfMonth();
        $toDate2 = $this->toDate2 ??= now()->subMonth()->endOfMonth();

        // Get data for both periods
        $data1 = Trend::model(Order::class)
            ->between(start: $fromDate1, end: $toDate1)
            ->perDay()
            ->sum('total');

        $data2 = Trend::model(Order::class)
            ->between(start: $fromDate2, end: $toDate2)
            ->perDay()
            ->sum('total');

        // Create combined labels with proper date formatting
        $labels1 = $data1->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('Y-m-d'));
        $labels2 = $data2->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('Y-m-d'));

        return [
            'datasets' => [
                [
                    'label' => __('Period 1') . ' (' . $fromDate1->format('M d') . ' - ' . $toDate1->format('M d') . ')',
                    'data' => $data1->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => false,
                ],
                [
                    'label' => __('Period 2') . ' (' . $fromDate2->format('M d') . ' - ' . $toDate2->format('M d') . ')',
                    'data' => $data2->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => false,
                ],
            ],
            'labels' => $labels1->merge($labels2)->unique()->sort()->values()->toArray(),
        ];
    }

    #[On('updateFromDate1')]
    public function updateFromDate1(?string $from): void
    {
        if ($from) {
            $this->fromDate1 = Carbon::parse($from)->startOfDay();
        }
        $this->dispatch('$refresh');
    }

    #[On('updateToDate1')]
    public function updateToDate1(?string $to): void
    {
        if ($to) {
            $this->toDate1 = Carbon::parse($to)->endOfDay();
        }
        $this->dispatch('$refresh');
    }

    #[On('updateFromDate2')]
    public function updateFromDate2(?string $from): void
    {
        if ($from) {
            $this->fromDate2 = Carbon::parse($from)->startOfDay();
        }
        $this->dispatch('$refresh');
    }

    #[On('updateToDate2')]
    public function updateToDate2(?string $to): void
    {
        if ($to) {
            $this->toDate2 = Carbon::parse($to)->endOfDay();
        }
        $this->dispatch('$refresh');
    }

    protected function getType(): string
    {
        return 'line';
    }
}
