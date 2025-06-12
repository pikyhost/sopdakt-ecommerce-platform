<?php

namespace App\Filament\Resources\WheelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\Concerns\Translatable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PrizesRelationManager extends RelationManager
{
    use Translatable;

    protected static string $relationship = 'prizes';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Prizes');
    }

    protected static function getModelLabel(): ?string
    {
        return __('Prize');
    }

    protected static function getPluralModelLabel(): ?string
    {
        return __('Prizes');
    }

    protected static function getPluralRecordLabel(): ?string
    {
        return __('Prizes');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute(__('Name'))
            ->columns([
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
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('Create Prize')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('Edit')),
                Tables\Actions\DeleteAction::make()->label(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected')),
            ]);
    }
}
