<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Setting;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Livewire\Attributes\On;

class LastOrders extends BaseWidget
{
    public Carbon $fromDate;
    public Carbon $toDate;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string|Htmlable|null
    {
        return __('Last Orders');
    }

    public function mount(): void
    {
        $this->fromDate = now()->subWeek();
        $this->toDate = now();
    }

    #[On('updateFromDate')]
    public function updateFromDate(string $from): void
    {
        $this->fromDate = Carbon::parse($from);
        $this->dispatch('$refresh');
    }

    #[On('updateToDate')]
    public function updateToDate(string $to): void
    {
        $this->toDate = Carbon::parse($to);
        $this->dispatch('$refresh');
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Last Orders'))
            ->paginationPageOptions([5, 10])
            ->query(
                Order::query()
                    ->whereBetween('created_at', [$this->fromDate, $this->toDate])
                    ->latest('created_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->color('primary')
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn($state) => '#' . $state)
                    ->label(__('Number'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->formatStateUsing(function ($record) {
                        return $record->user->name.' (#'.$record->user_id.')';
                    })
                    ->label(__('User Name'))
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('contact.name')
                    ->formatStateUsing(function ($record) {
                        return $record->contact->name.' (#'.$record->contact_id.')';
                    })
                    ->label(__('Contact Name'))
                    ->searchable()
                    ->placeholder('-')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('user.phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('User Phone Number'))
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('contact.phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Contact Phone Number'))
                    ->placeholder('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total')
                    ->label(__('Total'))
                    ->formatStateUsing(function ($state) {
                        $currency = Setting::getCurrency();
                        $symbol = $currency?->code ?? '';

                        $locale = app()->getLocale();
                        return $locale === 'en' ? "{$state} {$symbol}" : "{$symbol} {$state}";
                    })
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->color('gray')
                    ->icon('heroicon-o-shopping-bag')
                    ->label(__('Open'))
                    ->url(fn (Order $record): string => url('/admin/orders/'.$record->id)),
            ]);
    }
}
