<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Filament\Resources\CouponResource\RelationManagers\UsagesRelationManager;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $modelLabel = 'Coupon';

    protected static ?string $navigationLabel = 'Coupons';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Coupon Information')
                    ->schema([
                        Forms\Components\Select::make('discount_id')
                            ->label(__('Discount'))
                            ->relationship('discount', 'name')
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('code')
                            ->default(Coupon::generateCode())
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(32)
                            ->unique(Coupon::class, 'code', ignoreRecord: true),

                        Forms\Components\TextInput::make('usage_limit_per_user')
                            ->label(__('Uses Per Customer'))
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('total_usage_limit')
                            ->label(__('Total Usage Limit'))
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('Code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount.name')
                    ->label(__('Discount'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state[app()->getLocale()] ?? $state['en'] ?? ''),

                Tables\Columns\TextColumn::make('usage_limit_per_user')
                    ->label(__('Per Customer'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ?? __('Unlimited')),

                Tables\Columns\TextColumn::make('total_usage_limit')
                    ->label(__('Total Limit'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ?? __('Unlimited')),

                Tables\Columns\TextColumn::make('usages_count')
                    ->label(__('Times Used'))
                    ->counts('usages')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount.starts_at')
                    ->label(__('Active From'))
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount.ends_at')
                    ->label(__('Active Until'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('discount_id')
                    ->label(__('Discount'))
                    ->relationship('discount', 'name')
                    ->searchable(),

                Tables\Filters\Filter::make('active')
                    ->label(__('Active Coupons'))
                    ->query(fn ($query) => $query->active()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UsagesRelationManager::class,
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Coupons');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Coupons');
    }

    public static function getNavigationLabel(): string
    {
        return __('Coupons');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Coupons');
    }

    public static function getLabel(): ?string
    {
        return __('Coupon');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
