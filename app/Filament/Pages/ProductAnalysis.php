<?php

namespace App\Filament\Pages;

use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ProductAnalysis extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.product-analysis';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'products/{product}/analysis/{from}/{to}';

    public Product $product;
    public Carbon $fromDate;
    public Carbon $toDate;
    public array $sizeData = [];
    public array $colorData = [];
    public array $timeData = [];
    public array $countryData = [];
    public array $governorateData = [];
    public array $cityData = [];
    public array $statusData = [];
    public int $totalOrderCount;
    public $from_date;
    public $to_date;
    public array $colorSizeData = [];


    public function mount(Product $product, string $from, string $to): void
    {
        $this->product = $product;
        $this->fromDate = Carbon::parse($from)->startOfDay(); // Start of the from date
        $this->toDate = Carbon::parse($to)->endOfDay(); // End of the to date (includes all of today)
        $this->totalOrderCount = $this->getTotalOrderCount();

        $this->from_date = $this->fromDate->format('Y-m-d');
        $this->to_date = $this->toDate->format('Y-m-d');

        $this->form->fill([
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
        ]);

        $this->loadAnalysisData();
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make([
                DatePicker::make('from_date')
                    ->native(false)
                    ->columnSpan(4)
                    ->label(__('Start date')),

                DatePicker::make('to_date')
                    ->native(false)
                    ->columnSpan(4)
                    ->label(__('End date')),
            ])->columns(8)
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $this->redirect(self::getUrl([
            'product' => $this->product->slug,
            'from' => $data['from_date'],
            'to' => $data['to_date'],
        ]));
    }

    protected function loadAnalysisData(): void
    {
        $this->sizeData = $this->getSizeDistribution();
        $this->colorData = $this->getColorDistribution();
        $this->timeData = $this->getTimeDistribution();
        $this->countryData = $this->getCountryDistribution();
        $this->governorateData = $this->getGovernorateDistribution();
        $this->cityData = $this->getCityDistribution();
        $this->statusData = $this->getStatusDistribution();
        $this->colorSizeData = $this->getColorSizeDistribution();
    }

    public static function getRoutePath(): string
    {
        return '/products/{product}/analysis/{from}/{to}';
    }

    public static function getRouteParameters(): array
    {
        return [
            'product',
            'from',
            'to'
        ];
    }

    protected function getColorSizeDistribution(): array
    {
        return OrderItem::query()
            ->from('order_items as oi')
            ->leftJoin('orders as o', 'oi.order_id', '=', 'o.id')
            ->leftJoin('colors as c', 'oi.color_id', '=', 'c.id')
            ->leftJoin('sizes as s', 'oi.size_id', '=', 's.id')
            ->where('oi.product_id', $this->product->id)
            ->whereBetween('oi.created_at', [$this->fromDate, $this->toDate])
            ->selectRaw('
            IFNULL(c.name, \'{"en":"N/A"}\') as color_name,
            IFNULL(c.code, \'#cccccc\') as color_code,
            IFNULL(s.name, \'{"en":"N/A"}\') as size_name,
            SUM(oi.quantity) as total_quantity,
            COUNT(DISTINCT oi.order_id) as order_count
        ')
            ->groupByRaw('
            IFNULL(c.name, \'{"en":"N/A"}\'),
            IFNULL(c.code, \'#cccccc\'),
            IFNULL(s.name, \'{"en":"N/A"}\')
        ')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'color' => json_decode($item->color_name, true),
                    'color_code' => $item->color_code,
                    'size' => json_decode($item->size_name, true),
                    'total_quantity' => (int) $item->total_quantity,
                    'order_count' => (int) $item->order_count,
                ];
            })
            ->toArray();
    }

    protected function getTotalOrderCount(): int
    {
        return OrderItem::query()
            ->where('product_id', $this->product->id)
            ->whereBetween('created_at', [$this->fromDate, $this->toDate])
            ->distinct('order_id')
            ->count('order_id');
    }


    protected function getSizeDistribution(): array
    {
        return OrderItem::query()
            ->from('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('sizes as s', 'oi.size_id', '=', 's.id')
            ->where('oi.product_id', $this->product->id)
            ->where('oi.created_at', '>=', $this->fromDate)
            ->where('oi.created_at', '<=', $this->toDate) // Use <= to include end of day
            ->selectRaw('s.name, SUM(oi.quantity) as total')
            ->groupBy('oi.size_id', 's.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'size' => json_decode($item->name, true) ?? ['en' => $item->name],
                'total' => $item->total,
            ])
            ->toArray();
    }

    protected function getColorDistribution(): array
    {
        return OrderItem::query()
            ->from('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('colors as c', 'oi.color_id', '=', 'c.id')
            ->where('oi.product_id', $this->product->id)
            ->whereBetween('oi.created_at', [$this->fromDate, $this->toDate])
            ->selectRaw('c.name, c.code, SUM(oi.quantity) as total')
            ->groupBy('oi.color_id', 'c.name', 'c.code')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'color' => json_decode($item->name, true) ?? ['en' => $item->name],
                'code' => $item->code,
                'total' => $item->total,
            ])
            ->toArray();
    }


    protected function getTimeDistribution(): array
    {
        return OrderItem::query()
            ->from('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('oi.product_id', $this->product->id)
            ->whereBetween('oi.created_at', [$this->fromDate, $this->toDate])
            ->selectRaw('DATE(oi.created_at) as date, SUM(oi.quantity) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    protected function getCountryDistribution(): array
    {
        return OrderItem::query()
            ->from('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('countries as c', 'o.country_id', '=', 'c.id')
            ->where('oi.product_id', $this->product->id)
            ->whereBetween('oi.created_at', [$this->fromDate, $this->toDate])
            ->selectRaw('c.name as country, SUM(oi.quantity) as total')
            ->groupBy('o.country_id', 'c.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'country' => json_decode($item->country, true) ?? ['en' => $item->country],
                'total' => $item->total,
            ])
            ->toArray();
    }

    protected function getGovernorateDistribution(): array
    {
        return OrderItem::query()
            ->from('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('governorates as g', 'o.governorate_id', '=', 'g.id')
            ->where('oi.product_id', $this->product->id)
            ->whereBetween('oi.created_at', [$this->fromDate, $this->toDate])
            ->selectRaw('g.name as governorate, SUM(oi.quantity) as total')
            ->groupBy('o.governorate_id', 'g.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'governorate' => json_decode($item->governorate, true) ?? ['en' => $item->governorate],
                'total' => $item->total,
            ])
            ->toArray();
    }

    protected function getCityDistribution(): array
    {
        return OrderItem::query()
            ->from('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->join('cities as c', 'o.city_id', '=', 'c.id')
            ->where('oi.product_id', $this->product->id)
            ->whereBetween('oi.created_at', [$this->fromDate, $this->toDate])
            ->selectRaw('c.name as city, SUM(oi.quantity) as total')
            ->groupBy('o.city_id', 'c.name')
            ->orderByDesc('total')
            ->limit(15)
            ->get()
            ->map(fn($item) => [
                'city' => json_decode($item->city, true) ?? ['en' => $item->city],
                'total' => $item->total,
            ])
            ->toArray();
    }


    protected function getStatusDistribution(): array
    {
        return OrderItem::query()
            ->from('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->where('oi.product_id', $this->product->id)
            ->whereBetween('oi.created_at', [$this->fromDate, $this->toDate])
            ->selectRaw('o.status, SUM(oi.quantity) as total')
            ->groupBy('o.status')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'status' => $item->status,
                'total' => $item->total,
            ])
            ->toArray();
    }

    public function getHeading(): string|Htmlable
    {
        return __('Product Analysis: ') . $this->product->name;
    }
}
