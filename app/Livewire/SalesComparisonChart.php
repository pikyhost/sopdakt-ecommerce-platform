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
    public ?Carbon $fromDate1 = null;
    public ?Carbon $toDate1 = null;
    public ?Carbon $fromDate2 = null;
    public ?Carbon $toDate2 = null;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    public function mount(): void
    {
        $this->fromDate1 = now()->startOfMonth();
        $this->toDate1 = now()->endOfMonth();
        $this->fromDate2 = now()->subMonth()->startOfMonth();
        $this->toDate2 = now()->subMonth()->endOfMonth();
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('Sales Chart Comparison');
    }

    protected function getData(): array
    {
        // Ensure we have valid dates
        $fromDate1 = $this->fromDate1 ?? now()->startOfMonth();
        $toDate1 = $this->toDate1 ?? now()->endOfMonth();
        $fromDate2 = $this->fromDate2 ?? now()->subMonth()->startOfMonth();
        $toDate2 = $this->toDate2 ?? now()->subMonth()->endOfMonth();

        // Generate complete date ranges for both periods
        $period1Dates = $this->generateDateRange($fromDate1, $toDate1);
        $period2Dates = $this->generateDateRange($fromDate2, $toDate2);

        // Get all unique dates for the chart labels
        $allDates = $period1Dates->merge($period2Dates)
            ->unique()
            ->sort()
            ->values();

        // Get data for both periods
        $data1 = $this->getPeriodData($fromDate1, $toDate1, $allDates);
        $data2 = $this->getPeriodData($fromDate2, $toDate2, $allDates);

        return [
            'datasets' => [
                [
                    'label' => __('Current Period') . ' (' . $fromDate1->format('M d') . ' - ' . $toDate1->format('M d') . ')',
                    'data' => $data1,
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => false,
                ],
                [
                    'label' => __('Comparison Period') . ' (' . $fromDate2->format('M d') . ' - ' . $toDate2->format('M d') . ')',
                    'data' => $data2,
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => false,
                ],
            ],
            'labels' => $allDates->map(fn ($date) => $date->format('Y-m-d'))->toArray(),
        ];
    }

    protected function generateDateRange(Carbon $startDate, Carbon $endDate)
    {
        $dates = collect();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dates->push($currentDate->copy());
            $currentDate->addDay();
        }

        return $dates;
    }

    protected function getPeriodData(Carbon $startDate, Carbon $endDate, $allDates)
    {
        $trendData = Trend::model(Order::class)
            ->between(start: $startDate, end: $endDate)
            ->perDay()
            ->sum('total');

        $dataByDate = $trendData->keyBy(fn (TrendValue $value) => Carbon::parse($value->date)->format('Y-m-d'));

        return $allDates->map(function ($date) use ($dataByDate, $startDate, $endDate) {
            $dateKey = $date->format('Y-m-d');

            // Only show data within the period's date range
            if ($date >= $startDate && $date <= $endDate) {
                return $dataByDate[$dateKey]->aggregate ?? 0;
            }

            return null; // This will create a gap in the chart for dates outside the period
        })->toArray();
    }

    #[On('updateFromDate1')]
    public function updateFromDate1(?string $from): void
    {
        if ($from) {
            $this->fromDate1 = Carbon::parse($from)->startOfDay();
            $this->dispatch('$refresh');
        }
    }

    #[On('updateToDate1')]
    public function updateToDate1(?string $to): void
    {
        if ($to) {
            $this->toDate1 = Carbon::parse($to)->endOfDay();
            $this->dispatch('$refresh');
        }
    }

    #[On('updateFromDate2')]
    public function updateFromDate2(?string $from): void
    {
        if ($from) {
            $this->fromDate2 = Carbon::parse($from)->startOfDay();
            $this->dispatch('$refresh');
        }
    }

    #[On('updateToDate2')]
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
