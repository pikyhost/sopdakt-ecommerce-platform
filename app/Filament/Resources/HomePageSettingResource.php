<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomePageSettingResource\Pages;
use App\Models\HomePageSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomePageSettingResource extends Resource
{
    use Translatable;

    protected static ?string $model = HomePageSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?int $navigationSort = 3;

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
                Section::make(__('Slider Content'))
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('main_heading')
                    ->label(__('Main Heading')),

                TextColumn::make('discount_text')
                    ->label(__('Discount Text')),

                TextColumn::make('discount_value')
                    ->label(__('Discount Value')),

                TextColumn::make('starting_price')
                    ->label(__('Starting Price')),

                TextColumn::make('currency_symbol')
                    ->label(__('Currency Symbol')),

                TextColumn::make('button_text')
                    ->label(__('Button Text')),

                TextColumn::make('button_url')
                    ->label(__('Button URL'))
                    ->url(fn ($record) => $record->button_url, true)
                    ->openUrlInNewTab(),

                ImageColumn::make('slider1_image')
                    ->label(__('Slider 1 Background Image')),

                ImageColumn::make('slider2_image')
                    ->label(__('Slider 2 Background Image')),

                TextColumn::make('center_main_heading')
                    ->label(__('Center Main Heading')),

                TextColumn::make('center_button_text')
                    ->label(__('Center Button Text')),

                TextColumn::make('center_button_url')
                    ->label(__('Center Button URL'))
                    ->url(fn ($record) => $record->center_button_url, true)
                    ->openUrlInNewTab(),

                ImageColumn::make('center_image')
                    ->label(__('Center Image')),

                TextColumn::make('last1_heading')
                    ->label(__('Last 1 Heading')),

                TextColumn::make('last1_subheading')
                    ->label(__('Last 1 Subheading')),

                TextColumn::make('last1_button_text')
                    ->label(__('Last 1 Button Text')),

                TextColumn::make('last1_button_url')
                    ->label(__('Last 1 Button URL'))
                    ->url(fn ($record) => $record->last1_button_url, true)
                    ->openUrlInNewTab(),

                ImageColumn::make('last1_image')
                    ->label(__('Last First Image')),

                TextColumn::make('last2_heading')
                    ->label(__('Last 2 Heading')),

                TextColumn::make('last2_subheading')
                    ->label(__('Last 2 Subheading')),

                TextColumn::make('last2_button_text')
                    ->label(__('Last 2 Button Text')),

                TextColumn::make('last2_button_url')
                    ->label(__('Last 2 Button URL'))
                    ->url(fn ($record) => $record->last2_button_url, true)
                    ->openUrlInNewTab(),

                ImageColumn::make('last2_image')
                    ->label(__('Last Second Image')),

                TextColumn::make('latest_heading')
                    ->label(__('Latest Heading')),

                TextColumn::make('latest_button_text')
                    ->label(__('Latest Button Text')),

                TextColumn::make('latest_button_url')
                    ->label(__('Latest Button URL'))
                    ->url(fn ($record) => $record->latest_button_url, true)
                    ->openUrlInNewTab(),
            ])
            ->actions([
                EditAction::make(),
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
