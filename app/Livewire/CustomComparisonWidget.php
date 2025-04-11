<?php

namespace App\Livewire;

use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class CustomComparisonWidget extends Widget
{
    public ?Carbon $fromDate1 = null;
    public ?Carbon $toDate1 = null;
    public ?Carbon $fromDate2 = null;
    public ?Carbon $toDate2 = null;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'livewire.custom-comparison-widget';

    public function mount(): void
    {
        $this->fromDate1 = now()->startOfMonth();
        $this->toDate1 = now()->endOfMonth();
        $this->fromDate2 = now()->subMonth()->startOfMonth();
        $this->toDate2 = now()->subMonth()->endOfMonth();
    }

    public function getRevenueData(): array
    {
        // Get revenue for period 1
        $period1Revenue = Order::query()
            ->whereBetween('created_at', [$this->fromDate1, $this->toDate1])
            ->sum('total');

        // Get revenue for period 2
        $period2Revenue = Order::query()
            ->whereBetween('created_at', [$this->fromDate2, $this->toDate2])
            ->sum('total');

        // Get daily breakdown for period 1
        $dailyPeriod1 = Order::query()
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            ])
            ->whereBetween('created_at', [$this->fromDate1, $this->toDate1])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => $item->revenue
                ];
            })
            ->toArray();

        // Get daily breakdown for period 2
        $dailyPeriod2 = Order::query()
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue')
            ])
            ->whereBetween('created_at', [$this->fromDate2, $this->toDate2])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'x' => $item->date,
                    'y' => $item->revenue
                ];
            })
            ->toArray();

        return [
            'period1' => [
                'total' => $period1Revenue,
                'daily' => $dailyPeriod1,
                'from_date' => $this->fromDate1,
                'to_date' => $this->toDate1,
                'label' => $this->fromDate1->format('M d, Y').' - '.$this->toDate1->format('M d, Y')
            ],
            'period2' => [
                'total' => $period2Revenue,
                'daily' => $dailyPeriod2,
                'from_date' => $this->fromDate2,
                'to_date' => $this->toDate2,
                'label' => $this->fromDate2->format('M d, Y').' - '.$this->toDate2->format('M d, Y')
            ],
        ];
    }

    #[On('updateFromDate1')]
    public function updateFromDate1(?string $from): void
    {
        if ($from) {
            $this->fromDate1 = Carbon::parse($from)->startOfDay();
            $this->dispatch('updateChart', data: $this->getRevenueData());
        }
    }

    #[On('updateToDate1')]
    public function updateToDate1(?string $to): void
    {
        if ($to) {
            $this->toDate1 = Carbon::parse($to)->endOfDay();
            $this->dispatch('updateChart', data: $this->getRevenueData());
        }
    }

    #[On('updateFromDate2')]
    public function updateFromDate2(?string $from): void
    {
        if ($from) {
            $this->fromDate2 = Carbon::parse($from)->startOfDay();
            $this->dispatch('updateChart', data: $this->getRevenueData());
        }
    }

    #[On('updateToDate2')]
    public function updateToDate2(?string $to): void
    {
        if ($to) {
            $this->toDate2 = Carbon::parse($to)->endOfDay();
            $this->dispatch('updateChart', data: $this->getRevenueData());
        }
    }
}
