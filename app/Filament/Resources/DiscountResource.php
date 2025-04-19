<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountResource\Pages;
use App\Models\Discount;
use App\Models\Product;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webbingbrasil\FilamentAdvancedFilter\Filters\BooleanFilter;

class DiscountResource extends Resource
{
    use Translatable;

    protected static ?string $model = Discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Basic Information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Name'))
                            ->required(),

                        TextArea::make('description')
                            ->label(__('Description'))
                            ->nullable(),

                        Select::make('applies_to')
                            ->label(__('Applies To'))
                            ->options([
                                'product' => __('Product'),
                                'category' => __('Category'),
                                'cart' => __('Cart'),
                                'collection' => __('Collection'),
                            ])
                            ->required()
                            ->live(),
                    ]),

                Section::make(__('Applies To Settings'))
                    ->schema([
                        // Show products if applies_to = product
                        Select::make('products')
                            ->multiple()
                            ->label(__('Select Products'))
                            ->relationship(
                                name: 'products',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, callable $get) => $query->when(
                                    $get('categories'),
                                    fn ($query, $categories) => $query->whereIn('category_id', $categories)
                                )
                            )
                            ->preload()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $products = Product::whereIn('id', $state)->get();
                                $totalPrice = $products->sum(fn ($product) => $product->price ?? 0);

                                $set('price', $totalPrice);

                                $discountType = $get('discount_type');
                                $value = $get('value');

                                if ($discountType === 'percentage') {
                                    $set('after_discount_price', $totalPrice - ($totalPrice * ($value / 100)));
                                } elseif ($discountType === 'fixed') {
                                    $set('after_discount_price', $totalPrice - $value);
                                } else {
                                    $set('after_discount_price', null);
                                }
                            }),

                        // Show categories if applies_to is category or product
                        Select::make('categories')
                            ->multiple()
                            ->label(__('Select Categories'))
                            ->helperText(__('The discount will apply to products under these categories.'))
                            ->relationship('categories', 'name')
                            ->reactive()
                            ->preload()
                            ->searchable()
                            ->visible(fn ($get) => in_array($get('applies_to'), ['category', 'product'])),

                        // Show collections if applies_to is collection
                        Select::make('collections')
                            ->multiple()
                            ->label(__('Select Collections'))
                            ->relationship('collections', 'name')
                            ->preload()
                            ->searchable()
                            ->visible(fn ($get) => $get('applies_to') === 'collection'),
                    ]),

                Section::make(__('Discount Settings'))
                    ->columns(2)
                    ->schema([
                        Select::make('discount_type')
                            ->label(__('Discount Type'))
                            ->options([
                                'percentage' => __('Percentage'),
                                'fixed' => __('Fixed Amount'),
                                'free_shipping' => __('Free Shipping'),
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateDiscountPrice($set, $get)),

                        TextInput::make('value')
                            ->label(__('Value'))
                            ->numeric()
                            ->nullable()
                            ->reactive()
                            ->visible(fn ($get) => in_array($get('discount_type'), ['percentage', 'fixed']))
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::calculateDiscountPrice($set, $get)),
                    ]),

                Section::make(__('Price Calculation'))
                    ->columns(3)
                    ->schema([
                        TextInput::make('price')
                            ->label(__('Original Price (for preview)'))
                            ->numeric()
                            ->readOnly(),

                        TextInput::make('after_discount_price')
                            ->label(__('After Discount Price'))
                            ->numeric()
                            ->readOnly(),

                        TextInput::make('min_order_value')
                            ->label(__('Minimum Order Value'))
                            ->numeric()
                            ->nullable(),
                    ]),

                Section::make(__('Validity'))
                    ->schema([
                        TextInput::make('usage_limit')
                            ->label(__('Usage Limit'))
                            ->helperText(__('discounts.fields.usage_limit_help'))
                            ->columnSpanFull()
                            ->numeric()
                            ->nullable(),

                        DateTimePicker::make('starts_at')
                            ->label(__('Starts At'))
                            ->nullable(),

                        DateTimePicker::make('ends_at')
                            ->label(__('Ends At'))
                            ->nullable(),

                        Checkbox::make('requires_coupon')
                            ->label(__('Requires Coupon')),

                        Checkbox::make('is_active')
                            ->label(__("Active")),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('discount_type')
                    ->label(__('Type')),

                TextColumn::make('applies_to')
                    ->label(__('Applies To')),

                TextColumn::make('value')
                    ->label(__('Value')),

                TextColumn::make('after_discount_price')
                    ->label(__('After Discount Price')),

                Tables\Columns\IconColumn::make('requires_coupon')
                    ->boolean()
                    ->label(__('Requires Coupon')),

                TextColumn::make('starts_at')
                    ->label(__('Starts At'))
                    ->dateTime(),

                TextColumn::make('ends_at')
                    ->label(__('Ends At'))
                    ->dateTime(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__("Active")),
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                // Filter by discount type
                SelectFilter::make('discount_type')
                    ->label(__('Discount Type'))
                    ->options([
                        'percentage' => __('Percentage'),
                        'fixed' => __('Fixed Amount'),
                        'free_shipping' => __('Free Shipping'),
                    ]),

                // Filter by applies_to
                SelectFilter::make('applies_to')
                    ->label(__('Applies To'))
                    ->options([
                        'product' => __('Product'),
                        'category' => __('Category'),
                        'cart' => __('Cart'),
                        'collection' => __('Collection'),
                    ]),

                BooleanFilter::make('is_active')
                    ->label(__("Active")),

                // Filter by specific collection
                SelectFilter::make('collections')
                    ->label(__('Collection'))
                    ->relationship('collections', 'name'),

                // Filter by date range (e.g. active discounts)
                Filter::make('active')
                    ->label(__('Currently Active'))
                    ->query(fn (Builder $query) =>
                    $query->where(function ($query) {
                        $query->whereNull('starts_at')
                            ->orWhere('starts_at', '<=', now());
                    })->where(function ($query) {
                        $query->whereNull('ends_at')
                            ->orWhere('ends_at', '>=', now());
                    })
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Discount');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Discounts');
    }

    public static function getNavigationLabel(): string
    {
        return __('Discounts');
    }


    public static function getPluralLabel(): ?string
    {
        return __('Discounts');
    }

    public static function getLabel(): ?string
    {
        return __('Discount');
    }

    public static function calculateDiscountPrice(callable $set, callable $get): void {
        $price = $get('price');
        $value = $get('value');
        $discountType = $get('discount_type');

        if (! $price) {
            $set('after_discount_price', null);
            return;
        }

        if ($discountType === 'percentage') {
            $set('after_discount_price', $price - ($price * ($value / 100)));
        } elseif ($discountType === 'fixed') {
            $set('after_discount_price', $price - $value);
        } else {
            $set('after_discount_price', null);
        }
    }
}
