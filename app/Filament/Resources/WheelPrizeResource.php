<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WheelPrizeResource\Pages;
use App\Models\Coupon;
use App\Models\Discount;
use App\Models\WheelPrize;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WheelPrizeResource extends Resource
{
    protected static ?string $model = WheelPrize::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'wheel_of_fortune';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Prize Information'))
                    ->schema([
                        Forms\Components\Select::make('wheel_id')
                            ->relationship('wheel', 'name')
                            ->required()
                            ->label(__('Wheel')),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label(__('Name')),
                        Forms\Components\Select::make('type')
                            ->options([
                                'discount' => __('Discount'),
                                'coupon' => __('Coupon'),
                                'points' => __('Points'),
                                'product' => __('Product'),
                                'none' => __('None (Just for show)'),
                            ])
                            ->required()
                            ->live()
                            ->default('none')
                            ->label(__('Type')),
                        Forms\Components\TextInput::make('value')
                            ->numeric()
                            ->nullable()
                            ->visible(fn (Forms\Get $get): bool => in_array($get('type'), ['points']))
                            ->label(__('Value')),
                        Forms\Components\Select::make('coupon_id')
                            ->options(Coupon::all()->pluck('code', 'id'))
                            ->searchable()
                            ->nullable()
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'coupon')
                            ->label(__('Coupon')),
                        Forms\Components\Select::make('discount_id')
                            ->options(Discount::all()->pluck('name', 'id'))
                            ->searchable()
                            ->nullable()
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'discount')
                            ->label(__('Discount')),
                    ])->columns(2),

                Forms\Components\Section::make(__('Probability & Availability'))
                    ->schema([
                        Forms\Components\TextInput::make('probability')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(10)
                            ->suffix('%')
                            ->label(__('Probability')),
                        Forms\Components\Toggle::make('is_available')
                            ->required()
                            ->default(true)
                            ->label(__('Is Available')),
                    ])->columns(2),

                Forms\Components\Section::make(__('Limits'))
                    ->schema([
                        Forms\Components\TextInput::make('daily_limit')
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->label(__('Daily Limit')),
                        Forms\Components\TextInput::make('total_limit')
                            ->numeric()
                            ->minValue(1)
                            ->nullable()
                            ->label(__('Total Limit')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wheel.name')
                    ->sortable()
                    ->label(__('Wheel')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('Name')),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'discount' => 'success',
                        'coupon' => 'warning',
                        'points' => 'info',
                        'product' => 'primary',
                        default => 'gray',
                    })
                    ->label(__('Type')),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn (?int $state, WheelPrize $record): string => match ($record->type) {
                        'points' => $state . ' ' . __('points'),
                        default => '-',
                    })
                    ->label(__('Value')),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                    ->label(__('Available')),
                Tables\Columns\TextColumn::make('probability')
                    ->numeric()
                    ->suffix('%')
                    ->sortable()
                    ->label(__('Probability')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('wheel')
                    ->relationship('wheel', 'name')
                    ->label(__('Wheel')),
                Tables\Filters\Filter::make('is_available')
                    ->query(fn (Builder $query): Builder => $query->where('is_available', true))
                    ->label(__('Only Available')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete selected')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWheelPrizes::route('/'),
            'create' => Pages\CreateWheelPrize::route('/create'),
            'edit' => Pages\EditWheelPrize::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Prize');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Prizes');
    }

    public static function getNavigationLabel(): string
    {
        return __('Prizes');
    }
}
