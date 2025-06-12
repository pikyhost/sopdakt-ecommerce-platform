<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WheelSpinResource\Pages;
use App\Models\WheelSpin;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WheelSpinResource extends Resource
{
    protected static ?string $model = WheelSpin::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static function getNavigationLabel(): string
    {
        return __('Wheel Spins');
    }

    public static function getModelLabel(): string
    {
        return __('Wheel Spin');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Wheel Spins');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Wheel Spins');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wheel.name')
                    ->label(__('Wheel Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User Name'))
                    ->searchable()
                    ->default('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('session_id')
                    ->label(__('Session ID'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('prize.name')
                    ->label(__('Prize Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('IP Address'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('next_spin_at')
                    ->label(__('Next Spin At'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),
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
            'index' => Pages\ListWheelSpins::route('/'),
            'create' => Pages\CreateWheelSpin::route('/create'),
            'edit' => Pages\EditWheelSpin::route('/{record}/edit'),
        ];
    }
}
