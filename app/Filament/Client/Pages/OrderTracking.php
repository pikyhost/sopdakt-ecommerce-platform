<?php

namespace App\Filament\Client\Pages;

use App\Models\Order;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class OrderTracking extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static string $view = 'filament.pages.order-tracking';
    protected static ?string $title = 'Orders Tracking';
    protected static ?string $slug = 'orders-tracking';
    protected static ?int $navigationSort = 99;

    public static function getNavigationLabel(): string
    {
        return __('Orders Tracking');
    }

    public function getHeading(): string|Htmlable
    {
        return __('Order Tracking');
    }

    protected static function getTableColumns(): array
    {
        return [
            TextColumn::make('tracking_number')
                ->label(__('Tracking Number'))
                ->searchable()
                ->weight(FontWeight::Bold),

            TextColumn::make('id')
                ->formatStateUsing(fn($state) => '#' . $state)
                ->label(__('Number'))
                ->searchable(),

            TextColumn::make('status')
                ->label(__('Status'))
                ->badge()
                ->searchable(),

            TextColumn::make('created_at')
                ->label(__('Created At'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function table(Table $table, bool $paginationStatus = true): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query, $livewire) {
                $search = $livewire->tableSearch ?? null;
                if ($search) {
                    Order::where('user_id', auth()->id())
                        ->where('tracking_number', 'like', "%{$search}%");
                } else {
                    $query->whereRaw('1 = 0'); // Ensures empty state until a search is made
                }
            })
            ->emptyStateHeading('Please enter a tracking number.')
            ->emptyStateDescription('No orders were found matching the given tracking number.')
            ->actions([
                ActionGroup::make([
                    Action::make('viewOrder')
                        ->label(__('View Order'))
                        ->icon('heroicon-o-eye')
                        ->url(fn ($record) => url('/client/my-orders/' . $record->id))
                        ->openUrlInNewTab(),                    DeleteAction::make(), //
                ])->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->poll(null)
            ->filtersFormColumns(4)
            ->columns(static::getTableColumns())
            ->paginationPageOptions([9, 18, 27])
            ->query(static::getQuery())
            ->recordUrl(fn ($record) => url('/client/my-orders/'. $record->id));
    }

    protected static function getQuery(): Builder
    {
        return Order::where('user_id', auth()->id());
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('client');
    }
}
