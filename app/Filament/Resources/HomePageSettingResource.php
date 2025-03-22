<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageSettingResource\Pages;
use App\Models\HomePageSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;

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
                    ->collapsed()
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

                Section::make(__('Slider Images'))
                    ->collapsed()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('slider1_image')
                            ->collection('slider1_image')
                            ->label(__('Slider 1 Background Image'))
                            ->image(),

                        SpatieMediaLibraryFileUpload::make('slider2_image')
                            ->collection('slider2_image')
                            ->label(__('Slider 2 Background Image'))
                            ->image(),
                    ])->columnSpanFull(),

                Section::make(__('Center Section'))
                    ->collapsed()
                    ->schema([
                        TextInput::make('center_main_heading')
                            ->label(__('Center Main Heading'))
                            ->required(),

                        TextInput::make('center_button_text')
                            ->label(__('Center Button Text'))
                            ->required(),

                        TextInput::make('center_button_url')
                            ->label(__('Center Button URL'))
                            ->required(),
                        SpatieMediaLibraryFileUpload::make('center_image')
                            ->collection('center_image')
                            ->label(__('Center Image'))
                            ->image(),
                    ])->columnSpanFull(),

                Section::make(__('Last Section'))
                    ->collapsed()
                    ->schema([
                        TextInput::make('last1_heading')
                            ->label(__('Last 1 Heading'))
                            ->required(),

                        TextInput::make('last1_subheading')
                            ->label(__('Last 1 Subheading'))
                            ->required(),

                        TextInput::make('last1_button_text')
                            ->label(__('Last 1 Button Text'))
                            ->required(),

                        TextInput::make('last1_button_url')
                            ->label(__('Last 1 Button URL'))
                            ->required(),

                        SpatieMediaLibraryFileUpload::make('last1_image')
                            ->collection('last1_image')
                            ->label(__('Last First Image'))
                            ->image(),


                        TextInput::make('last2_heading')
                            ->label(__('Last 2 Heading'))
                            ->required(),

                        TextInput::make('last2_subheading')
                            ->label(__('Last 2 Subheading'))
                            ->required(),

                        TextInput::make('last2_button_text')
                            ->label(__('Last 2 Button Text'))
                            ->required(),

                        TextInput::make('last2_button_url')
                            ->label(__('Last 2 Button URL'))
                            ->required(),

                        SpatieMediaLibraryFileUpload::make('last2_image')
                            ->collection('last2_image')
                            ->label(__('Last Second Image'))
                            ->image(),
                    ])->columnSpanFull(),

                Section::make(__('Latest Section'))
                    ->collapsed()
                    ->schema([
                        TextInput::make('latest_heading')
                            ->label(__('Latest Heading'))
                            ->required(),

                        TextInput::make('latest_button_text')
                            ->label(__('Latest Button Text'))
                            ->required(),

                        TextInput::make('latest_button_url')
                            ->label(__('Latest Button URL'))
                            ->required(),
                    ])->columnSpanFull(),
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
