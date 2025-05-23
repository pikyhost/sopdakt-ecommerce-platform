<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductCouponResource\Pages\CreateProductCoupon;
use App\Filament\Resources\ProductCouponResource\Pages\EditProductCoupon;
use App\Filament\Resources\ProductCouponResource\Pages\ListProductCoupons;
use App\Models\ProductCoupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductCouponResource extends Resource
{
    protected static ?string $model = ProductCoupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coupon Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('FLASHSALE50'),

                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->required()
                            ->preload(),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('original_price')
                            ->required()
                            ->numeric(),

                        Forms\Components\TextInput::make('discounted_price')
                            ->required()
                            ->numeric(),
                    ])->columns(2),

                Forms\Components\Section::make('Availability')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->native(false),

                        Forms\Components\DateTimePicker::make('ends_at')
                            ->native(false)
                            ->rules(['after:starts_at']),

                        Forms\Components\TextInput::make('usage_limit')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Leave empty for unlimited'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('original_price')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discounted_price')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_percentage')
                    ->label('Discount')
                    ->numeric(),

                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('usage_limit')
                    ->label('Limit')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->query(fn ($query) => $query->where('is_active', true)),

                Tables\Filters\Filter::make('expired')
                    ->query(fn ($query) => $query->where('ends_at', '<', now())),

                Tables\Filters\SelectFilter::make('product')
                    ->relationship('product', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('starts_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductCoupons::route('/'),
            'create' => CreateProductCoupon::route('/create'),
            'edit' => EditProductCoupon::route('/{record}/edit'),
        ];
    }
}
