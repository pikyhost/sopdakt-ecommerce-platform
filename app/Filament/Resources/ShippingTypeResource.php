<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingTypeResource\Pages;
use App\Models\ShippingType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ShippingTypeResource extends Resource
{
    use Translatable;

    protected static ?string $model = ShippingType::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    public static function getNavigationLabel(): string
    {
        return __('shipping_type.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping Management'); //Products Management
    }

    public static function getModelLabel(): string
    {
        return __('shipping_type.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('shipping_type.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('shipping_type.name'))
                    ->required()
                    ->maxLength(255)
                    ->translateLabel(),

                Forms\Components\TextInput::make('shipping_cost')
                    ->label(__('shipping_type.shipping_cost'))
                    ->required(),

                Forms\Components\TextInput::make('shipping_estimate_time')
                    ->label(__('shipping_type.shipping_estimate_time'))
                    ->required(),

                Forms\Components\TextInput::make('description')
                    ->label(__('Description'))
                    ->nullable(),

                Forms\Components\Checkbox::make('status')
                    ->label(__('Is Active?')),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('shipping_type.name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('shipping_cost')
                    ->weight(FontWeight::Bold)
                    ->label(__('shipping_type.shipping_cost'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_estimate_time')
                    ->badge()
                    ->label(__('shipping_type.shipping_estimate_time')),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description')),

                Tables\Columns\IconColumn::make('status')
                    ->label(__('Is Active?'))
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->columnSpanFull()
                    ->label(__('shipping_type.status'))
                    ->trueLabel(__('shipping_type.active'))
                    ->falseLabel(__('shipping_type.inactive')),
            ], Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageShippingTypes::route('/'),
        ];
    }
}
