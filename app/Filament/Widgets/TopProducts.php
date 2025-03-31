<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Setting;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\App;
use Mokhosh\FilamentRating\Columns\RatingColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;

class TopProducts extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Top Products'))
            ->description(function () {
                $locale = App::getLocale();

                return $locale === 'ar'
                    ? 'تعرف على المنتجات الأكثر طلبًا بناءً على عدد المرات التي تم طلبها 🔥🛒'
                    : 'Discover the most popular products based on the number of times they have been ordered 🛒🔥';
            })
            ->query(
                \App\Models\Product::query()
                    ->select('products.*')
                    ->selectRaw('SUM(order_items.quantity) as total_sold')
                    ->join('order_items', 'products.id', '=', 'order_items.product_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
//                    ->where('orders.status', 'completed')
                    ->groupBy('products.id')
                    ->orderByDesc('total_sold')
                    ->limit(10)
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('id')
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

                Tables\Columns\SpatieMediaLibraryImageColumn::make('feature_product_image')
                    ->toggleable(true, false)
                    ->circular()
                    ->simpleLightbox()
                    ->placeholder('-')
                    ->collection('feature_product_image')
                    ->label(__('products.Product Image')),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('products.Product Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_sold')
                    ->formatStateUsing(function ($state) {
                        $locale = app()->getLocale();
                        return $locale === 'ar'
                            ? "{$state} طلب مبيع"  // Example: "100 طلب مبيع"
                            : "{$state} units sold"; // Example: "$100 units sold"
                    })
                    ->color('success')
                    ->badge()
                    ->fontFamily(FontFamily::Sans)
                    ->weight(FontWeight::Bold)
                    ->label(__('Total Sold')),


                RatingColumn::make('fake_average_rating')
                    ->label(__('products.Rating')),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('products.Price'))
                    ->formatStateUsing(function ($state) {
                        $currency = Setting::getCurrency();
                        $symbol = $currency?->symbol ?? '';

                        $locale = app()->getLocale();
                        return $locale === 'ar' ? "{$state} {$symbol}" : "{$symbol} {$state}";
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('after_discount_price')
                    ->formatStateUsing(function ($state) {
                        $currency = Setting::getCurrency();
                        $symbol = $currency?->symbol ?? '';

                        $locale = app()->getLocale();
                        return $locale === 'ar' ? "{$state} {$symbol}" : "{$symbol} {$state}";
                    })
                    ->label(__('products.After Discount Price')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('products.Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->actions([
                Tables\Actions\Action::make('view')
                    ->color('gray')
                    ->icon('heroicon-o-eye')
                    ->label(__('View'))
                    ->url(fn (Product $record): string => url('/products/'.$record->slug)),
            ]);
    }
}
