<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CityResource extends Resource
{
    use Translatable;

    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    public static function getNavigationLabel(): string
    {
        return __('cities'); // Translated to "Governorates"
    }

    public static function getModelLabel(): string
    {
        return __('cities'); // Translated to "Governorate"
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('cities'); // Translated to "Governorates"
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping & Countries'); //Products Attributes Management
    }

    public static function getLabel(): ?string
    {
        return __('cities'); // Translated to "Governorate"
    }

    public static function getPluralModelLabel(): string
    {
        return __('cities'); // Translated to "Governorates"
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255)
                    ->label(__('name')),
                Forms\Components\Select::make('governorate_id')
                    ->relationship('governorate', 'name')
                    ->required()
                    ->label(__('governorate_name')),

                Forms\Components\TextInput::make('cost')
                    ->label(__('shipping_cost.cost'))
                    ->required()
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('shipping_estimate_time')
                    ->label(__('shipping_cost.shipping_estimate_time'))
                    ->required()
                    ->maxLength(255)
                    ->default('0-0'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('name')),
                Tables\Columns\TextColumn::make('governorate.name')
                    ->searchable()
                    ->label(__('governorate_name')),
                Tables\Columns\TextColumn::make('cost')
                    ->label(__('shipping_cost.cost'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_estimate_time')
                    ->label(__('shipping_cost.shipping_estimate_time'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('created_at')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('updated_at')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('governorate_id')
                    ->columnSpanFull()
                    ->label(__('governorate'))
                    ->relationship('governorate', 'name')
            ], Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('delete_bulk')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCities::route('/'),
        ];
    }
}
