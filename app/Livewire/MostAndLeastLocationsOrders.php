<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MostAndLeastLocationsOrders extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => Order::query()
                    ->selectRaw('
                        COALESCE(countries.name, governorates.name, cities.name) as location_name,
                        COUNT(orders.id) as orders_count
                    ')
                    ->leftJoin('countries', 'orders.country_id', '=', 'countries.id')
                    ->leftJoin('governorates', 'orders.governorate_id', '=', 'governorates.id')
                    ->leftJoin('cities', 'orders.city_id', '=', 'cities.id')
                    ->groupBy('location_name')
            )
            ->modifyQueryUsing(function (Builder $query, $livewire) {
                $filtersCount = !empty($livewire->tableFilters) && collect($livewire->tableFilters)->pluck('value')->filter()->isNotEmpty();

                $query->when(!$filtersCount, fn ($query) => $query->whereRaw('1 = 0'));
            })
            ->emptyStateHeading('Please search or filter records.')
            ->emptyStateDescription('Current search did not return any results. Please try again.')
            ->columns([
                TextColumn::make('location_name')
                    ->label('Location'),
                TextColumn::make('orders_count')
                    ->label('Orders Count')
                    ->sortable()
                    ->alignRight(),
            ])
            ->filters([
                Filter::make('location_filter')
                    ->label('Location Orders Ranking')
                    ->form([
                        Select::make('type')
                            ->options([
                                'most_countries' => 'Most Countries',
                                'least_countries' => 'Least Countries',
                                'most_governorates' => 'Most Governorates',
                                'least_governorates' => 'Least Governorates',
                                'most_cities' => 'Most Cities',
                                'least_cities' => 'Least Cities',
                            ])
                            ->required()
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $type = $data['type'] ?? null;

                        if (! $type) {
                            return $query;
                        }

                        $order = str_contains($type, 'most') ? 'desc' : 'asc';

                        return $query
                            ->selectRaw(match (true) {
                                str_contains($type, 'countries') => 'countries.name as location_name, COUNT(orders.id) as orders_count',
                                str_contains($type, 'governorates') => 'governorates.name as location_name, COUNT(orders.id) as orders_count',
                                str_contains($type, 'cities') => 'cities.name as location_name, COUNT(orders.id) as orders_count',
                            })
                            ->when(str_contains($type, 'countries'), fn ($q) => $q->join('countries', 'orders.country_id', '=', 'countries.id'))
                            ->when(str_contains($type, 'governorates'), fn ($q) => $q->join('governorates', 'orders.governorate_id', '=', 'governorates.id'))
                            ->when(str_contains($type, 'cities'), fn ($q) => $q->join('cities', 'orders.city_id', '=', 'cities.id'))
                            ->groupBy('location_name')
                            ->orderBy('orders_count', $order);
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return match ($data['type'] ?? null) {
                            'most_countries' => 'Most Countries by Orders',
                            'least_countries' => 'Least Countries by Orders',
                            'most_governorates' => 'Most Governorates by Orders',
                            'least_governorates' => 'Least Governorates by Orders',
                            'most_cities' => 'Most Cities by Orders',
                            'least_cities' => 'Least Cities by Orders',
                            default => null,
                        };
                    }),
            ]);
    }
}
