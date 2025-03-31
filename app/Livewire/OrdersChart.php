<?php

namespace App\Livewire;

use App\Models\Order;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;

class OrdersChart extends ChartWidget
{
    public Carbon $fromDate;
    public Carbon $toDate;

    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;

    public function getHeading(): string|Htmlable|null
    {
        return __('Orders Chart');
    }

    protected function getData(): array
    {
        $fromDate = $this->fromDate ??= now()->subWeek();
        $toDate = $this->toDate ??= now();

        $data = Trend::model(Order::class)
            ->between(
                start: $fromDate,
                end: $toDate,
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    #[On('updateFromDate')]
    public function updateFromDate(string $from): void
    {
        $this->fromDate = Carbon::parse($from);
        $this->dispatch('$refresh'); // This tells Livewire to refresh the component
    }

    #[On('updateToDate')]
    public function updateToDate(string $to): void
    {
        $this->toDate = Carbon::parse($to);
        $this->dispatch('$refresh');
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
