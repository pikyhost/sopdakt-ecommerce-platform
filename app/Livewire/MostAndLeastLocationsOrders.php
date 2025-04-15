<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MostAndLeastLocationsOrders extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Country::query()->whereRaw('1 = 0');
            })
            ->columns([
                TextColumn::make('type')
                    ->label('Ranking Type')
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state))),

                TextColumn::make('name')
                    ->label('Location Name')
                    ->searchable(),

                TextColumn::make('orders_count')
                    ->label('Orders Count')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),
            ])
            ->paginated(false)
            ->heading('Location Orders Ranking')
            ->description('Top and bottom 10 locations by order count')
            ->emptyStateHeading('No orders data available')
            ->emptyStateDescription('Once orders are placed, rankings will appear here');
    }

    protected function getTableQuery(): ?Builder
    {
        return null; // We override this to provide our own data
    }

    public function getTableRecords(): \Illuminate\Contracts\Pagination\CursorPaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Database\Eloquent\Collection
    {
        return collect()
            // Most Countries
            ->merge($this->getRankedData(
                Country::withCount('orders')
                    ->having('orders_count', '>', 0)
                    ->orderByDesc('orders_count')
                    ->limit(10)
                    ->get(),
                'most_countries'
            ))

            // Least Countries
            ->merge($this->getRankedData(
                Country::withCount('orders')
                    ->having('orders_count', '>', 0)
                    ->orderBy('orders_count')
                    ->limit(10)
                    ->get(),
                'least_countries'
            ))

            // Most Governorates
            ->merge($this->getRankedData(
                Governorate::withCount('orders')
                    ->having('orders_count', '>', 0)
                    ->orderByDesc('orders_count')
                    ->limit(10)
                    ->get(),
                'most_governorates'
            ))

            // Least Governorates
            ->merge($this->getRankedData(
                Governorate::withCount('orders')
                    ->having('orders_count', '>', 0)
                    ->orderBy('orders_count')
                    ->limit(10)
                    ->get(),
                'least_governorates'
            ))

            // Most Cities
            ->merge($this->getRankedData(
                City::withCount('orders')
                    ->having('orders_count', '>', 0)
                    ->orderByDesc('orders_count')
                    ->limit(10)
                    ->get(),
                'most_cities'
            ))

            // Least Cities
            ->merge($this->getRankedData(
                City::withCount('orders')
                    ->having('orders_count', '>', 0)
                    ->orderBy('orders_count')
                    ->limit(10)
                    ->get(),
                'least_cities'
            ));
    }

    protected function getRankedData($locations, string $type): Collection
    {
        return $locations->map(function ($location) use ($type) {
            return (object) [
                'type' => $type,
                'name' => $location->name,
                'orders_count' => $location->orders_count,
            ];
        });
    }
}
