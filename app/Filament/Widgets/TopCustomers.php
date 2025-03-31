<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\App;

class TopCustomers extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1000;

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Top Customers'))
            ->description(function () {
                $locale = App::getLocale();

                return $locale === 'ar'
                    ? 'تعرف على 🌟 العملاء الأكثر شراءً والأكثر إنفاقًا 💰 بناءً على عدد الطلبات أو إجمالي المشتريات 🏆'
                    : 'Discover 🌟 the top-spending and most frequent customers 💰 based on order count or total purchases 🏆';
            })
            ->paginated(false)
            ->query(
                \App\Models\User::query()
                    ->select('users.id', 'users.name')
                    ->selectRaw('COUNT(orders.id) as total_orders, SUM(orders.total) as total_spent')
                    ->join('orders', 'users.id', '=', 'orders.user_id')
                    ->groupBy('users.id', 'users.name')
                    ->orderByDesc('total_spent') // Sort by total spending first
                    ->orderByDesc('total_orders')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('updated_at')
                    ->fontFamily(FontFamily::Mono)
                    ->size(TextColumnSize::Large)
                    ->formatStateUsing(fn ($state) => '#'.$state)
                    ->rowIndex()
                    ->weight(FontWeight::Bold)
                    ->color('primary')
                    ->label(__('Position'))
                    ->copyable(false)
                    ->toggleable(false)
                    ->extraAttributes([
                        'class' => 'space-y-4 flex items-center justify-center',
                        'style' => 'margin-bottom: 16px; text-align: center; vertical-align: middle;',
                    ]),

                ImageColumn::make('avatar_url')
                    ->label(__('Avatar'))
                    ->defaultImageUrl(function ($record) {
                        $nameParts = explode(' ', trim($record->name));
                        $initials = count($nameParts) === 1
                            ? mb_strtoupper(mb_substr($nameParts[0], 0, 1))
                            : mb_strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr($nameParts[1], 0, 1));
                        return 'https://ui-avatars.com/api/?background=000000&color=fff&name=' . urlencode($initials);
                    })
                    ->circular()
                    ->grow(false),

                TextColumn::make('name')
                    ->label(__('name'))
                    ->tooltip(fn (TextColumn $column): ?string => static::getTooltip($column))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('id')
                    ->label(__('User ID'))
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_orders')
                    ->formatStateUsing(function ($state) {
                        $locale = app()->getLocale();
                        return $locale === 'ar'
                            ? "{$state} طلب" // Example: "50 طلب منفّذ"
                            : "{$state} orders"; // Example: "50 completed orders"
                    })
                    ->color('success')
                    ->badge()
                    ->label(__('Total Orders'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_spent')
                    ->formatStateUsing(function ($state) {
                        $currency = Setting::getCurrency();
                        $symbol = $currency?->symbol ?? '';

                        $locale = app()->getLocale();
                        return $locale === 'ar'
                            ? "{$state} {$symbol}" // Example: "5000 $ إجمالي إنفاق"
                            : "{$symbol}{$state}"; // Example: "$5000 total spent"
                    })
                    ->color('success')
                    ->badge()
                    ->label(__('Total Spent'))
                    ->sortable(),


                TextColumn::make('email')
                    ->placeholder('-')
                    ->label(__('Email address'))
                    ->tooltip(fn (TextColumn $column): ?string => static::getTooltip($column))
                    ->searchable()
                    ->iconColor('primary')
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Phone'))
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('second_phone')
                    ->iconColor('primary')
                    ->icon('heroicon-o-phone')
                    ->label(__('Second Phone'))
                    ->placeholder('-')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->color('gray')
                    ->icon('heroicon-o-eye')
                    ->label(__('View'))
                    ->url(fn (User $record): string => url('/admin/users/'.$record->id)),
            ]);
    }

    protected static function getTooltip(TextColumn $column): ?string
    {
        return strlen($column->getState()) > $column->getCharacterLimit() ? $column->getState() : null;
    }
}
