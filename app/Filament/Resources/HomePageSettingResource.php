<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageSettingResource\Pages;
use App\Models\HomePageSetting;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
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
                Section::make('Slider Content')
                    ->schema([
                        TextInput::make('main_heading')
                            ->label(__('Main Heading'))
                            ->required()
                            ->maxLength(255)
                            ->default(__('Spring / Summer Season')),

                        TextInput::make('discount_text')
                            ->label(__('Discount Text'))
                            ->required()
                            ->maxLength(255)
                            ->default(__('Up to')),

                        TextInput::make('discount_value')
                            ->label(__('Discount Value'))
                            ->required()
                            ->maxLength(255)
                            ->default(__('50% off')),

                        TextInput::make('starting_price')
                            ->label(__('Starting Price'))
                            ->required()
                            ->numeric()
                            ->default(19),

                        TextInput::make('currency_symbol')
                            ->label(__('Currency Symbol'))
                            ->required()
                            ->maxLength(10)
                            ->default('$'),

                        TextInput::make('button_text')
                            ->label(__('Button Text'))
                            ->required()
                            ->maxLength(255)
                            ->default(__('Shop Now')),

                        TextInput::make('button_url')
                            ->label(__('Button URL'))
                            ->required()
                            ->maxLength(255)
                            ->default('#'),
                    ])
                    ->columns(2),

                Section::make('Slider 1 Images')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('slider1_background_image')
                            ->collection('slider1_background')
                            ->label(__('Slider 1 Background Image'))
                            ->singleFile()
                            ->image(),

                        SpatieMediaLibraryFileUpload::make('slider1_layer_image')
                            ->collection('slider1_layer')
                            ->label(__('Slider 1 Layer Image'))
                            ->singleFile()
                            ->image(),

                        SpatieMediaLibraryFileUpload::make('slider1_thumbnail_image')
                            ->collection('slider1_thumbnail')
                            ->label(__('Slider 1 Thumbnail Image'))
                            ->singleFile()
                            ->image(),
                    ])
                    ->columns(3),

                Section::make('Slider 2 Images')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('slider2_background_image')
                            ->collection('slider2_background')
                            ->label(__('Slider 2 Background Image'))
                            ->singleFile()
                            ->image(),

                        SpatieMediaLibraryFileUpload::make('slider2_layer_image')
                            ->collection('slider2_layer')
                            ->label(__('Slider 2 Layer Image'))
                            ->singleFile()
                            ->image(),

                        SpatieMediaLibraryFileUpload::make('slider2_thumbnail_image')
                            ->collection('slider2_thumbnail')
                            ->label(__('Slider 2 Thumbnail Image'))
                            ->singleFile()
                            ->image(),
                    ])
                    ->columns(3),
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
