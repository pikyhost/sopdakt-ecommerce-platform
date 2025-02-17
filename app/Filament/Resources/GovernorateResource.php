<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GovernorateResource\Pages;
use App\Models\Governorate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GovernorateResource extends Resource
{
    use Translatable;

    protected static ?string $model = Governorate::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function getNavigationLabel(): string
    {
        return __('governorates'); // Translated to "Governorates"
    }

    public static function getModelLabel(): string
    {
        return __('governorate'); // Translated to "Governorate"
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('governorates'); // Translated to "Governorates"
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Shipping & Countries'); //Products Attributes Management
    }

    public static function getLabel(): ?string
    {
        return __('governorate'); // Translated to "Governorate"
    }

    public static function getPluralModelLabel(): string
    {
        return __('governorates'); // Translated to "Governorates"
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->unique('cities', 'name', fn ($record) => $record) // Explicitly check the unique rule for the 'name' field
                    ->required()
                    ->maxLength(255)
                    ->label(__('name')),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required()
                    ->label(__('country_name')),

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
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label(__('name')),
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->label(__('country_name')),

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
                Tables\Filters\SelectFilter::make('country_id')
                    ->columnSpanFull()
                    ->label(__('country'))
                    ->relationship('country', 'name')
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
            'index' => Pages\ManageGovernorates::route('/'),
        ];
    }
}
