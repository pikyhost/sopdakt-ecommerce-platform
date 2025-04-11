<?php

namespace App\Livewire;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class LocationsAnalysisWidget extends Widget
{
    protected static string $view = 'livewire.locations-analysis-widget';

    public Carbon $fromDate;
    public Carbon $toDate;

    public Collection $countryData;
    public Collection $governorateData;
    public Collection $cityData;

    public function mount(): void
    {
        $this->fromDate = now()->subMonth();
        $this->toDate = now();

        $this->loadAnalysisData();
    }

    #[On('updateFromDate1')]
    public function updateFromDate(?string $from): void
    {
        if ($from) {
            $this->fromDate = Carbon::parse($from)->startOfDay();
        }

        $this->loadAnalysisData();
        $this->dispatch('$refresh');
    }

    #[On('updateToDate1')]
    public function updateToDate(?string $to): void
    {
        if ($to) {
            $this->toDate = Carbon::parse($to)->endOfDay();
        }

        $this->loadAnalysisData();
        $this->dispatch('$refresh');
    }

    protected function loadAnalysisData(): void
    {
        $this->countryData = $this->getCountryDistribution();
        $this->governorateData = $this->getGovernorateDistribution();
        $this->cityData = $this->getCityDistribution();
    }

    protected function getCountryDistribution(): Collection
    {
        return Order::query()
            ->join('countries as c', 'orders.country_id', '=', 'c.id')
            ->whereBetween('orders.created_at', [$this->fromDate, $this->toDate])
            ->selectRaw('c.name as country, COUNT(orders.id) as total')
            ->groupBy('orders.country_id', 'c.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($item) => [
                'country' => json_decode($item->country, true) ?? ['en' => $item->country],
                'total' => $item->total,
            ]);
    }

    protected function getGovernorateDistribution(): Collection
    {
        return Order::query()
            ->join('governorates as g', 'orders.governorate_id', '=', 'g.id')
            ->whereBetween('orders.created_at', [$this->fromDate, $this->toDate])
            ->selectRaw('g.name as governorate, COUNT(orders.id) as total')
            ->groupBy('orders.governorate_id', 'g.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($item) => [
                'governorate' => json_decode($item->governorate, true) ?? ['en' => $item->governorate],
                'total' => $item->total,
            ]);
    }

    protected function getCityDistribution(): Collection
    {
        return Order::query()
            ->join('cities as c', 'orders.city_id', '=', 'c.id')
            ->whereBetween('orders.created_at', [$this->fromDate, $this->toDate])
            ->selectRaw('c.name as city, COUNT(orders.id) as total')
            ->groupBy('orders.city_id', 'c.name')
            ->orderByDesc('total')
            ->limit(15)
            ->get()
            ->map(fn ($item) => [
                'city' => json_decode($item->city, true) ?? ['en' => $item->city],
                'total' => $item->total,
            ]);
    }
}
