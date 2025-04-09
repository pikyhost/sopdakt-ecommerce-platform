<?php

namespace App\Filament\Pages;

use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class ProductAnalysis extends Page
{
    protected static string $view = 'filament.pages.product-analysis';
    protected static bool $shouldRegisterNavigation = false;

    public Product $product;
    public Carbon $fromDate;
    public Carbon $toDate;
    public array $sizeData = [];
    public array $colorData = [];
    public array $timeData = [];
    public array $locationData = [];
    public array $statusData = [];

    protected $listeners = [
        'updateFromDateProduct' => 'updateFromDate',
        'updateToDateProduct' => 'updateToDate',
    ];

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->fromDate = now()->subMonth();
        $this->toDate = now();
        $this->loadAnalysisData();
    }

    public static function getRoutePath(): string
    {
        return '/products/{product}/analysis';
    }

    #[On('updateFromDateProduct')]
    public function updateFromDate(string $from): void
    {
        $this->fromDate = Carbon::parse($from)->startOfDay();
        $this->loadAnalysisData();
    }

    #[On('updateToDateProduct')]
    public function updateToDate(string $to): void
    {
        $this->toDate = Carbon::parse($to)->endOfDay();
        $this->loadAnalysisData();
    }

    protected function loadAnalysisData(): void
    {
        $this->sizeData = $this->getSizeDistribution();
        $this->colorData = $this->getColorDistribution();
        $this->timeData = $this->getTimeDistribution();
        $this->locationData = $this->getLocationDistribution();
        $this->statusData = $this->getStatusDistribution();

        $this->dispatch('updateCharts', [
            'sizeData' => $this->sizeData,
            'colorData' => $this->colorData,
            'timeData' => $this->timeData,
            'locationData' => $this->locationData,
            'statusData' => $this->statusData,
        ]);
    }

    protected function getSizeDistribution(): array
    {
        return OrderItem::query()
            ->from('order_items as oi')
            ->where('oi.product_id', $this->product->id)
            ->whereBetween('oi.created_at', [$this->fromDate, $this->toDate])
            ->join('sizes as s', 'oi.size_id', '=', 's.id')
            ->select(
                's.name',
                DB::raw('SUM(oi.quantity) as total')
            )
            ->groupBy('oi.size_id', 's.name')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                $sizeName = json_decode($item->name, true);
                return [
                    'size' => $sizeName[app()->getLocale()] ?? $sizeName['en'],
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    protected function getColorDistribution(): array
    {
        return OrderItem::query()
            ->where('order_items.product_id', $this->product->id)
            ->whereBetween('order_items.created_at', [$this->fromDate, $this->toDate])
            ->join('colors', 'order_items.color_id', '=', 'colors.id')
            ->select(
                'colors.name',
                'colors.code',
                DB::raw('SUM(order_items.quantity) as total')
            )
            ->groupBy('order_items.color_id', 'colors.name', 'colors.code')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                $colorName = json_decode($item->name, true);
                return [
                    'color' => $colorName[app()->getLocale()] ?? $colorName['en'],
                    'code' => $item->code,
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    protected function getTimeDistribution(): array
    {
        return OrderItem::query()
            ->where('order_items.product_id', $this->product->id)
            ->whereBetween('order_items.created_at', [$this->fromDate, $this->toDate])
            ->select(
                DB::raw('DATE(order_items.created_at) as date'),
                DB::raw('SUM(order_items.quantity) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    protected function getLocationDistribution(): array
    {
        return OrderItem::query()
            ->where('order_items.product_id', $this->product->id)
            ->whereBetween('order_items.created_at', [$this->fromDate, $this->toDate])
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('countries', 'orders.country_id', '=', 'countries.id')
            ->select(
                'countries.name as country',
                DB::raw('SUM(order_items.quantity) as total')
            )
            ->groupBy('orders.country_id', 'countries.name')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    protected function getStatusDistribution(): array
    {
        return OrderItem::query()
            ->where('order_items.product_id', $this->product->id)
            ->whereBetween('order_items.created_at', [$this->fromDate, $this->toDate])
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'orders.status',
                DB::raw('SUM(order_items.quantity) as total')
            )
            ->groupBy('orders.status')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    public function getHeading(): string|Htmlable
    {
        return __('Product Analysis: ') . $this->product->name;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Livewire\ProductFilter::class,
        ];
    }
}
