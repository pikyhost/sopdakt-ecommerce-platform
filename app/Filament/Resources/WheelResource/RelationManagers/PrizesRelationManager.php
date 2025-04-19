<?php

namespace App\Filament\Resources\WheelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrizesRelationManager extends RelationManager
{
    protected static string $relationship = 'prizes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'discount' => 'Discount',
                        'coupon' => 'Coupon',
                        'points' => 'Points',
                        'product' => 'Product',
                        'none' => 'None (Just for show)',
                    ])
                    ->required()
                    ->live()
                    ->default('none'),
                Forms\Components\TextInput::make('value')
                    ->numeric()
                    ->nullable()
                    ->visible(fn (Forms\Get $get): bool => in_array($get('type'), ['points'])),
                Forms\Components\TextInput::make('probability')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->default(10)
                    ->suffix('%'),
                Forms\Components\Toggle::make('is_available')
                    ->required()
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'discount' => 'success',
                        'coupon' => 'warning',
                        'points' => 'info',
                        'product' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn (?int $state, $record): string => match ($record->type) {
                        'points' => $state . ' points',
                        default => '-',
                    }),
                Tables\Columns\TextColumn::make('probability')
                    ->suffix('%'),
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
