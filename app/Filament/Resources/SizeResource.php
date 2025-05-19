<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SizeResource\Pages;
use App\Models\Size;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SizeResource extends Resource
{
    use Translatable;

    protected static ?string $model = Size::class;

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    public static function getNavigationLabel(): string
    {
        return __('sizes.label');
    }

    public static function getModelLabel(): string
    {
        return __('sizes.label');
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('sizes.label');
    }


    public static function getNavigationGroup(): ?string
    {
        return __('Products Management'); //Products Attributes Management
    }

    public static function getLabel(): ?string
    {
        return __('sizes.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sizes.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->columnSpanFull()
                    ->label(__('name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Checkbox::make('is_active')
                    ->columnSpanFull()
                    ->label(__('is_active'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('name'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('is_active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSizes::route('/'),
            'create' => Pages\CreateSize::route('/create'),
            'edit' => Pages\EditSize::route('/{record}/edit'),
        ];
    }
}
