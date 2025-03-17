<?php

namespace App\Filament\Resources;

use App\Models\Product;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Models\LandingPageVarieties;
use Filament\Forms\{Get, Set, Form};
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\{IconColumn, TextColumn};
use Filament\Tables\Actions\{DeleteAction, EditAction, BulkActionGroup, DeleteBulkAction};
use App\Models\{Size, Color, LandingPage, ShippingType, Region, ShippingZone, Governorate};
use App\Filament\Resources\LandingPageResource\Pages\{EditLandingPage, ListLandingPages, CreateLandingPage};
use Filament\Forms\Components\{Grid, Tabs, Select, Toggle, Section, Repeater, Textarea, TextInput, DatePicker, FileUpload, ColorPicker, Tabs\Tab};

class LandingPageResource extends Resource
{
    protected static ?string $model = LandingPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    public static function getNavigationGroup(): ?string
    {
        return __('Products Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('landing_page.landing_pages');
    }

    public static function getModelLabel(): string
    {
        return __('landing_page.landing_page');
    }

    public static function getPluralLabel(): ?string
    {
        return __('landing_page.landing_pages');
    }

    public static function getLabel(): ?string
    {
        return __('landing_page.landing_page');
    }

    public static function getPluralModelLabel(): string
    {
        return __('landing_page.landing_pages');
    }

    protected static ?int $fileMaxSize = 50 * 1024;

    protected static $acceptedFileTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
        'video/mp4',
        'video/webm',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
                    Tabs::make('Tabs')->tabs([
                        self::baseData(),
                        self::Product(),
                        self::Feature(),
                        self::Other()
                    ])->columnSpanFull(),
                ]);
    }

    private static function baseData(): Tab
    {
        return Tab::make(__('landing_page.base_data'))
            ->icon('heroicon-o-document-text')
            ->schema([
                Section::make(__('landing_page.basic_information'))->schema([
                    TextInput::make('slug')->label(__('landing_page.slug'))->required(),
                    TextInput::make('sku')->label(__('landing_page.sku'))->required(),
                    TextInput::make('meta_title')->label(__('landing_page.meta_title'))->required(),
                    Textarea::make('meta_description')->label(__('landing_page.meta_description'))->required(),
                    Textarea::make('meta_keywords')->label(__('landing_page.meta_keywords'))->required(),
                    Toggle::make('status')->label(__('landing_page.status'))->default(true),
                ])->collapsed(),

                Section::make(__('landing_page.topbar_items_section'))->schema([
                    Repeater::make('topBars')
                    ->relationship('topBars')
                    ->schema([
                        TextInput::make('title')->label(__('landing_page.title'))->required(),
                        TextInput::make('link')->label(__('landing_page.link'))->required(),
                    ])
                    ->label(__('landing_page.top_bar_items'))
                    ->createItemButtonLabel(__('landing_page.add_top_bar_item'))
                    ->columns(2),
                ])->collapsed(),

                Section::make(__('landing_page.home_section'))->schema([
                    TextInput::make('home_title')->label(__('landing_page.title')),
                    TextInput::make('home_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_home')->label(__('landing_page.status')),
                    Toggle::make('home_show_cta_button')->label(__('landing_page.show_cta_button')),
                    Toggle::make('is_home_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_home_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('home_section_top_image')
                        ->label(__('landing_page.home_section_top_image'))
                        ->directory('landing-page-home')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('home_section_bottom_image')
                        ->label(__('landing_page.home_section_bottom_image'))
                        ->directory('landing-page-home')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    TextInput::make('home_cta_button_text')->label(__('landing_page.home_cta_button_text')),
                    TextInput::make('home_cta_button_link')->label(__('landing_page.home_cta_button_link')),
                    TextInput::make('home_discount')->label(__('landing_page.home_discount'))->required(),
                    FileUpload::make('home_image')
                        ->label(__('landing_page.home_image'))
                        ->directory('landing-page-home')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                ])->collapsed(),

                Section::make(__('landing_page.about_section'))->schema([
                    TextInput::make('about_title')->label(__('landing_page.title')),
                    TextInput::make('about_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_about_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_about_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('about_section_top_image')
                            ->label(__('landing_page.about_section_top_image'))
                            ->directory('landing-page-about')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                    FileUpload::make('about_section_bottom_image')
                            ->label(__('landing_page.about_section_bottom_image'))
                            ->directory('landing-page-about')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                    Toggle::make('is_about')->label(__('landing_page.status')),
                    Repeater::make('about_content')
                        ->relationship('aboutItems')
                        ->schema([
                            FileUpload::make('image')
                                ->label(__('landing_page.icon'))
                                ->directory('landing-page-about-icon')
                                ->preserveFilenames()
                                // ->acceptedFileTypes(self::$acceptedFileTypes)
                                ->maxSize(self::$fileMaxSize)
                                ->downloadable()
                                ->openable()
                                ->required(),
                            TextInput::make('title')->label(__('landing_page.title'))->required(),
                            TextInput::make('subtitle')->label(__('landing_page.subtitle')),
                            Toggle::make('cta_button')->label(__('landing_page.show_cta_button')),
                            TextInput::make('cta_button_text')->label(__('landing_page.cta_button_text')),
                            TextInput::make('cta_button_link')->label(__('landing_page.cta_button_link')),
                        ])
                        ->label(__('landing_page.content'))
                        ->createItemButtonLabel(__('landing_page.add_content')),
                ])->collapsed(),
            ])
            ->columns(2);
    }

    private static function Product(): Tab
    {
        return Tab::make(__('landing_page.product'))
            ->icon('heroicon-o-list-bullet')
            ->schema([
                Section::make(__('landing_page.criteria_section'))->schema([
                    TextInput::make('title')->label(__('landing_page.product_title'))->required(),
                    Textarea::make('description')->label(__('landing_page.product_description'))->required(),
                    TextInput::make('quantity')->label(__('landing_page.quantity'))->required(),
                    TextInput::make('price')->label(__('landing_page.price'))->required(),
                    TextInput::make('after_discount_price')->label(__('landing_page.after_discount_price'))->required(),
                    TextInput::make('rating')->label(__('landing_page.rating'))->required()->numeric(),
                    Grid::make()
                    ->schema([
                        Section::make(__('landing_page.combinations'))
                        ->schema([
                            Select::make('colors')->label(__('landing_page.colors'))->multiple()->options(Color::pluck('name', 'id'))->live()->afterStateUpdated(function (Get $get, Set $set) {self::generateCombinations($get, $set);}),
                            Select::make('sizes')->label(__('landing_page.sizes'))->multiple()->options(Size::pluck('name', 'id'))->live()->afterStateUpdated(function (Get $get, Set $set) {self::generateCombinations($get, $set);}),

                            Repeater::make('combinations')
                                ->relationship('varieties')
                                ->schema([
                                    TextInput::make('combination_name')->label(__('landing_page.combination_name'))->disabled(),
                                    TextInput::make('price')->label(__('landing_page.price'))->required(),
                                    TextInput::make('quantity')->label(__('landing_page.quantity'))->required(),
                                    Hidden::make('size_id'),
                                    Hidden::make('color_id'),
                                ])
                                ->columns(3)
                                ->afterStateHydrated(function ($state, $set, $record) {
                                    $set('combinations', self::loadExistingCombinations($record));
                                })
                                ->disableItemCreation()
                                ->disableItemDeletion()
                        ])
                    ]),
                ])->collapsed(),

                Section::make(__('landing_page.media_section'))->schema([
                    Repeater::make('product_media')
                    ->relationship('media')
                    ->schema([
                        FileUpload::make('url')
                            ->label(__('landing_page.product_media'))
                            ->directory('landing-page-media')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                    ])
                    ->label(__('landing_page.product_media'))
                    ->createItemButtonLabel(__('landing_page.add_product_media')),
                ])->collapsed(),

                Section::make(__('landing_page.features_section'))->schema([
                    Repeater::make('product_features')
                    ->relationship('features')
                    ->schema([
                        TextInput::make('title')->label(__('landing_page.title'))->required(),
                    ])
                    ->label(__('landing_page.product_features'))
                    ->createItemButtonLabel(__('landing_page.add_product_feature')),
                ])->collapsed(),

                Section::make(__('landing_page.bundles_section'))->schema([
                    Repeater::make('product_bundles')
                    ->relationship('bundles')
                    ->schema([
                        TextInput::make('name')
                        ->label(fn () => __('landing_page.bundle_name'))
                        ->required()
                        ->afterStateHydrated(function (TextInput $component, $state) {
                            $locale = App::getLocale();
                            $component->state($state[$locale] ?? $state['en'] ?? '');
                        }),

                        TextInput::make('name_for_admin')->label(__('landing_page.name_for_admin'))->required(),

                        Select::make('bundle_type')->live()->label(__('landing_page.type'))
                        ->options([
                            'fixed_price' => __('landing_page.fixed_price'),
                            'buy_x_get_y' => __('landing_page.buy_x_get_y')
                        ])->required(),

                        Select::make('products')
                        ->maxItems(fn (Get $get) => ($get('bundle_type') instanceof \App\Enums\BundleType ? $get('bundle_type')->value: $get('bundle_type')) === 'fixed_price' ? 10 : 1)
                        ->searchable()->preload()->label(__('landing_page.products'))->multiple()->relationship('products', 'name'),

                        TextInput::make('buy_x')->live()->label(__('landing_page.buy_x'))->numeric()
                        ->visible(fn ($get) => $get('bundle_type') === 'buy_x_get_y')
                        ->afterStateUpdated(fn (Set $set, Get $get) => self::updateDiscountPrice($set, $get)),

                        TextInput::make('get_y')->live()->label(__('landing_page.get_y_free'))->numeric()
                        ->visible(fn ($get) => $get('bundle_type') === 'buy_x_get_y')
                        ->afterStateUpdated(fn (Set $set, Get $get) => self::updateDiscountPrice($set, $get)),

                        TextInput::make('discount_price')->live()->label(__('landing_page.discount_price'))->numeric()
                        ->visible(fn ($get) => $get('bundle_type'))
                        ->disabled(fn ($get) => $get('bundle_type') === 'buy_x_get_y' && $get('buy_x') !== null && $get('get_y') !== null)
                        ->default(fn (Get $get) => self::calculateDiscountPrice($get))
                        ->afterStateHydrated(fn (Set $set, Get $get) => $set('discount_price', self::calculateDiscountPrice($get)))
                        ->dehydrated(fn ($get) => $get('bundle_type') === 'buy_x_get_y'),
                    ])
                    ->label(__('landing_page.product_bundles'))
                    ->createItemButtonLabel(__('landing_page.add_product_bundle')),
                ])->collapsed(),

                Section::make(__('landing_page.products_section'))->schema([
                    TextInput::make('product_title')->label(__('landing_page.title')),
                    TextInput::make('product_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_products_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_products_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('products_section_top_image')
                        ->label(__('landing_page.products_section_top_image'))
                        ->directory('landing-page-products-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('products_section_bottom_image')
                        ->label(__('landing_page.products_section_bottom_image'))
                        ->directory('landing-page-products-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_products')->label(__('landing_page.status')),
                    Repeater::make('products_content')
                    ->relationship('productsItems')
                    ->schema([
                        TextInput::make('title')->label(__('landing_page.title'))->required(),
                        TextInput::make('subtitle')->label(__('landing_page.subtitle')),
                        FileUpload::make('image')
                            ->label(__('landing_page.icon'))
                            ->directory('landing-page-deal-of-the-week')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        Toggle::make('status')->label(__('landing_page.status')),
                        Toggle::make('cat_bottom')->label(__('landing_page.show_cat_bottom')),
                        TextInput::make('cat_buttom_text')->label(__('landing_page.cta_button_text')),
                        TextInput::make('cat_buttom_link')->label(__('landing_page.cta_button_link')),
                        TextInput::make('price')->label(__('landing_page.price'))->required(),
                        TextInput::make('after_discount_price')->label(__('landing_page.after_discount_price'))->required(),
                    ])
                    ->label(__('landing_page.content'))
                    ->createItemButtonLabel(__('landing_page.add_content')),
                ])->collapsed(),
            ])
            ->columns(2);
    }

    private static function Feature(): Tab
    {
        return Tab::make(__('landing_page.feature'))
            ->icon('heroicon-o-information-circle')
            ->schema([
                Section::make(__('landing_page.features_section'))->schema([
                    TextInput::make('feature_title')->label(__('landing_page.title')),
                    TextInput::make('feature_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_features3_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_features3_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('features3_section_top_image')
                        ->label(__('landing_page.features_1_section_top_image'))
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('features3_section_bottom_image')
                        ->label(__('landing_page.features_1_section_bottom_image'))
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_features')->label(__('landing_page.status')),
                    Toggle::make('is_feature_cta_button')->label(__('landing_page.show_cat_bottom')),
                    TextInput::make('feature_cta_button_text')->label(__('landing_page.cta_button_text')),
                    TextInput::make('feature_cta_button_link')->label(__('landing_page.cta_button_link')),
                    FileUpload::make('feature_image')
                        ->label(__('landing_page.image'))
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Repeater::make('feature_content')
                    ->relationship('featuresItems')
                    ->schema([
                        FileUpload::make('image')
                            ->label(__('landing_page.icon'))
                            ->directory('landing-page-feature-icon')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        TextInput::make('title')->label(__('landing_page.title'))->required(),
                        TextInput::make('subtitle')->label(__('landing_page.subtitle')),
                    ])
                    ->label(__('landing_page.content'))
                    ->createItemButtonLabel(__('landing_page.add_content')),
                ])->collapsed(),

                Section::make(__('landing_page.features_1_section'))->schema([
                    TextInput::make('feature1_title')->label(__('landing_page.title')),
                    TextInput::make('feature1_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_features1_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_features1_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('features1_section_top_image')
                        ->label(__('landing_page.features_1_section_top_image'))
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('features1_section_bottom_image')
                        ->label(__('landing_page.features_1_section_bottom_image'))
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_features1')->label(__('landing_page.status')),
                    Toggle::make('is_feature1_cta_button')->label(__('landing_page.show_cat_bottom')),
                    TextInput::make('feature1_cta_button_text')->label(__('landing_page.cta_button_text')),
                    TextInput::make('feature1_cta_button_link')->label(__('landing_page.cta_button_link')),
                    FileUpload::make('feature1_image')
                        ->label(__('landing_page.image'))
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Repeater::make('feature_content')
                    ->relationship('featuresItems')
                    ->schema([
                        FileUpload::make('image')
                            ->label(__('landing_page.icon'))
                            ->directory('landing-page-feature-icon')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        TextInput::make('title')->label(__('landing_page.title'))->required(),
                        TextInput::make('subtitle')->label(__('landing_page.subtitle')),
                    ])
                    ->label(__('landing_page.content'))
                    ->createItemButtonLabel(__('landing_page.add_content')),
                ])->collapsed(),

                Section::make(__('landing_page.features_2_section'))->schema([
                    TextInput::make('feature2_title')->label(__('landing_page.title')),
                    TextInput::make('feature2_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_features2_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_features2_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('features2_section_top_image')
                        ->label(__('landing_page.features_2_section_top_image'))
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('features2_section_bottom_image')
                        ->label(__('landing_page.features_2_section_bottom_image'))
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_features2')->label(__('landing_page.status')),
                    Toggle::make('is_feature2_cta_button')->label(__('landing_page.show_cat_bottom')),
                    TextInput::make('feature2_cta_button_text')->label(__('landing_page.cta_button_text')),
                    TextInput::make('feature2_cta_button_link')->label(__('landing_page.cta_button_link')),
                    FileUpload::make('feature2_image')
                        ->label(__('landing_page.image'))
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Repeater::make('feature_content')
                    ->relationship('featuresItems')
                    ->schema([
                        FileUpload::make('image')
                            ->label(__('landing_page.icon'))
                            ->directory('landing-page-feature-icon')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        TextInput::make('title')->label(__('landing_page.title'))->required(),
                        TextInput::make('subtitle')->label(__('landing_page.subtitle')),
                    ])
                    ->label(__('landing_page.content'))
                    ->createItemButtonLabel(__('landing_page.add_content')),
                ])->collapsed(),
            ])
            ->columns(2);
    }

    private static function Other(): Tab
    {
        return Tab::make(__('landing_page.other_sections'))
            ->icon('heroicon-o-information-circle')
            ->schema([
                Section::make(__('landing_page.deal_of_the_week_section'))->schema([
                    TextInput::make('deal_of_the_week_title')->label(__('landing_page.title')),
                    TextInput::make('deal_of_the_week_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_deal_of_the_week_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_deal_of_the_week_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('deal_of_the_week_section_top_image')
                        ->label(__('landing_page.top_image'))
                        ->directory('landing-page-deal-of-the-week')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('deal_of_the_week_section_bottom_image')
                        ->label(__('landing_page.bottom_image'))
                        ->directory('landing-page-deal-of-the-week')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_deal_of_the_week')->label(__('landing_page.status')),
                    Repeater::make('deals')
                        ->relationship('dealOfTheWeekItems')
                        ->schema([
                            FileUpload::make('image')
                                ->label(__('landing_page.icon'))
                                ->directory('landing-page-deal-of-the-week')
                                ->preserveFilenames()
                                ->maxSize(self::$fileMaxSize)
                                ->downloadable()
                                ->openable()
                                ->required(),
                            TextInput::make('rate')->label(__('landing_page.rate'))->live()->minValue(0)->required()->rule('gte:0')->extraAttributes(['oninput' => "this.value = Math.max(0, this.value)"]),
                            TextInput::make('title')->label(__('landing_page.title'))->required(),
                            TextInput::make('subtitle')->label(__('landing_page.subtitle'))->required(),
                            TextInput::make('price')->label(__('landing_page.price'))->required(),
                            TextInput::make('after_discount_price')->label(__('landing_page.after_discount_price'))->required(),
                            DatePicker::make('date_of_birth')->label(__('landing_page.end_date'))->native(false),
                            TextInput::make('cta_button_text')->label(__('landing_page.cta_button_text')),
                            TextInput::make('cta_button_link')->label(__('landing_page.cta_button_link')),
                        ])
                        ->label(__('landing_page.deals'))
                        ->createItemButtonLabel(__('landing_page.add_deals')),
                ])->collapsed(),

                Section::make(__('landing_page.why_choose_us_section'))->schema([
                    TextInput::make('why_choose_us_title')->label(__('landing_page.title')),
                    TextInput::make('why_choose_us_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_why_choose_us_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_why_choose_us_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('why_choose_us_section_top_image')
                        ->label(__('landing_page.top_image'))
                        ->directory('landing-page-why-choose-us-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('why_choose_us_section_bottom_image')
                        ->label(__('landing_page.bottom_image'))
                        ->directory('landing-page-why-choose-us-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_why_choose_us')->label(__('landing_page.status')),
                    FileUpload::make('why_choose_us_video')
                        ->label(__('landing_page.video'))
                        ->directory('landing-page-why-choose-us-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Repeater::make('why_choose_us_content')
                        ->relationship('whyChooseUsItems')
                        ->schema([
                            FileUpload::make('image')
                                ->label(__('landing_page.icon'))
                                ->directory('landing-page-why-choose-us-section')
                                ->preserveFilenames()
                                ->maxSize(self::$fileMaxSize)
                                ->downloadable()
                                ->openable()
                                ->required(),
                            TextInput::make('title')->label(__('landing_page.title'))->required(),
                            ColorPicker::make('background_color')->label(__('landing_page.background_color'))->hex(),
                            ColorPicker::make('text_color')->label(__('landing_page.text_color'))->hex(),
                        ])
                        ->label(__('landing_page.content'))
                        ->createItemButtonLabel(__('landing_page.add_content')),
                ])->collapsed(),

                Section::make(__('landing_page.compare_section'))->schema([
                    TextInput::make('compare_title')->label(__('landing_page.title')),
                    TextInput::make('compare_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_compares_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_compares_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('compares_section_top_image')
                        ->label(__('landing_page.top_image'))
                        ->directory('landing-page-compare-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable(),
                    FileUpload::make('compares_section_bottom_image')
                        ->label(__('landing_page.bottom_image'))
                        ->directory('landing-page-compare-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable(),
                    Toggle::make('is_compare')->label(__('landing_page.status')),
                    Repeater::make('compare_content')
                        ->relationship('comparesItems')
                        ->schema([
                            FileUpload::make('image')
                                ->label(__('landing_page.icon'))
                                ->directory('landing-page-compare-section')
                                ->preserveFilenames()
                                ->maxSize(self::$fileMaxSize)
                                ->downloadable()
                                ->openable()
                                ->required(),
                            TextInput::make('title')->label(__('landing_page.title'))->required(),
                            TextInput::make('subtitle')->label(__('landing_page.subtitle'))->required(),
                            TextInput::make('price')->label(__('landing_page.price'))->required(),
                            TextInput::make('brand')->label(__('landing_page.brand'))->required(),
                            TextInput::make('color')->label(__('landing_page.color'))->required(),
                            TextInput::make('dimensions')->label(__('landing_page.dimensions'))->required(),
                            TextInput::make('weight')->label(__('landing_page.weight'))->required(),
                            TextInput::make('attributes')->label(__('landing_page.attributes'))->required(),
                            Toggle::make('cta_button')->label(__('landing_page.cta_button')),
                            TextInput::make('cta_button_text')->label(__('landing_page.cta_button_text'))->required(),
                            TextInput::make('cta_button_link')->label(__('landing_page.cta_button_link'))->required(),
                        ])
                        ->label(__('landing_page.content'))
                        ->createItemButtonLabel(__('landing_page.add_content')),
                ])->collapsed(),

                Section::make(__('landing_page.feedbacks_section'))->schema([
                    TextInput::make('feedback_title')->label(__('landing_page.title')),
                    TextInput::make('feedback_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_feedbacks_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_feedbacks_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('feedbacks_section_top_image')
                        ->label(__('landing_page.top_image'))
                        ->directory('landing-page-feedbacks-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('feedbacks_section_bottom_image')
                        ->label(__('landing_page.bottom_image'))
                        ->directory('landing-page-feedbacks-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_feedbacks')->label(__('landing_page.status')),
                    Repeater::make('feedbacks_content')
                        ->relationship('feedbacksItems')
                        ->schema([
                            FileUpload::make('image')
                                ->label(__('landing_page.icon'))
                                ->directory('landing-page-feedbacks-section')
                                ->preserveFilenames()
                                ->maxSize(self::$fileMaxSize)
                                ->downloadable()
                                ->openable()
                                ->required(),
                            TextInput::make('comment')->label(__('landing_page.comment'))->required(),
                            TextInput::make('user_name')->label(__('landing_page.user_name'))->required(),
                            TextInput::make('user_position')->label(__('landing_page.user_position'))->required(),
                        ])
                        ->label(__('landing_page.content'))
                        ->createItemButtonLabel(__('landing_page.add_content')),
                ])->collapsed(),

                Section::make(__('landing_page.faq_section'))->schema([
                    TextInput::make('faq_title')->label(__('landing_page.title')),
                    TextInput::make('faq_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_faq_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_faq_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('faq_section_top_image')
                        ->label(__('landing_page.top_image'))
                        ->directory('landing-page-faq-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('faq_section_bottom_image')
                        ->label(__('landing_page.bottom_image'))
                        ->directory('landing-page-faq-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_faq')->label(__('landing_page.status')),
                    FileUpload::make('faq_image')
                        ->label(__('landing_page.faq_image'))
                        ->directory('landing-page-faq-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Repeater::make('content')
                        ->relationship('faqsItems')
                        ->schema([
                            TextInput::make('question')->label(__('landing_page.question'))->required(),
                            TextInput::make('answer')->label(__('landing_page.answer'))->required(),
                        ])
                        ->label(__('landing_page.content'))
                        ->createItemButtonLabel(__('landing_page.add_content')),
                ])->collapsed(),

                Section::make(__('landing_page.footer_section'))->schema([
                    TextInput::make('footer_title')->label(__('landing_page.title')),
                    TextInput::make('footer_subtitle')->label(__('landing_page.subtitle')),
                    Toggle::make('is_footer_section_top_image')->label(__('landing_page.show_top_image')),
                    Toggle::make('is_footer_section_bottom_image')->label(__('landing_page.show_bottom_image')),
                    FileUpload::make('footer_section_top_image')
                        ->label(__('landing_page.top_image'))
                        ->directory('landing-page-footer-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('footer_section_bottom_image')
                        ->label(__('landing_page.bottom_image'))
                        ->directory('landing-page-footer-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_footer')->label(__('landing_page.status')),
                    FileUpload::make('footer_image')
                        ->label(__('landing_page.footer_image'))
                        ->directory('landing-page-faq-section')
                        ->preserveFilenames()
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                ])->collapsed(),

                Section::make(__('landing_page.counter_section'))->schema([
                    TextInput::make('counter_section_cta_button_text')->label(__('landing_page.cta_button_text')),
                    TextInput::make('counter_section_cta_button_link')->label(__('landing_page.cta_button_link')),
                    DatePicker::make('counter_section_end_date')->label(__('landing_page.end_date'))->native(false),
                    Toggle::make('is_counter_section')->label(__('landing_page.status')),
                ])->collapsed(),
            ])
            ->columns(2);
    }

    protected static function calculateDiscountPrice(?Get $get)
    {
        if ($get && $get('bundle_type') === 'buy_x_get_y' && $get('buy_x') && $get('get_y')) {
            $productIds = $get('products') ?? [];
            $productId = is_array($productIds) ? reset($productIds) : $productIds;

            if ($productId) {
                $product = Product::find($productId);

                if ($product) {
                    $buyX = floatval($get('buy_x')) ?: 1;
                    $pricePerUnit = floatval($product->discount_price_for_current_country);
                    return $buyX * $pricePerUnit;
                }
            }
        }

        return null;
    }

    protected static function updateDiscountPrice(Set $set, Get $get)
    {
        if ($get('bundle_type') === 'buy_x_get_y' && $get('buy_x') && $get('get_y')) {
            $discountPrice = self::calculateDiscountPrice($get);
            $set('discount_price', $discountPrice);
        }
    }

    private static function loadExistingCombinations($record): array
    {
        $existingCombinations = LandingPageVarieties::where('landing_page_id', $record?->id)->get();

        return $existingCombinations->map(fn ($variety) => [
            'combination_name' => "{$variety->color->name}_{$variety->size->name}",
            'price'            => $variety->price,
            'quantity'         => $variety->quantity,
            'color_id'         => $variety->color_id,
            'size_id'          => $variety->size_id,
        ])->toArray();
    }

    private static function generateCombinations(Get $get, Set $set): void
    {
        $selectedColors = $get('colors') ?? [];
        $selectedSizes = $get('sizes') ?? [];

        $colors = Color::whereIn('id', $selectedColors)->get();
        $sizes = Size::whereIn('id', $selectedSizes)->get();

        $combinations = [];

        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                $combinations[] = [
                    'combination_name'  => "{$color->name}_{$size->name}",
                    'price'             => 0,
                    'quantity'          => 0,
                    'color_id'          => $color->id,
                    'size_id'           => $size->id,
                ];
            }
        }

        $set('combinations', $combinations);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')->label(__('landing_page.slug'))->searchable(),
                TextColumn::make('sku')->label(__('landing_page.sku'))->searchable(),
                IconColumn::make('is_home')->label(__('landing_page.is_home'))->boolean(),
                IconColumn::make('is_about')->label(__('landing_page.is_about'))->boolean(),
                IconColumn::make('is_features')->label(__('landing_page.is_features'))->boolean(),
                IconColumn::make('is_products')->label(__('landing_page.is_products'))->boolean(),
                IconColumn::make('is_compare')->label(__('landing_page.is_compare'))->boolean(),
                IconColumn::make('is_feedbacks')->label(__('landing_page.is_feedbacks'))->boolean(),
                IconColumn::make('is_faq')->label(__('landing_page.is_faq'))->boolean(),
                IconColumn::make('is_footer')->label(__('landing_page.is_footer'))->boolean(),
                IconColumn::make('status')->label(__('landing_page.status'))->boolean(),
                TextColumn::make('created_at')->label(__('landing_page.created_at'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('landing_page.updated_at'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                ActionGroup::make([
                    Action::make(__('landing_page.view'))
                        ->url(fn ($record) => route('landing-page.show-by-slug', ['slug' => $record->slug]))
                        ->color('success')
                        ->icon('heroicon-o-eye')
                        ->openUrlInNewTab(),

                    Action::make(__('landing_page.shipping_types'))
                        ->form([self::SyncShippingTypes()])
                        ->action(fn (array $data) => Log::info($data))
                        ->icon('heroicon-o-tag')
                        ->color('primary'),

                    Action::make(__('landing_page.shipping_zones'))
                        ->form([self::SyncShippingZones()])
                        ->action(fn (array $data) => Log::info($data))
                        ->icon('heroicon-o-map')
                        ->color('warning'),

                    Action::make(__('landing_page.shipping_governorates'))
                        ->form([self::SyncShippingGovernorates()])
                        ->action(fn (array $data) => Log::info($data))
                        ->icon('heroicon-o-building-office')
                        ->color('danger'),

                    Action::make(__('landing_page.shipping_regions'))
                        ->form([self::SyncShippingRegions()])
                        ->action(fn (array $data) => Log::info($data))
                        ->icon('heroicon-o-map-pin')
                        ->color('info'),
                ])
                ->dropdown(true)
                ->label(__('landing_page.shipping_options'))
                ->icon('heroicon-o-bars-3'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function SyncShippingTypes()
    {
        return Repeater::make('shipping_types')
                ->relationship('LandingPageShippingTypes')
                ->label(__('landing_page.shipping_types'))
                ->schema([
                    TextInput::make('name')->label(__('landing_page.name'))->disabled(),
                    TextInput::make('shipping_cost')->label(__('landing_page.shipping_cost'))->numeric(false),
                    Toggle::make('status')->label(__('landing_page.status')),
                    Hidden::make('landing_page_id'),
                    Hidden::make('shipping_type_id'),
                ])
                ->afterStateHydrated(function ($state, $set, $record) {
                    $allShippingTypes = ShippingType::all();
                    $existingShippingTypes = $record->LandingPageShippingTypes->keyBy('shipping_type_id');

                    $set('shipping_types', $allShippingTypes->map(fn ($type) => [
                        'name'              => $type->name,
                        'shipping_cost'     => $existingShippingTypes[$type->id]->shipping_cost ?? $type->shipping_cost,
                        'status'            => $existingShippingTypes[$type->id]->status ?? $type->status,
                        'landing_page_id'   => $record->id,
                        'shipping_type_id'  => $type->id,
                    ])->toArray());
                })
                ->columns(3)
                ->defaultItems(0)
                ->disableItemCreation()
                ->disableItemDeletion();
    }

    private static function SyncShippingZones()
    {
        $governorates = Governorate::pluck('name', 'id')->toArray();

        return Repeater::make('shipping_zones')
                ->relationship('LandingPageShippingZones')
                ->label(__('landing_page.shipping_zones'))
                ->schema([
                    TextInput::make('name')->label(__('landing_page.name'))->disabled(),
                    Select::make('governorates')->label(__('landing_page.governorates'))->multiple()->options($governorates),
                    TextInput::make('shipping_cost')->label(__('landing_page.shipping_cost'))->numeric(false),
                    Toggle::make('status')->label(__('landing_page.status')),
                    Hidden::make('shipping_zone_id'),
                    Hidden::make('landing_page_id'),
                    Hidden::make('shipping_type_id'),
                ])
                ->afterStateHydrated(function ($state, $set, $record) {
                    $allShippingZones = ShippingZone::with('governorates', 'shippingTypes')->get();
                    $existingShippingZones = $record->LandingPageShippingZones->keyBy('shipping_zone_id');

                    $set('shipping_zones', $allShippingZones->map(fn ($zone) => [
                        'name'              => $zone->name,
                        'status'            => $existingShippingZones[$zone->id]->status ?? false,
                        'shipping_cost'     => $existingShippingZones[$zone->id]->shipping_cost ?? $zone->cost,
                        'shipping_zone_id'  => $zone->id,
                        'landing_page_id'   => $record->id,
                        'governorates'      => $zone->governorates->pluck('id')->toArray(),
                        'shipping_type_id'  => $existingShippingZones[$zone->id]->shipping_type_id ?? $zone->shippingTypes->pluck('id')->first(),
                    ])->toArray());
                })
                ->columns(4)
                ->defaultItems(0)
                ->disableItemCreation()
                ->disableItemDeletion();
    }

    private static function SyncShippingGovernorates()
    {
        return Repeater::make('shipping_governorates')
                ->relationship('LandingPageGovernorates')
                ->label(__('landing_page.shipping_governorates'))
                ->schema([
                    TextInput::make('name')->label(__('landing_page.name'))->disabled(),
                    TextInput::make('shipping_cost')->label(__('landing_page.shipping_cost'))->numeric(false),
                    Toggle::make('status')->label(__('landing_page.status')),
                    Hidden::make('governorate_id'),
                    Hidden::make('landing_page_id'),
                    Hidden::make('shipping_type_id'),
                ])
                ->afterStateHydrated(function ($state, $set, $record) {
                    $allGovernorates = Governorate::with('shippingTypes')->get();
                    $existingGovernorates = $record->LandingPageGovernorates->keyBy('governorate_id');

                    $set('shipping_governorates', $allGovernorates->map(fn ($governorate) => [
                        'name'              => $governorate->name,
                        'shipping_cost'     => $existingGovernorates[$governorate->id]->shipping_cost ?? $governorate->cost,
                        'status'            => $existingGovernorates[$governorate->id]->status ?? false,
                        'governorate_id'    => $governorate->id,
                        'shipping_type_id'  => $existingGovernorates[$governorate->id]->shipping_type_id ?? $governorate->shippingTypes->pluck('id')->first() ?? 1,
                        'landing_page_id'   => $record?->id,
                    ])->toArray());
                })
                ->columns(3)
                ->disableItemCreation()
                ->disableItemDeletion();
    }

    private static function SyncShippingRegions()
    {
        return Repeater::make('shipping_regions')
                ->relationship('LandingPageRegions')
                ->label(__('landing_page.shipping_regions'))
                ->schema([
                    TextInput::make('name')->label(__('landing_page.name'))->disabled(),
                    TextInput::make('governorate')->label(__('landing_page.governorate'))->disabled(),
                    TextInput::make('shipping_cost')->label(__('landing_page.shipping_cost'))->numeric(false),
                    Toggle::make('status')->label(__('landing_page.status')),
                    Hidden::make('region_id'),
                    Hidden::make('landing_page_id'),
                    Hidden::make('shipping_type_id'),
                ])
                ->lazy()
                ->afterStateHydrated(function ($state, $set, $record) {
                    $allRegions =  Region::with('shippingTypes')->get();
                    $existingRegions = $record->LandingPageRegions->keyBy('region_id');

                    $set('shipping_regions', $allRegions->map(fn ($region) => [
                        'name'              => $region->name,
                        'governorate'       => optional($region->governorate)->name,
                        'shipping_cost'     => optional($existingRegions[$region->id] ?? null)->shipping_cost ?? 0,
                        'status'            => optional($existingRegions[$region->id] ?? null)->status ?? false,
                        'region_id'         => $region->id,
                        'landing_page_id'   => $record->id,
                        'shipping_type_id'  => optional($existingRegions[$region->id] ?? null)->shipping_type_id ?? $region->shippingTypes->pluck('id')->first() ?? 1,
                    ])->toArray());
                })
                ->columns(4)
                ->disableItemCreation()
                ->disableItemDeletion();
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLandingPages::route('/'),
            'create' => CreateLandingPage::route('/create'),
            'edit'   => EditLandingPage::route('/{record}/edit'),
        ];
    }
}
