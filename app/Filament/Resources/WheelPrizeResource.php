<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WheelPrizeResource\Pages;
use App\Models\WheelPrize;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WheelPrizeResource extends Resource
{
    use Translatable;

    protected static ?string $model = WheelPrize::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    public static function getNavigationLabel(): string
    {
        return __('Wheel Prizes');
    }

    public static function getModelLabel(): string
    {
        return __('Wheel Prize');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Wheel Prizes');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Wheel Prizes');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('wheel_id')
                        ->label(__('Wheel'))
                        ->relationship('wheel', 'name')
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('coupon_id')
                        ->label(__('Coupon'))
                        ->relationship('coupon', 'name')
                        ->nullable(),
                    Forms\Components\TextInput::make('probability')
                        ->label(__('Probability'))
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->default(1),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Is Active'))
                        ->default(true),
                ])->columns(1)
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wheel.name')
                    ->label(__('Wheel Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('coupon.name')
                    ->label(__('Coupon Name'))
                    ->searchable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('probability')
                    ->label(__('Probability'))
                    ->sortable(),
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
            ])
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
            'index' => Pages\ListWheelPrizes::route('/'),
            'create' => Pages\CreateWheelPrize::route('/create'),
            'edit' => Pages\EditWheelPrize::route('/{record}/edit'),
        ];
    }
}
