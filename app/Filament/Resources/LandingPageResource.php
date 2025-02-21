<?php

namespace App\Filament\Resources;

use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\LandingPageVarieties;
use Filament\Forms\{Get, Set, Form};
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\{IconColumn, TextColumn};
use Filament\Tables\Actions\{DeleteAction, EditAction, BulkActionGroup, DeleteBulkAction};
use App\Models\{Size, Color, LandingPage, ShippingType, Region, ShippingZone, Governorate};
use App\Filament\Resources\LandingPageResource\Pages\{EditLandingPage, ListLandingPages, CreateLandingPage};
use Filament\Forms\Components\{Grid, Tabs, Select, Toggle, Section, Repeater, Textarea, TextInput, DatePicker, FileUpload, ColorPicker, Tabs\Tab};

class LandingPageResource extends Resource
{
    protected static ?string $model = LandingPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Products Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Landing Page');
    }

    public static function getModelLabel(): string
    {
        return __('Landing Page');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Landing Page');
    }

    public static function getLabel(): ?string
    {
        return __('Landing Page');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Landing Page');
    }

    public static function form(Form $form): Form
    {
        $governorates = Governorate::pluck('name', 'id')->toArray();

        return $form->schema([
            Tabs::make('Tabs')
                ->tabs([
                    self::baseDataSection(),
                    self::topBarItemsSection(),
                    self::productCriteriaSection(),
                    self::productMediaSection(),
                    self::productFeaturesSection(),
                    self::productBundlesSection(),
                    self::homeSection(),
                    self::aboutSection(),
                    self::featuresSection(),
                    self::features1Section(),
                    self::features2Section(),
                    self::dealOfTheWeekSection(),
                    self::productsSection(),
                    self::whyChooseUsSection(),
                    self::compareSection(),
                    self::feedbacksSection(),
                    self::faqSection(),
                    self::footerSection(),
                    self::counterSection(),
                ])->columnSpanFull(),

            Section::make('Shipping Types')
            ->schema([
                    Repeater::make('shipping_types')
                        ->relationship('LandingPageShippingTypes')
                        ->label('Shipping Types')
                        ->schema([
                            Hidden::make('landing_page_id'),
                            Hidden::make('shipping_type_id'),
                            TextInput::make('name')->label('Name')->disabled(),
                            Toggle::make('status')->label('Active'),
                        ])
                        ->afterStateHydrated(function ($state, $set, $record) {
                            $allShippingTypes = ShippingType::all();

                            if (!$record) {
                                $set('shipping_types', $allShippingTypes->map(fn ($type) => [
                                    'shipping_type_id'  => $type->id,
                                    'landing_page_id'   => request()->route('id') ?? null,
                                    'name'              => $type->name,
                                    'status'            => false,
                                ])->toArray());
                                return;
                            }

                            $existingShippingTypes = $record->LandingPageShippingTypes->keyBy('shipping_type_id');

                            $set('shipping_types', $allShippingTypes->map(fn ($type) => [
                                'landing_page_id'   => $record->id,
                                'shipping_type_id'  => $type->id,
                                'name'              => $type->name,
                                'status'            => $existingShippingTypes[$type->id]->status ?? false,
                            ])->toArray());
                        })
                        ->columns(2)
                        ->defaultItems(ShippingType::count())
                        ->disableItemCreation()
                        ->disableItemDeletion(),
            ])
            ->collapsed(),

            Section::make('Shipping Zones')
            ->schema([
                    Repeater::make('shipping_zones')
                        ->relationship('LandingPageShippingZones')
                        ->label('Shipping Zones')
                        ->schema([
                            TextInput::make('name')->label('Name')->disabled(),
                            Select::make('governorates')->label('Governorates')->multiple()->options($governorates),
                            TextInput::make('shipping_cost')->label('Shipping Cost')->numeric(),
                            Toggle::make('status')->label('Active'),
                            Hidden::make('shipping_zone_id'),
                            Hidden::make('landing_page_id'),
                        ])
                        ->afterStateHydrated(function ($state, $set, $record) {
                            $allShippingZone = ShippingZone::all();

                            if (!$record) {
                                $set('shipping_zones', $allShippingZone->map(fn ($zone) => [
                                    'shipping_zone_id'  => $zone->id,
                                    'landing_page_id'   => null,
                                    'name'              => $zone->name,
                                    'governorates'      => $zone->governorates,
                                    'status'            => false,
                                ])->toArray());
                                return;
                            }

                            $existingShippingZones = $record->LandingPageShippingZones->keyBy('shipping_zone_id');

                            $set('shipping_zones', $allShippingZone->map(fn ($zone) => [
                                'shipping_zone_id'  => $zone->id,
                                'landing_page_id'   => $record->id,
                                'name'              => $zone->name,
                                'governorates'      => $zone->governorates,
                                'status'            => $existingShippingZones[$zone->id]->status ?? false,
                            ])->toArray());
                        })
                        ->columns(4)
                        ->defaultItems(ShippingZone::count())
                        ->disableItemCreation()
                        ->disableItemDeletion(),
            ])
            ->collapsed(),

            Section::make('Shipping Governorates')
            ->schema([
                    Repeater::make('shipping_governorates')
                        ->relationship('LandingPageGovernorates')
                        ->label('Shipping Governorates')
                        ->schema([
                            TextInput::make('name')->label('Name')->disabled(),
                            TextInput::make('shipping_cost')->label('Shipping Cost')->numeric(),
                            Toggle::make('status')->label('Active'),
                            Hidden::make('governorate_id'),
                            Hidden::make('landing_page_id'),
                        ])
                        ->afterStateHydrated(function ($state, $set, $record) {
                            $allGovernorates = Governorate::all();

                            if (!$record) {
                                $set('shipping_governorates', $allGovernorates->map(fn ($governorate) => [
                                    'governorate_id'    => $governorate->id,
                                    'landing_page_id'   => null,
                                    'name'              => $governorate->name,
                                    'shipping_cost'     => $governorate->shipping_cost,
                                    'status'            => false,
                                ])->toArray());
                                return;
                            }

                            $existingGovernorates = $record->LandingPageGovernorates->keyBy('governorate_id');

                            $set('shipping_governorates', $allGovernorates->map(fn ($governorate) => [
                                'governorate_id'    => $governorate->id,
                                'landing_page_id'   => $record->id,
                                'name'              => $governorate->name,
                                'shipping_cost'     => $governorate->shipping_cost,
                                'status'            => $existingGovernorates[$governorate->id]->status ?? false,
                            ])->toArray());
                        })
                        ->columns(3)
                        ->disableItemCreation()
                        ->disableItemDeletion(),
            ])
            ->collapsed(),

            Section::make('Shipping Regions')
            ->schema([
                    Repeater::make('shipping_regions')
                        ->relationship('LandingPageRegions')
                        ->label('Shipping Regions')
                        ->schema([
                            TextInput::make('name')->label('Name')->disabled(),
                            TextInput::make('governorate')->label('Governorate')->disabled(),
                            TextInput::make('shipping_cost')->label('Shipping Cost')->numeric(),
                            Toggle::make('status')->label('Active'),
                            Hidden::make('region_id'),
                            Hidden::make('landing_page_id'),
                        ])
                        ->afterStateHydrated(function ($state, $set, $record) {
                            $allRegions = Region::all();

                            if (!$record) {
                                $set('shipping_regions', $allRegions->map(fn ($region) => [
                                    'region_id'         => $region->id,
                                    'landing_page_id'   => null,
                                    'name'              => $region->name,
                                    'governorate'       => $region->governorate->name ?? null,
                                    'shipping_cost'     => $region->shipping_cost,
                                    'status'            => false,
                                ])->toArray());
                                return;
                            }

                            $existingRegions = $record->LandingPageRegions->keyBy('region_id');

                            $set('shipping_regions', $allRegions->map(fn ($region) => [
                                'region_id'         => $region->id,
                                'landing_page_id'   => $record->id,
                                'name'              => $region->name,
                                'governorate'       => $region->governorate->name ?? null,
                                'shipping_cost'     => 0,
                                'status'            => $existingRegions[$region->id]->status ?? false,
                            ])->toArray());
                        })
                        ->columns(4)
                        ->disableItemCreation()
                        ->disableItemDeletion(),
            ])
            ->collapsed(),
        ]);
    }

    private static function baseDataSection(): Tab
    {
        return Tab::make('Base Data')
            ->icon('heroicon-o-document-text')
            ->schema([
                TextInput::make('slug')->label('Slug')->required(),
                TextInput::make('sku')->label('SKU')->required(),
                TextInput::make('meta_title')->label('Meta Title')->required(),
                Textarea::make('meta_description')->label('Meta Description')->required(),
                Textarea::make('meta_keywords')->label('Meta Keywords')->required(),
                Toggle::make('status')->label('Status')->default(true),
            ])
            ->columns(2);
    }

    private static function topBarItemsSection(): Tab
    {
        return Tab::make('Top Bar Items')
            ->icon('heroicon-o-bars-3')
            ->schema([
                Repeater::make('topBars')
                    ->relationship('topBars')
                    ->schema([
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('link')->label('Link')->required(),
                    ])
                    ->label('Top Bar Items')
                    ->createItemButtonLabel('Add Top Bar Item')
                    ->columns(2),
            ]);
    }

    private static function productCriteriaSection(): Tab
    {
        return Tab::make('Product Criteria')
            ->icon('heroicon-o-list-bullet')
            ->schema([
                TextInput::make('title')->label('Product Title')->required(),
                Textarea::make('description')->label('Product Description')->required(),
                TextInput::make('price')->label('Price')->numeric()->required(),
                TextInput::make('after_discount_price')->label('After Discount Price')->numeric(),
                TextInput::make('rating')->label('Rating')->numeric()->minValue(0)->maxValue(9.9)->step(0.1)->required(),
                TextInput::make('quantity')->label('Quantity')->numeric()->required(),
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
                                        TextInput::make('price')->label('Price')->numeric(),
                                        TextInput::make('quantity')->label('Quantity')->numeric(),
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
                    ])
            ])
            ->columns(2);
    }

    private static function productMediaSection(): Tab
    {
        return Tab::make('Product Media')
            ->icon('heroicon-o-photo')
            ->schema([
                Repeater::make('product_media')
                    ->relationship('media')
                    ->schema([
                        FileUpload::make('url')->label('Product Media')->multiple()->directory('landing-page-media'),
                    ])
                    ->label('Product Media')
                    ->createItemButtonLabel('Add Product Media'),
            ]);
    }

    private static function productFeaturesSection(): Tab
    {
        return Tab::make('Product Features')
            ->icon('heroicon-o-sparkles')
            ->schema([
                Repeater::make('product_features')
                ->relationship('features')
                    ->schema([
                        TextInput::make('title')->label('Title')->required(),
                    ])
                    ->label('Product Features')
                    ->createItemButtonLabel('Add Product Feature'),
            ]);
    }

    private static function productBundlesSection(): Tab
    {
        return Tab::make('Product Bundles')
            ->icon('heroicon-o-gift')
            ->schema([
                Repeater::make('product_bundles')
                    ->relationship('bundles')
                    ->schema([
                        TextInput::make('name')->label('Name')->required(),
                        TextInput::make('quantity')->label('Quantity')->numeric()->required(),
                        TextInput::make('price')->label('Price')->numeric()->required(),
                    ])
                    ->label('Product Bundles')
                    ->createItemButtonLabel('Add Product Bundle'),
            ]);
    }

    private static function homeSection(): Tab
    {
        return Tab::make('Home Section')
            ->icon('heroicon-o-home')
            ->schema([
                TextInput::make('home_title')->label('Title'),
                TextInput::make('home_subtitle')->label('Subtitle'),
                Toggle::make('is_home')->label('Status'),
                Toggle::make('home_show_cta_button')->label('Show Cta Button'),
                Toggle::make('is_home_section_top_image')->label('Show Top Image'),
                Toggle::make('is_home_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('home_section_top_image')->label('Home Section Top Image')->directory('landing-page-home'),
                FileUpload::make('home_section_bottom_image')->label('Home Section Bottom Image')->directory('landing-page-home'),
                TextInput::make('home_cta_button_text')->label('Home Cta Button Text'),
                TextInput::make('home_cta_button_link')->label('Home Cta Button Link'),
                TextInput::make('home_discount')->label('Home Discount')->numeric()->required(),
                FileUpload::make('home_image')->label('Home Image')->directory('landing-page-home'),
            ])
            ->columns(2);
    }

    private static function aboutSection(): Tab
    {
        return Tab::make('About Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('about_title')->label('Title'),
                TextInput::make('about_subtitle')->label('Subtitle'),
                Toggle::make('is_about_section_top_image')->label('Show Top Image'),
                Toggle::make('is_about_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('about_section_top_image')->label('About Section Top Image')->directory('landing-page-about'),
                FileUpload::make('about_section_bottom_image')->label('About Section Bottom Image')->directory('landing-page-about'),
                Toggle::make('is_about')->label('Status'),
                Repeater::make('about_content')
                    ->relationship('aboutItems')
                    ->schema([
                        FileUpload::make('image')->label('Icon')->directory('landing-page-about-icon'),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle'),
                        Toggle::make('cta_button')->label('Show Cta Button'),
                        TextInput::make('cta_button_text')->label('Cta Button Text'),
                        TextInput::make('cta_button_link')->label('Cta Button Link'),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
            ])
            ->columns(2);
    }

    private static function featuresSection(): Tab
    {
        return Tab::make('Features Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('feature_title')->label('Title'),
                TextInput::make('feature_subtitle')->label('Subtitle'),
                Toggle::make('is_features3_section_top_image')->label('Show Top Image'),
                Toggle::make('is_features3_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('features3_section_top_image')->label("Features 1 Section Top Image")->directory('landing-page-feature'),
                FileUpload::make('features3_section_bottom_image')->label("Features 1 Section Bottom Image")->directory('landing-page-feature'),
                Toggle::make('is_features')->label('Status'),
                Toggle::make('is_feature_cta_button')->label('Show Cat Bottom'),
                TextInput::make('feature_cta_button_text')->label('Cta Button Text'),
                TextInput::make('feature_cta_button_link')->label('Cta Button Link'),
                FileUpload::make('feature_image')->label('Image')->directory('landing-page-feature'),
                Repeater::make('feature_content')
                    ->relationship('featuresItems')
                    ->schema([
                        FileUpload::make('image')->label('Icon')->directory('landing-page-feature-icon'),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle'),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
            ])
            ->columns(2);
    }

    private static function features1Section(): Tab
    {
        return Tab::make('Features 1 Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('feature1_title')->label('Title'),
                TextInput::make('feature1_subtitle')->label('Subtitle'),
                Toggle::make('is_features1_section_top_image')->label('Show Top Image'),
                Toggle::make('is_features1_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('features1_section_top_image')->label("Features 1 Section Top Image")->directory('landing-page-feature'),
                FileUpload::make('features1_section_bottom_image')->label("Features 1 Section Bottom Image")->directory('landing-page-feature'),
                Toggle::make('is_features1')->label('Status'),
                Toggle::make('is_feature1_cta_button')->label('Show Cat Bottom'),
                TextInput::make('feature1_cta_button_text')->label('Cta Button Text'),
                TextInput::make('feature1_cta_button_link')->label('Cta Button Link'),
                FileUpload::make('feature1_image')->label('Image')->directory('landing-page-feature'),
                Repeater::make('feature_content')
                    ->relationship('featuresItems')
                    ->schema([
                        FileUpload::make('image')->label('Icon')->directory('landing-page-feature-icon'),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle'),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
            ])
            ->columns(2);
    }

    private static function features2Section(): Tab
    {
        return Tab::make('Features 2 Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('feature2_title')->label('Title'),
                TextInput::make('feature2_subtitle')->label('Subtitle'),
                Toggle::make('is_features2_section_top_image')->label('Show Top Image'),
                Toggle::make('is_features2_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('features2_section_top_image')->label("Features 2 Section Top Image")->directory('landing-page-feature'),
                FileUpload::make('features2_section_bottom_image')->label("Features 2 Section Bottom Image")->directory('landing-page-feature'),
                Toggle::make('is_features2')->label('Status'),
                Toggle::make('is_feature2_cta_button')->label('Show Cat Bottom'),
                TextInput::make('feature2_cta_button_text')->label('Cta Button Text'),
                TextInput::make('feature2_cta_button_link')->label('Cta Button Link'),
                FileUpload::make('feature2_image')->label('Image')->directory('landing-page-feature'),
                Repeater::make('feature_content')
                    ->relationship('featuresItems')
                    ->schema([
                        FileUpload::make('image')->label('Icon')->directory('landing-page-feature-icon'),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle'),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
            ])
            ->columns(2);
    }

    private static function dealOfTheWeekSection(): Tab
    {
        return Tab::make('Deal Of The Week Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('deal_of_the_week_title')->label('Title'),
                TextInput::make('deal_of_the_week_subtitle')->label('Subtitle'),
                Toggle::make('is_deal_of_the_week_section_top_image')->label('Deal Of The Week Section Show Top Image'),
                Toggle::make('is_deal_of_the_week_section_bottom_image')->label('Deal Of The Week Section Show Bottom Image'),
                FileUpload::make('deal_of_the_week_section_top_image')->label('Deal Of The Week Section Top Image')->directory('landing-page-deal-of-the-week'),
                FileUpload::make('deal_of_the_week_section_bottom_image')->label('Deal Of The Week Section Bottom Image')->directory('landing-page-deal-of-the-week'),
                Toggle::make('is_deal_of_the_week')->label('Status'),
                Repeater::make('deals')
                    ->relationship('dealOfTheWeekItems')
                    ->schema([
                        FileUpload::make('image')->label('Icon')->directory('landing-page-deal-of-the-week'),
                        TextInput::make('rate')->label('Rate')->live()->numeric()->minValue(0)->required()->rule('gte:0')->extraAttributes(['oninput' => "this.value = Math.max(0, this.value)"]),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle')->required(),
                        TextInput::make('price')->label('Price')->numeric()->required(),
                        TextInput::make('after_discount_price')->label('After Discount Price')->numeric(),
                        DatePicker::make('date_of_birth')->label('End Date')->native(false),
                        TextInput::make('cta_button_text')->label('Cta Button Text'),
                        TextInput::make('cta_button_link')->label('Cta Button Link'),
                    ])
                    ->label('Deals')
                    ->createItemButtonLabel('Add Deals'),
            ])
            ->columns(2);
    }

    private static function productsSection(): Tab
    {
        return Tab::make('Products Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('product_title')->label('Title'),
                TextInput::make('product_subtitle')->label('Subtitle'),
                Toggle::make('is_products_section_top_image')->label('Show Top Image'),
                Toggle::make('is_products_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('products_section_top_image')->label('Products Section Top Image')->directory('landing-page-products-section'),
                FileUpload::make('products_section_bottom_image')->label('Products Section Bottom Image')->directory('landing-page-products-section'),
                Toggle::make('is_products')->label('Status'),
                Repeater::make('products_content')
                    ->relationship('productsItems')
                    ->schema([
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle'),
                        FileUpload::make('image')->label('Icon')->directory('landing-page-deal-of-the-week'),
                        Toggle::make('status')->label('Status'),
                        Toggle::make('cat_bottom')->label('Show Cat Bottom'),
                        TextInput::make('cat_buttom_text')->label('Cta Button Text')->required(),
                        TextInput::make('cat_buttom_link')->label('Cta Button Link')->required(),
                        TextInput::make('price')->label('Price')->numeric()->required(),
                        TextInput::make('after_discount_price')->label('After Discount Price')->numeric(),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
            ])
            ->columns(2);
    }

    private static function whyChooseUsSection(): Tab
    {
        return Tab::make('Why Choose Us Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('why_choose_us_title')->label('Title'),
                TextInput::make('why_choose_us_subtitle')->label('Subtitle'),
                Toggle::make('is_why_choose_us_section_top_image')->label('Show Top Image'),
                Toggle::make('is_why_choose_us_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('why_choose_us_section_top_image')->label('Why Choose Us Section Top Image')->directory('landing-page-why-choose-us-section'),
                FileUpload::make('why_choose_us_section_bottom_image')->label('Why Choose Us Section Bottom Image')->directory('landing-page-why-choose-us-section'),
                Toggle::make('is_why_choose_us')->label('Status'),
                FileUpload::make('why_choose_us_video')->label('Why Choose Us Video')->directory('landing-page-why-choose-us-section'),
                Repeater::make('why_choose_us_content')
                    ->relationship('whyChooseUsItems')
                    ->schema([
                        FileUpload::make('image')->label('Icon')->directory('landing-page-why-choose-us-section'),
                        TextInput::make('title')->label('Title')->required(),
                        ColorPicker::make('background_color')->label('Background Color')->hex(),
                        ColorPicker::make('text_color')->label('Text Color')->hex(),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
            ])
            ->columns(2);
    }

    private static function compareSection(): Tab
    {
        return Tab::make('Compare Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('compare_title')->label('Title'),
                TextInput::make('compare_subtitle')->label('Subtitle'),
                Toggle::make('is_compares_section_top_image')->label('Show Top Image'),
                Toggle::make('is_compares_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('compares_section_top_image')->label('Compare Section Top Image')->directory('landing-page-compare-section'),
                FileUpload::make('compares_section_bottom_image')->label('Compare Section Bottom Image')->directory('landing-page-compare-section'),
                Toggle::make('is_compare')->label('Status'),
                Repeater::make('compare_content')
                    ->relationship('comparesItems')
                    ->schema([
                        FileUpload::make('image')->label('Icon')->directory('landing-page-compare-section'),
                        TextInput::make('title')->label('Title')->required(),
                        TextInput::make('subtitle')->label('Subtitle')->required(),
                        TextInput::make('price')->label('Price')->numeric()->required(),
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
            ])
            ->columns(2);
    }

    private static function feedbacksSection(): Tab
    {
        return Tab::make('Feedbacks Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('feedback_title')->label('Title'),
                TextInput::make('feedback_subtitle')->label('Subtitle'),
                Toggle::make('is_feedbacks_section_top_image')->label('Show Top Image'),
                Toggle::make('is_feedbacks_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('feedbacks_section_top_image')->label('Feedbacks Section Top Image')->directory('landing-page-feedbacks-section'),
                FileUpload::make('feedbacks_section_bottom_image')->label('Feedbacks Section Bottom Image')->directory('landing-page-feedbacks-section'),
                Toggle::make('is_feedbacks')->label('Status'),
                Repeater::make('feedbacks_content')
                    ->relationship('feedbacksItems')
                    ->schema([
                        FileUpload::make('image')->label('Icon')->directory('landing-page-feedbacks-section'),
                        TextInput::make('comment')->label('Comment')->required(),
                        TextInput::make('user_name')->label('User Name')->required(),
                        TextInput::make('user_position')->label('User Position')->required(),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
            ])
            ->columns(2);
    }

    private static function faqSection(): Tab
    {
        return Tab::make('Faq Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('faq_title')->label('Title'),
                TextInput::make('faq_subtitle')->label('Subtitle'),
                Toggle::make('is_faq_section_top_image')->label('Show Top Image'),
                Toggle::make('is_faq_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('faq_section_top_image')->label('Faq Section Top Image')->directory('landing-page-faq-section'),
                FileUpload::make('faq_section_bottom_image')->label('Faq Section Bottom Image')->directory('landing-page-faq-section'),
                Toggle::make('is_faq')->label('Status'),
                FileUpload::make('faq_image')->label('Faq Image')->directory('landing-page-faq-section'),
                Repeater::make('content')
                    ->relationship('faqsItems')
                    ->schema([
                        TextInput::make('question')->label('Question')->required(),
                        TextInput::make('answer')->label('Answer')->required(),
                    ])
                    ->label('Content')
                    ->createItemButtonLabel('Add Content'),
            ])
            ->columns(2);
    }

    private static function footerSection(): Tab
    {
        return Tab::make('Footer Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('footer_title')->label('Title'),
                TextInput::make('footer_subtitle')->label('Subtitle'),
                Toggle::make('is_footer_section_top_image')->label('Show Top Image'),
                Toggle::make('is_footer_section_bottom_image')->label('Show Bottom Image'),
                FileUpload::make('footer_section_top_image')->label('Footer Section Top Image')->directory('landing-page-footer-section'),
                FileUpload::make('footer_section_bottom_image')->label('Footer Section Bottom Image')->directory('landing-page-footer-section'),
                Toggle::make('is_footer')->label('Status'),
                FileUpload::make('footer_image')->label('Footer Image')->directory('landing-page-faq-section'),
            ])
            ->columns(2);
    }

    private static function counterSection(): Tab
    {
        return Tab::make('Counter Section')
            ->icon('heroicon-o-information-circle')
            ->schema([
                TextInput::make('counter_section_cta_button_text')->label('Counter Section Cta Button Text'),
                TextInput::make('counter_section_cta_button_link')->label('Counter Section Cta Button Link'),
                DatePicker::make('counter_section_end_date')->label('Counter End Date')->native(false),
                Toggle::make('is_counter_section')->label('Status'),
            ])
            ->columns(2);
    }

    private static function loadExistingCombinations($record): array
    {
        $existingCombinations = LandingPageVarieties::where('landing_page_id', $record->id)->get();

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
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
