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
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Landing Page';
    protected static ?int $fileMaxSize = 50 * 1024;

    public static function getNavigationGroup(): ?string
    {
        return __('Products Management');
    }

    // protected static $acceptedFileTypes = [
    //     'image/jpeg',
    //     'image/jpg',
    //     'image/png',
    //     'image/gif',
    //     'image/webp',
    //     'video/mp4',
    //     'video/webm',
    //     'application/pdf',
    //     'application/msword',
    //     'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    //     'application/vnd.ms-excel',
    //     'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    // ];

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
        return Tab::make('Base Data')
            ->icon('heroicon-o-document-text')
            ->schema([
                Section::make('Basic Information')->schema([
                    TextInput::make('slug')->label('Slug')->required(),
                    TextInput::make('sku')->label('SKU')->required(),
                    TextInput::make('meta_title')->label('Meta Title')->required(),
                    Textarea::make('meta_description')->label('Meta Description')->required(),
                    Textarea::make('meta_keywords')->label('Meta Keywords')->required(),
                    Toggle::make('status')->label('Status')->default(true),
                ])->collapsed(),

                Section::make('TopBar Items Section')->schema([
                    Repeater::make('topBars')
                    ->relationship('topBars')
                    ->schema([
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('link')->label('Link')->required(),
                    ])
                    ->label('Top Bar Items')
                    ->createItemButtonLabel('Add Top Bar Item')
                    ->columns(2),
                ])->collapsed(),

                Section::make('Home Section')->schema([
                    TextInput::make('home_title')->label('Title'),
                    TextInput::make('home_subtitle')->label('Subtitle'),
                    Toggle::make('is_home')->label('Status'),
                    Toggle::make('home_show_cta_button')->label('Show Cta Button'),
                    Toggle::make('is_home_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_home_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('home_section_top_image')
                        ->label('Home Section Top Image')
                        ->directory('landing-page-home')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('home_section_bottom_image')
                        ->label('Home Section Bottom Image')
                        ->directory('landing-page-home')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    TextInput::make('home_cta_button_text')->label('Home Cta Button Text'),
                    TextInput::make('home_cta_button_link')->label('Home Cta Button Link'),
                    TextInput::make('home_discount')->label('Home Discount')->required(),
                    FileUpload::make('home_image')
                        ->label('Home Image')
                        ->directory('landing-page-home')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                ])->collapsed(),

                Section::make('About Section')->schema([
                    TextInput::make('about_title')->label('Title'),
                    TextInput::make('about_subtitle')->label('Subtitle'),
                    Toggle::make('is_about_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_about_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('about_section_top_image')
                            ->label('About Section Top Image')
                            ->directory('landing-page-about')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                    FileUpload::make('about_section_bottom_image')
                            ->label('About Section Bottom Image')
                            ->directory('landing-page-about')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                    Toggle::make('is_about')->label('Status'),
                    Repeater::make('about_content')
                        ->relationship('aboutItems')
                        ->schema([
                            FileUpload::make('image')
                                ->label('Icon')
                                ->directory('landing-page-about-icon')
                                ->preserveFilenames()
                                // ->acceptedFileTypes(self::$acceptedFileTypes)
                                ->maxSize(self::$fileMaxSize)
                                ->downloadable()
                                ->openable()
                                ->required(),
                            TextInput::make('title')->label('Title')->required(),
                            TextInput::make('subtitle')->label('Subtitle'),
                            Toggle::make('cta_button')->label('Show Cta Button'),
                            TextInput::make('cta_button_text')->label('Cta Button Text'),
                            TextInput::make('cta_button_link')->label('Cta Button Link'),
                        ])
                        ->label('Content')
                        ->createItemButtonLabel('Add Content'),
                ])->collapsed(),
            ])
            ->columns(2);
    }

    private static function Product(): Tab
    {
        return Tab::make('Product')
            ->icon('heroicon-o-list-bullet')
            ->schema([
                Section::make('Criteria Section')->schema([
                    TextInput::make('title')->label('Product Title')->required(),
                    Textarea::make('description')->label('Product Description')->required(),
                    TextInput::make('quantity')->label('Quantity')->required(),
                    TextInput::make('price')->label('Price')->required(),
                    TextInput::make('after_discount_price')->label('After Discount Price')->required(),
                    TextInput::make('rating')->label('Rating')->required(),
                    Grid::make()
                    ->schema([
                        Section::make('Combinations')
                        ->schema([
                            Select::make('colors')->label('Colors')->multiple()->options(Color::pluck('name', 'id'))->live()->afterStateUpdated(function (Get $get, Set $set) {self::generateCombinations($get, $set);}),
                            Select::make('sizes')->label('Sizes')->multiple()->options(Size::pluck('name', 'id'))->live()->afterStateUpdated(function (Get $get, Set $set) {self::generateCombinations($get, $set);}),

                            Repeater::make('combinations')
                                ->relationship('varieties')
                                ->schema([
                                    TextInput::make('combination_name')->label('Combination Name')->disabled(),
                                    TextInput::make('price')->label('Price')->required(),
                                    TextInput::make('quantity')->label('Quantity')->required(),
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

                Section::make('Media Section')->schema([
                    Repeater::make('product_media')
                    ->relationship('media')
                    ->schema([
                        FileUpload::make('url')
                            ->label('Product Media')
                            ->directory('landing-page-media')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                    ])
                    ->label('Product Media')
                    ->createItemButtonLabel('Add Product Media'),
                ])->collapsed(),

                Section::make('Features Section')->schema([
                    Repeater::make('product_features')
                    ->relationship('features')
                    ->schema([
                        TextInput::make('title')->label('Title')->required(),
                    ])
                    ->label('Product Features')
                    ->createItemButtonLabel('Add Product Feature'),
                ])->collapsed(),

                Section::make('Bundles Section')->schema([
                    Repeater::make('product_bundles')
                    ->relationship('bundles')
                    ->schema([
                        TextInput::make('name')
                        ->label(fn () => __('Bundle Name'))
                        ->required()
                        ->afterStateHydrated(function (TextInput $component, $state) {
                            $locale = App::getLocale();
                            $component->state($state[$locale] ?? $state['en'] ?? '');
                        }),

                        TextInput::make('name_for_admin')->label('Name For Admin')->required(),

                        Select::make('bundle_type')->live()->label('Type')
                        ->options([
                            'fixed_price' => 'Fixed Price',
                            'buy_x_get_y' => 'Buy X Get Y'
                        ])->required(),

                        Select::make('products')
                        ->maxItems(fn (Get $get) => ($get('bundle_type') instanceof \App\Enums\BundleType ? $get('bundle_type')->value: $get('bundle_type')) === 'fixed_price' ? 10 : 1)
                        ->searchable()->preload()->label(__('bundles.products'))->multiple()->relationship('products', 'name'),

                        TextInput::make('buy_x')->live()->label('Buy X')->numeric()
                        ->visible(fn ($get) => $get('bundle_type') === 'buy_x_get_y')
                        ->afterStateUpdated(fn (Set $set, Get $get) => self::updateDiscountPrice($set, $get)),

                        TextInput::make('get_y')->live()->label('Get Y Free')->numeric()
                        ->visible(fn ($get) => $get('bundle_type') === 'buy_x_get_y')
                        ->afterStateUpdated(fn (Set $set, Get $get) => self::updateDiscountPrice($set, $get)),

                        TextInput::make('discount_price')->live()->label('Discount Price')->numeric()
                        ->visible(fn ($get) => $get('bundle_type'))
                        ->disabled(fn ($get) => $get('bundle_type') === 'buy_x_get_y' && $get('buy_x') !== null && $get('get_y') !== null)
                        ->default(fn (Get $get) => self::calculateDiscountPrice($get))
                        ->afterStateHydrated(fn (Set $set, Get $get) => $set('discount_price', self::calculateDiscountPrice($get)))
                        ->dehydrated(fn ($get) => $get('bundle_type') === 'buy_x_get_y'),
                    ])
                    ->label('Product Bundles')
                    ->createItemButtonLabel('Add Product Bundle'),
                ])->collapsed(),

                Section::make('Products Section')->schema([
                    TextInput::make('product_title')->label('Title'),
                    TextInput::make('product_subtitle')->label('Subtitle'),
                    Toggle::make('is_products_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_products_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('products_section_top_image')
                        ->label('Products Section Top Image')
                        ->directory('landing-page-products-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('products_section_bottom_image')
                        ->label('Products Section Bottom Image')
                        ->directory('landing-page-products-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_products')->label('Status'),
                    Repeater::make('products_content')
                    ->relationship('productsItems')
                    ->schema([
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle'),
                        FileUpload::make('image')
                            ->label('Icon')
                            ->directory('landing-page-deal-of-the-week')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        Toggle::make('status')->label('Status'),
                        Toggle::make('cat_bottom')->label('Show Cat Bottom'),
                        TextInput::make('cat_buttom_text')->label('Cta Button Text'),
                        TextInput::make('cat_buttom_link')->label('Cta Button Link'),
                        TextInput::make('price')->label('Price')->required(),
                        TextInput::make('after_discount_price')->label('After Discount Price')->required(),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
                ])->collapsed(),
            ])
            ->columns(2);
    }

    private static function Feature(): Tab
    {
        return Tab::make('Feature')
            ->icon('heroicon-o-information-circle')
            ->schema([
                Section::make('Features Section')->schema([
                    TextInput::make('feature_title')->label('Title'),
                    TextInput::make('feature_subtitle')->label('Subtitle'),
                    Toggle::make('is_features3_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_features3_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('features3_section_top_image')
                        ->label("Features 1 Section Top Image")
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('features3_section_bottom_image')
                        ->label("Features 1 Section Bottom Image")
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_features')->label('Status'),
                    Toggle::make('is_feature_cta_button')->label('Show Cat Bottom'),
                    TextInput::make('feature_cta_button_text')->label('Cta Button Text'),
                    TextInput::make('feature_cta_button_link')->label('Cta Button Link'),
                    FileUpload::make('feature_image')
                        ->label('Image')
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
                            ->label('Icon')
                            ->directory('landing-page-feature-icon')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle'),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
                ])->collapsed(),

                Section::make('Features 1 Section')->schema([
                    TextInput::make('feature1_title')->label('Title'),
                    TextInput::make('feature1_subtitle')->label('Subtitle'),
                    Toggle::make('is_features1_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_features1_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('features1_section_top_image')
                        ->label("Features 1 Section Top Image")
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('features1_section_bottom_image')
                        ->label("Features 1 Section Bottom Image")
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_features1')->label('Status'),
                    Toggle::make('is_feature1_cta_button')->label('Show Cat Bottom'),
                    TextInput::make('feature1_cta_button_text')->label('Cta Button Text'),
                    TextInput::make('feature1_cta_button_link')->label('Cta Button Link'),
                    FileUpload::make('feature1_image')
                        ->label('Image')
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
                            ->label('Icon')
                            ->directory('landing-page-feature-icon')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle'),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
                ])->collapsed(),

                Section::make('Features 2 Section')->schema([
                    TextInput::make('feature2_title')->label('Title'),
                    TextInput::make('feature2_subtitle')->label('Subtitle'),
                    Toggle::make('is_features2_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_features2_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('features2_section_top_image')
                        ->label("Features 2 Section Top Image")
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('features2_section_bottom_image')
                        ->label("Features 2 Section Bottom Image")
                        ->directory('landing-page-feature')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_features2')->label('Status'),
                    Toggle::make('is_feature2_cta_button')->label('Show Cat Bottom'),
                    TextInput::make('feature2_cta_button_text')->label('Cta Button Text'),
                    TextInput::make('feature2_cta_button_link')->label('Cta Button Link'),
                    FileUpload::make('feature2_image')
                        ->label('Image')
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
                            ->label('Icon')
                            ->directory('landing-page-feature-icon')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle'),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
                ])->collapsed(),
            ])
            ->columns(2);
    }

    private static function Other(): Tab
    {
        return Tab::make('Other Sections')
            ->icon('heroicon-o-information-circle')
            ->schema([
                Section::make('Deal Of The Week Section')->schema([
                    TextInput::make('deal_of_the_week_title')->label('Title'),
                    TextInput::make('deal_of_the_week_subtitle')->label('Subtitle'),
                    Toggle::make('is_deal_of_the_week_section_top_image')->label('Deal Of The Week Section Show Top Image'),
                    Toggle::make('is_deal_of_the_week_section_bottom_image')->label('Deal Of The Week Section Show Bottom Image'),
                    FileUpload::make('deal_of_the_week_section_top_image')
                        ->label('Deal Of The Week Section Top Image')
                        ->directory('landing-page-deal-of-the-week')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('deal_of_the_week_section_bottom_image')
                        ->label('Deal Of The Week Section Bottom Image')
                        ->directory('landing-page-deal-of-the-week')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_deal_of_the_week')->label('Status'),
                    Repeater::make('deals')
                    ->relationship('dealOfTheWeekItems')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Icon')
                            ->directory('landing-page-deal-of-the-week')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        TextInput::make('rate')->label('Rate')->live()->minValue(0)->required()->rule('gte:0')->extraAttributes(['oninput' => "this.value = Math.max(0, this.value)"]),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle')->required(),
                        TextInput::make('price')->label('Price')->required(),
                        TextInput::make('after_discount_price')->label('After Discount Price')->required(),
                        DatePicker::make('date_of_birth')->label('End Date')->native(false),
                        TextInput::make('cta_button_text')->label('Cta Button Text'),
                        TextInput::make('cta_button_link')->label('Cta Button Link'),
                    ])
                    ->label('Deals')
                    ->createItemButtonLabel('Add Deals'),
                ])->collapsed(),

                Section::make('Why Choose Us Section')->schema([
                    TextInput::make('why_choose_us_title')->label('Title'),
                    TextInput::make('why_choose_us_subtitle')->label('Subtitle'),
                    Toggle::make('is_why_choose_us_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_why_choose_us_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('why_choose_us_section_top_image')
                        ->label('Why Choose Us Section Top Image')
                        ->directory('landing-page-why-choose-us-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('why_choose_us_section_bottom_image')
                        ->label('Why Choose Us Section Bottom Image')
                        ->directory('landing-page-why-choose-us-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_why_choose_us')->label('Status'),
                    FileUpload::make('why_choose_us_video')
                        ->label('Why Choose Us Video')
                        ->directory('landing-page-why-choose-us-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Repeater::make('why_choose_us_content')
                    ->relationship('whyChooseUsItems')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Icon')
                            ->directory('landing-page-why-choose-us-section')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        TextInput::make('title')->label('Title')->required(),
                        ColorPicker::make('background_color')->label('Background Color')->hex(),
                        ColorPicker::make('text_color')->label('Text Color')->hex(),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
                ])->collapsed(),

                Section::make('Compare Section')->schema([
                    TextInput::make('compare_title')->label('Title'),
                    TextInput::make('compare_subtitle')->label('Subtitle'),
                    Toggle::make('is_compares_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_compares_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('compares_section_top_image')
                        ->label('Compare Section Top Image')
                        ->directory('landing-page-compare-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable(),
                    FileUpload::make('compares_section_bottom_image')
                        ->label('Compare Section Bottom Image')
                        ->directory('landing-page-compare-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable(),
                    Toggle::make('is_compare')->label('Status'),
                    Repeater::make('compare_content')
                    ->relationship('comparesItems')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Icon')
                            ->directory('landing-page-compare-section')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle')->required(),
                        TextInput::make('price')->label('Price')->required(),
                        TextInput::make('brand')->label('Brand')->required(),
                        TextInput::make('color')->label('Color')->required(),
                        TextInput::make('dimensions')->label('Dimensions')->required(),
                        TextInput::make('weight')->label('Weight')->required(),
                        TextInput::make('attributes')->label('Attributes')->required(),
                        Toggle::make('cta_button')->label('Cta Button'),
                        TextInput::make('cta_button_text')->label('Cta Button Text')->required(),
                        TextInput::make('cta_button_link')->label('Cta Button Link')->required(),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
                ])->collapsed(),

                Section::make('Feedbacks Section')->schema([
                    TextInput::make('feedback_title')->label('Title'),
                    TextInput::make('feedback_subtitle')->label('Subtitle'),
                    Toggle::make('is_feedbacks_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_feedbacks_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('feedbacks_section_top_image')
                        ->label('Feedbacks Section Top Image')
                        ->directory('landing-page-feedbacks-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('feedbacks_section_bottom_image')
                        ->label('Feedbacks Section Bottom Image')
                        ->directory('landing-page-feedbacks-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_feedbacks')->label('Status'),
                    Repeater::make('feedbacks_content')
                    ->relationship('feedbacksItems')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Icon')
                            ->directory('landing-page-feedbacks-section')
                            ->preserveFilenames()
                            // ->acceptedFileTypes(self::$acceptedFileTypes)
                            ->maxSize(self::$fileMaxSize)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        TextInput::make('comment')->label('Comment')->required(),
                        TextInput::make('user_name')->label('User Name')->required(),
                        TextInput::make('user_position')->label('User Position')->required(),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
                ])->collapsed(),

                Section::make('Faq Section')->schema([
                    TextInput::make('faq_title')->label('Title'),
                    TextInput::make('faq_subtitle')->label('Subtitle'),
                    Toggle::make('is_faq_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_faq_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('faq_section_top_image')
                        ->label('Faq Section Top Image')
                        ->directory('landing-page-faq-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('faq_section_bottom_image')
                        ->label('Faq Section Bottom Image')
                        ->directory('landing-page-faq-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_faq')->label('Status'),
                    FileUpload::make('faq_image')
                        ->label('Faq Image')
                        ->directory('landing-page-faq-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Repeater::make('content')
                    ->relationship('faqsItems')
                    ->schema([
                        TextInput::make('question')->label('Question')->required(),
                        TextInput::make('answer')->label('Answer')->required(),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
                ])->collapsed(),

                Section::make('Footer Section')->schema([
                    TextInput::make('footer_title')->label('Title'),
                    TextInput::make('footer_subtitle')->label('Subtitle'),
                    Toggle::make('is_footer_section_top_image')->label('Show Top Image'),
                    Toggle::make('is_footer_section_bottom_image')->label('Show Bottom Image'),
                    FileUpload::make('footer_section_top_image')
                        ->label('Footer Section Top Image')
                        ->directory('landing-page-footer-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    FileUpload::make('footer_section_bottom_image')
                        ->label('Footer Section Bottom Image')
                        ->directory('landing-page-footer-section')
                        ->preserveFilenames()
                        // ->acceptedFileTypes(self::$acceptedFileTypes)
                        ->maxSize(self::$fileMaxSize)
                        ->downloadable()
                        ->openable()
                        ->required(),
                    Toggle::make('is_footer')->label('Status'),
                    FileUpload::make('footer_image')
                    ->label('Footer Image')
                    ->directory('landing-page-faq-section')
                    ->preserveFilenames()
                    // ->acceptedFileTypes(self::$acceptedFileTypes)
                    ->maxSize(self::$fileMaxSize)
                    ->downloadable()
                    ->openable()
                    ->required(),
                ])->collapsed(),

                Section::make('Counter Section')->schema([
                    TextInput::make('counter_section_cta_button_text')->label('Counter Section Cta Button Text'),
                    TextInput::make('counter_section_cta_button_link')->label('Counter Section Cta Button Link'),
                    DatePicker::make('counter_section_end_date')->label('Counter End Date')->native(false),
                    Toggle::make('is_counter_section')->label('Status'),
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
                TextColumn::make('slug')->searchable(),
                TextColumn::make('sku')->searchable(),
                IconColumn::make('is_home')->boolean(),
                IconColumn::make('is_about')->boolean(),
                IconColumn::make('is_features')->boolean(),
                IconColumn::make('is_products')->boolean(),
                IconColumn::make('is_compare')->boolean(),
                IconColumn::make('is_feedbacks')->boolean(),
                IconColumn::make('is_faq')->boolean(),
                IconColumn::make('is_footer')->boolean(),
                IconColumn::make('status')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                ActionGroup::make([
                    Action::make('view')
                        ->url(fn ($record) => route('landing-page.show-by-slug', ['slug' => $record->slug]))
                        ->color('success')
                        ->icon('heroicon-o-eye')
                        ->openUrlInNewTab(),

                    Action::make('Shipping Types')
                        ->form([self::SyncShippingTypes()])
                        ->action(fn (array $data) => Log::info($data))
                        ->icon('heroicon-o-tag')
                        ->color('primary'),

                    Action::make('Shipping Zones')
                        ->form([self::SyncShippingZones()])
                        ->action(fn (array $data) => Log::info($data))
                        ->icon('heroicon-o-map')
                        ->color('warning'),

                    Action::make('Shipping Governorates')
                        ->form([self::SyncShippingGovernorates()])
                        ->action(fn (array $data) => Log::info($data))
                        ->icon('heroicon-o-building-office')
                        ->color('danger'),

                    Action::make('Shipping Regions')
                        ->form([self::SyncShippingRegions()])
                        ->action(fn (array $data) => Log::info($data))
                        ->icon('heroicon-o-map-pin')
                        ->color('info'),
                ])
                ->dropdown(true)
                ->label('Shipping Options')
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
                ->label('Shipping Types')
                ->schema([
                    TextInput::make('name')->label('Name')->disabled(),
                    TextInput::make('shipping_cost')->label('Shipping Cost')->numeric(false),
                    Toggle::make('status')->label('Active'),
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
                ->label('Shipping Zones')
                ->schema([
                    TextInput::make('name')->label('Name')->disabled(),
                    Select::make('governorates')->label('Governorates')->multiple()->options($governorates),
                    TextInput::make('shipping_cost')->label('Shipping Cost')->numeric(false),
                    Toggle::make('status')->label('Active'),
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
                ->label('Shipping Governorates')
                ->schema([
                    TextInput::make('name')->label('Name')->disabled(),
                    TextInput::make('shipping_cost')->label('Shipping Cost')->numeric(false),
                    Toggle::make('status')->label('Active'),
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
                ->label('Shipping Regions')
                ->schema([
                    TextInput::make('name')->label('Name')->disabled(),
                    TextInput::make('governorate')->label('Governorate')->disabled(),
                    TextInput::make('shipping_cost')->label('Shipping Cost')->numeric(false),
                    Toggle::make('status')->label('Active'),
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
