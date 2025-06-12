<?php

namespace App\Filament\Resources;

use App\Enums\CouponType;
use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    use Translatable;

    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function getNavigationLabel(): string
    {
        return __('Coupons');
    }

    public static function getModelLabel(): string
    {
        return __('Coupon');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Coupons');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Coupons');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('Name'))
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(255)
                        ->unique(Coupon::class, 'name', ignoreRecord: true),
                    Forms\Components\TextInput::make('code')
                        ->label(__('Code'))
                        ->required()
                        ->maxLength(255)
                        ->unique(Coupon::class, 'code', ignoreRecord: true),
                    Forms\Components\Select::make('type')
                        ->label(__('Type'))
                        ->options(CouponType::class)
                        ->enum(CouponType::class)
                        ->required(),
                    Forms\Components\TextInput::make('value')
                        ->label(__('Value'))
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),
                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label(__('Expires At'))
                        ->nullable(),
                    Forms\Components\TextInput::make('min_order_amount')
                        ->label(__('Minimum Order Amount'))
                        ->numeric()
                        ->minValue(1)
                        ->nullable(),
                    Forms\Components\TextInput::make('usage_limit')
                        ->label(__('Usage Limit'))
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('usage_limit_per_user')
                        ->label(__('Usage Limit Per User'))
                        ->required()
                        ->numeric(),
                    Forms\Components\Checkbox::make('is_active')
                        ->label(__('Is Active'))
                        ->columnSpanFull()
                        ->default(true),
                ])->columns(2)
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('Code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->formatStateUsing(fn (string $state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('value')
                    ->label(__('Value'))
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '-'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label(__('Expires At'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_order_amount')
                    ->label(__('Minimum Order Amount'))
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Is Active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Is Active')),
            ], Tables\Enums\FiltersLayout::Modal)
            ->actions([
                Tables\Actions\EditAction::make()->label(__('Edit')),
                Tables\Actions\DeleteAction::make()->label(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected')),
            ]);
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
