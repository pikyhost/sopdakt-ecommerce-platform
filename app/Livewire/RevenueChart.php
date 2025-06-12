<?php

namespace App\Livewire;

use App\Models\Order;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    public Carbon $fromDate;
    public Carbon $toDate;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;

    public function getHeading(): string|Htmlable|null
    {
        return __('Revenue Chart 1');
    }

    protected function getData(): array
    {
        $fromDate = $this->fromDate ??= now()->subMonth();
        $toDate = $this->toDate ??= now();

        $data = Trend::model(Order::class)
            ->between(
                start: $fromDate,
                end: $toDate,
            )
            ->perDay()
            ->sum('total');

        return [
            'datasets' => [
                [
                    'label' => __('Revenue Chart 1'),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    #[On('updateFromDate1')]
    public function updateFromDate(?string $from): void
    {
        if ($from) {
            $this->fromDate = Carbon::parse($from)->startOfDay();
        }
        $this->dispatch('$refresh');
    }

    #[On('updateToDate1')]
    public function updateToDate(?string $to): void
    {
        if ($to) {
            $this->toDate = Carbon::parse($to)->endOfDay();
        }
        $this->dispatch('$refresh');
    }
    protected function getType(): string
    {
        return 'line';
    }
}
