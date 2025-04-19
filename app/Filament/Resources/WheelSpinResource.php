<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WheelSpinResource\Pages;
use App\Models\WheelSpin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WheelSpinResource extends Resource
{
    protected static ?string $model = WheelSpin::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'wheel_of_fortune';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label(__('User')),
                Forms\Components\Select::make('wheel_id')
                    ->relationship('wheel', 'name')
                    ->required()
                    ->label(__('Wheel')),
                Forms\Components\Select::make('wheel_prize_id')
                    ->relationship('prize', 'name')
                    ->nullable()
                    ->label(__('Prize')),
                Forms\Components\Toggle::make('is_winner')
                    ->required()
                    ->default(false)
                    ->label(__('Is Winner')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->label(__('User')),
                Tables\Columns\TextColumn::make('wheel.name')
                    ->searchable()
                    ->label(__('Wheel')),
                Tables\Columns\TextColumn::make('prize.name')
                    ->default('-')
                    ->searchable()
                    ->label(__('Prize')),
                Tables\Columns\IconColumn::make('is_winner')
                    ->boolean()
                    ->label(__('Winner')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('Spin Date')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('wheel')
                    ->columnSpanFull()
                    ->relationship('wheel', 'name')
                    ->label(__('Wheel')),
                Tables\Filters\Filter::make('winners')
                    ->columnSpanFull()
                    ->query(fn (Builder $query): Builder => $query->where('is_winner', true))
                    ->label(__('Only Winners')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete selected')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWheelSpins::route('/'),
            'create' => Pages\CreateWheelSpin::route('/create'),
            'edit' => Pages\EditWheelSpin::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('Spin');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Spins');
    }

    public static function getNavigationLabel(): string
    {
        return __('Spins History');
    }
}
