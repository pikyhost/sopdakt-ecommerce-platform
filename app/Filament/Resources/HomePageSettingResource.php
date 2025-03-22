<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageSettingResource\Pages;
use App\Models\HomePageSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomePageSettingResource extends Resource
{
    protected static ?string $model = HomePageSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('Pages Settings Management');
    }

    public static function getModelLabel(): string
    {
        return __('Home page settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Home page settings');
    }

    public static function getLabel(): string
    {
        return __('Home page settings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('main_heading')
                    ->label(__('Main Heading'))
                    ->required()
                    ->maxLength(255)
                    ->default(__('Spring / Summer Season')),
                Forms\Components\TextInput::make('discount_text')
                    ->label(__('Discount Text'))
                    ->required()
                    ->maxLength(255)
                    ->default(__('Up to')),
                Forms\Components\TextInput::make('discount_value')
                    ->label(__('Discount Value'))
                    ->required()
                    ->maxLength(255)
                    ->default(__('50% off')),
                Forms\Components\TextInput::make('starting_price')
                    ->label(__('Starting Price'))
                    ->required()
                    ->numeric()
                    ->default(19.99),
                Forms\Components\TextInput::make('currency_symbol')
                    ->label(__('Currency Symbol'))
                    ->required()
                    ->maxLength(255)
                    ->default('$'),
                Forms\Components\TextInput::make('button_text')
                    ->label(__('Button Text'))
                    ->required()
                    ->maxLength(255)
                    ->default(__('Shop Now')),
                Forms\Components\TextInput::make('button_url')
                    ->label(__('Button URL'))
                    ->required()
                    ->maxLength(255)
                    ->default('#'),
                Forms\Components\FileUpload::make('background_image')
                    ->label(__('Background Image'))
                    ->image(),
                Forms\Components\FileUpload::make('layer_image')
                    ->label(__('Layer Image'))
                    ->image(),
                Forms\Components\FileUpload::make('thumbnail_image')
                    ->label(__('Thumbnail Image'))
                    ->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('main_heading')
                    ->label(__('Main Heading'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount_text')
                    ->label(__('Discount Text'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount_value')
                    ->label(__('Discount Value'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('starting_price')
                    ->label(__('Starting Price'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency_symbol')
                    ->label(__('Currency Symbol'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('button_text')
                    ->label(__('Button Text'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('button_url')
                    ->label(__('Button URL'))
                    ->searchable(),
                Tables\Columns\ImageColumn::make('background_image')
                    ->label(__('Background Image')),
                Tables\Columns\ImageColumn::make('layer_image')
                    ->label(__('Layer Image')),
                Tables\Columns\ImageColumn::make('thumbnail_image')
                    ->label(__('Thumbnail Image')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomePageSettings::route('/'),
            'edit' => Pages\EditHomePageSetting::route('/{record}/edit'),
        ];
    }
}
