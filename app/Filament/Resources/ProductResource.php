<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\BundlesRelationManager;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingCost;
use App\Services\ProductActionsService;
use Closure;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Support\Enums\ActionSize;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Mokhosh\FilamentRating\Columns\RatingColumn;
use Mokhosh\FilamentRating\Components\Rating;

class ProductResource extends Resource
{
    use Translatable;

    protected static ?string $model = Product::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function getNavigationLabel(): string
    {
        return __('product.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Products Management'); //Products Attributes Management
    }

    public static function getModelLabel(): string
    {
        return __('product.label');
    }

    /**
     * @return string|null
     */
    public static function getPluralLabel(): ?string
    {
        return __('product.label');
    }

    public static function getLabel(): ?string
    {
        return __('product.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('product.label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Product Tabs')
                    ->tabs([
                        // General Information Tab
                        Tab::make(__('General Info'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                SelectTree::make('category_id')
                                    ->searchable()
                                    ->enableBranchNode()
                                    ->label(__('_category'))
                                    ->relationship('category', 'name', 'parent_id')
                                    ->placeholder(__('Please select a category'))
                                    ->required(),
                                TextInput::make('name')
                                    ->label(__('Product Name'))
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('slug')
                                    ->unique(ignoreRecord: true)
                                    ->label(__('Slug'))
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        // Pricing & Stock Tab
                        Tab::make(__('Stock & Pricing'))
                            ->columns(2)
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                TextInput::make('sku')
                                    ->label(__('SKU'))
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('Quantity'))
                                    ->required()
                                    ->numeric()
                                    ->default(1),
                                TextInput::make('price')
                                    ->label(__('Price'))
                                    ->required()
                                    ->numeric(),
                                TextInput::make('after_discount_price')
                                    ->lt('price')
                                    ->label(__('After Discount Price'))
                                    ->numeric(),
                                DateTimePicker::make('discount_start')
                                    ->requiredWith('after_discount_price')
                                    ->afterOrEqual('today')
                                    ->label(__('Discount Start'))
                                    ->nullable(),

                                DateTimePicker::make('discount_end')
                                    ->requiredWith('after_discount_price')
                                    ->after('discount_start')
                                    ->label(__('Discount End'))
                                    ->nullable(),
                            ]),

                        Tabs\Tab::make(__('tabs.special_prices'))
                            ->label(__('tabs.special_prices'))
                            ->icon('heroicon-o-flag') // banknotes
                            ->schema([
                                Repeater::make('specialPrices')
                                    ->defaultItems(0)
                                    ->label(__('tabs.special_prices'))
                                    ->relationship('specialPrices')
                                    ->columns(2)
                                    ->schema([
                                        Placeholder::make('country_or_group_info')
                                            ->label(__('country_or_group_info'))
                                            ->content(__('messages.select_country_or_group'))
                                            ->columnSpanFull(),

                                        Select::make('country_id')
                                            ->label(__('fields.select_country'))
                                            ->relationship('country', 'name')
                                            ->nullable()
                                            ->live()
                                            ->afterStateUpdated(fn (Forms\Set $set) => $set('country_group_id', null))
                                            ->hidden(fn (Forms\Get $get) => filled($get('country_group_id')))
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                        Select::make('country_group_id')
                                            ->label(__('fields.select_country_group'))
                                            ->relationship('countryGroup', 'name')
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->label(__('name'))
                                                    ->required()
                                                    ->maxLength(255),

                                                Forms\Components\Select::make('countries')
                                                    ->label(__('countries'))
                                                    ->relationship('countries', 'name')
                                                    ->multiple()
                                                    ->searchable()
                                                    ->preload(),
                                            ])
                                            ->nullable()
                                            ->live()
                                            ->afterStateUpdated(fn (Forms\Set $set) => $set('country_id', null))
                                            ->hidden(fn (Forms\Get $get) => filled($get('country_id')))
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                        Select::make('currency_id')
                                            ->label(__('fields.currency'))
                                            ->relationship('currency', 'name')
                                            ->required(),

                                        TextInput::make('special_price')
                                            ->label(__('Price'))
                                            ->numeric()
                                            ->required(),

                                        TextInput::make('special_price_after_discount')
                                            ->lt('special_price')
                                            ->label(__('After Discount Price'))
                                            ->numeric()
                                            ->nullable(),
                                    ])->columnSpanFull(),
                            ]),
                        Tab::make(__('Features'))
                            ->columns(2)
                            ->icon('heroicon-o-table-cells')
                            ->schema([
                                Select::make('labels')
                                    ->columnSpanFull()
                                    ->label(__('labels.plural_label'))
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('title')
                                            ->label(__('fields.text_title'))
                                            ->columnSpanFull(),
                                        Forms\Components\ColorPicker::make('color_code')
                                            ->label(__('fields.text_color_code')),
                                        Forms\Components\ColorPicker::make('background_color_code')
                                            ->label(__('fields.background_color_code')),
                                    ])
                                    ->multiple()
                                    ->label(__('labels'))
                                    ->relationship('labels', 'title')
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),

                                Repeater::make('productColors')
                                    ->columnSpanFull()
                                    ->relationship('productColors') // Uses hasMany relationship
                                    ->label(__('Colors'))
                                    ->schema([
                                        Select::make('color_id')
                                            ->label(__('Color'))
                                            ->relationship('color', 'name') // Fetch color names
                                            ->required(),

                                        Select::make('sizes')
                                            ->multiple()
                                            ->label(__('Sizes'))
                                            ->relationship('sizes', 'name')
                                            ->preload(),

                                        FileUpload::make('image')
                                            ->columnSpanFull()
                                            ->label(__('Image'))
                                            ->imageEditor()
                                            ->required(),
                                    ])->columns(2)
                                    ->collapsible(),

//                                Repeater::make('types')
//                                    ->defaultItems(0)
//                                    ->label(__('Types'))
//                                    ->columnSpanFull()
//                                    ->relationship('types') // Defines the relationship with ProductType model
//                                    ->schema([
//                                        TextInput::make('name')
//                                            ->required()
//                                            ->label(__('name')),
//
//                                        FileUpload::make('image')
//                                            ->label(__('image'))
//                                            ->imageEditor()
//                                            ->required(),
//                                    ])
//                                    ->collapsible() // Allow collapsing sections
//                                    ->addActionLabel('Add Product Type'), // Custom button label

                                Forms\Components\Section::make(__('Attributes'))
                                    ->schema([
                                        KeyValue::make('custom_attributes')
                                            ->label(__('Custom Attributes'))
                                            ->addActionLabel(__('Add Custom Attribute'))
                                    ]),
                            ]),

                        // Media Tab
                        Tab::make(__('Media'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('feature_product_image')
                                    ->label(__('Feature Image'))
                                    ->required()
                                    ->collection('feature_product_image')
                                    ->image()
                                    ->maxSize(5120),

                                SpatieMediaLibraryFileUpload::make('second_feature_product_image')
                                    ->label(__('Second Feature Image'))

                                    ->collection('second_feature_product_image')
                                    ->image()
                                    ->maxSize(5120),

                                SpatieMediaLibraryFileUpload::make('sizes_image')
                                    ->label(__('Size Guide Image'))

                                    ->collection('sizes_image')
                                    ->image()
                                    ->maxSize(5120),

                                SpatieMediaLibraryFileUpload::make('more_product_images_and_videos')
                                    ->maxFiles(20)
                                    ->label(__('Extra Images and Videos'))

                                    ->collection('more_product_images_and_videos')
                                    ->multiple()
                                    ->acceptedFileTypes(['video/mp4', 'video/mpeg', 'video/quicktime',
                                        'image/jpeg', 'image/png', 'image/webp'])
                                    ->imageEditor()
                                    ->reorderable(),
                            ]),


                        // SEO Tab
                        Tab::make(__('SEO'))
                            ->columns(2)
                            ->icon('heroicon-o-globe-alt') //banknotes
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label(__('Meta Title'))
                                    ->maxLength(255),
                                Textarea::make('meta_description')
                                    ->label(__('Meta Description'))
                                    ->maxLength(255),
                            ])->columns(1),

                        Tabs\Tab::make(__('shipping_cost.navigation_label'))
                            ->icon('heroicon-o-truck')
                            ->schema([
                               Forms\Components\Section::make()->schema([
                                   Forms\Components\TextInput::make('cost')
                                       ->numeric()
                                       ->label(__('Shipping cost worldwide')),

                                   Forms\Components\TextInput::make('shipping_estimate_time')
                                       ->label(__('Shipping estimate time worldwide')),

                                   Forms\Components\Checkbox::make('is_free_shipping')
                                       ->default(false)
                                       ->label(__('Is free shipping cost?')),
                               ])->columns(2),
                                Repeater::make('shipping_costs')
                                    ->defaultItems(0)
                                    ->label(__('shipping_cost.navigation_label'))
                                    ->relationship('shippingCosts')
                                    ->schema([
                                        Select::make('country_group_id')
                                            ->label(__('shipping_cost.country_group'))
                                            ->rules(fn (Get $get, $record) => [
                                                Rule::unique(ShippingCost::class, 'country_group_id')
                                                    ->where(fn ($query) => $query
                                                        ->where('product_id', $get('../../id'))
                                                        ->whereNull('governorate_id')
                                                        ->whereNull('shipping_zone_id')
                                                        ->whereNull('city_id')
                                                        ->whereNull('country_id')
                                                    )
                                                    ->when($record, fn ($rule) => $rule->ignore($record->id))
                                            ])
                                            ->relationship('countryGroup', 'name')
                                            ->nullable()
                                            ->live()
                                            ->hidden(fn (Get $get) => $get('../city_id') || $get('../governorate_id') || $get('../shipping_zone_id') || $get('../country_id')),

                                        Select::make('shipping_zone_id')
                                            ->label(__('shipping_zone.name'))
                                            ->rules(fn (Get $get, $record) => [
                                                Rule::unique(ShippingCost::class, 'shipping_zone_id')
                                                    ->where(fn ($query) => $query
                                                        ->where('product_id', $get('../../id'))
                                                        ->whereNull('governorate_id')
                                                        ->whereNull('country_group_id')
                                                        ->whereNull('city_id')
                                                        ->whereNull('country_id')
                                                    )
                                                    ->when($record, fn ($rule) => $rule->ignore($record->id))
                                            ])
                                            ->relationship('shippingZone', 'name')
                                            ->nullable()
                                            ->live()
                                            ->hidden(fn (Get $get) => $get('../city_id') || $get('../governorate_id') || $get('../country_id') || $get('../country_group_id')),

                                        Select::make('country_id')
                                            ->label(__('shipping_cost.country'))
                                            ->searchable()
                                            ->rules(fn (Get $get, $record) => [
                                                Rule::unique(ShippingCost::class, 'country_id')
                                                    ->where(fn ($query) => $query
                                                        ->where('product_id', $get('../../id'))
                                                        ->whereNull('governorate_id')
                                                        ->whereNull('city_id')
                                                    )
                                                    ->when($record, fn ($rule) => $rule->ignore($record->id))
                                            ])
                                            ->relationship('country', 'name')
                                            ->nullable()
                                            ->live()
                                            ->hidden(fn (Get $get) => $get('governorate_id') || $get('city_id')) // ✅ Hide when governorate or city is set
                                            ->afterStateHydrated(fn (Set $set, Get $get, $state) =>
                                            $set('country_id', $state ?: $get('../../country_id')) // ✅ Ensure correct value is selected
                                            )
                                            ->dehydrated(fn (Get $get) => !$get('governorate_id') && !$get('city_id')),

                                        Select::make('governorate_id')
                                            ->label(__('shipping_cost.governorate'))
                                            ->rules(fn (Get $get, $record) => [
                                                Rule::unique(ShippingCost::class, 'governorate_id')
                                                    ->where(fn ($query) => $query
                                                        ->where('product_id', $get('../../id'))
                                                        ->whereNull('shipping_zone_id')
                                                        ->whereNull('country_group_id')
                                                        ->whereNull('city_id')
                                                        ->whereNull('country_id')
                                                    )
                                                    ->when($record, fn ($rule) => $rule->ignore($record->id))
                                            ])
                                            ->options(fn (Get $get, $record) =>
                                            $get('country_id')
                                                ? Governorate::where('country_id', $get('country_id'))
                                                ->orWhere('id', $record?->governorate_id) // Ensure existing governorate is included
                                                ->pluck('name', 'id')
                                                : ($record?->governorate_id ? Governorate::where('id', $record->governorate_id)->pluck('name', 'id') : [])
                                            )
                                            ->nullable()
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
                                            ->hidden(fn ($get) => $get('city_id')),


                                        // City Select
                                        Select::make('city_id')
                                            ->label(__('shipping_cost.city'))
                                            ->rules(fn (Get $get, $record) => [
                                                Rule::unique(ShippingCost::class, 'city_id')
                                                    ->where(fn ($query) => $query
                                                        ->where('product_id', $get('../../id'))
                                                        ->whereNull('governorate_id')
                                                        ->whereNull('country_id')
                                                    )
                                                    ->when($record, fn ($rule) => $rule->ignore($record->id))
                                            ])
                                            ->options(fn (Get $get, $record) =>
                                            $get('governorate_id')
                                                ? City::where('governorate_id', $get('governorate_id'))
                                                ->orWhere('id', $record?->city_id)
                                                ->pluck('name', 'id')
                                                : ($record?->city_id ? City::where('id', $record->city_id)->pluck('name', 'id') : [])
                                            )
                                            ->nullable()
                                            ->live()
                                            ->afterStateHydrated(fn (Set $set, Get $get, $state) =>
                                            $set('city_id', $state ?: $get('../../city_id'))
                                            )
                                            ->dehydrated(true)
                                            ->hidden(false),

        TextInput::make('cost')
            ->label(__('shipping_cost.cost'))
            ->required()
            ->numeric()
            ->default(0),
        TextInput::make('shipping_estimate_time')
            ->label(__('shipping_cost.shipping_estimate_time'))
            ->required()
            ->maxLength(255)
            ->default('0-0'),
     ])
              ->columns(2),
                            ]),

                        Tabs\Tab::make(__('product.availability'))
                            ->icon('heroicon-o-globe-asia-australia')
                            ->schema([
                                CheckboxList::make('countries')
                                    ->label(__('product.available_countries')) // Translatable label
                                    ->relationship(
                                        name: 'countries',
                                        titleAttribute: 'name'
                                    )
                                    ->searchable()
                                    ->columns(5)
                                    ->bulkToggleable()
                                    ->selectAllAction(fn ($action) => $action->label(__('product.select_all')))
                                    ->deselectAllAction(fn ($action) => $action->label(__('product.deselect_all'))),
                            ]),


                        // Additional Info Tab
                        Tab::make(__('Additional Info'))
                            ->columns(3)
                            ->icon('heroicon-o-plus-circle')
                            ->schema([
                                Rating::make('fake_average_rating')
                                    ->default(0)
                                    ->columnSpanFull()
                                    ->live()
                                    ->allowZero()
                                    ->label(__('Rating')),
                                TextInput::make('views')
                                    ->label(__('Views'))
                                    ->numeric()
                                    ->default(0),
                                TextInput::make('sales')
                                    ->label(__('Sales'))
                                    ->numeric()
                                    ->default(0),

                                Select::make('complementaryProducts')
                                    ->columnSpanFull()
                                    ->label(__('Complementary Products'))
                                    ->relationship('complementaryProducts', 'name')
                                    ->multiple()
                                    ->preload(),

                                Textarea::make('summary')
                                    ->rules([
                                        fn (): Closure => function (string $attribute, $value, Closure $fail) {
                                            if (strlen($value) > 255) {
                                                $fail(__('validation.max.string', ['attribute' => __('Small description'), 'max' => 255]));
                                            }
                                        },
                                    ])
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->nullable()
                                    ->label(__('Small description')),

                                MarkdownEditor::make('description')
                                    ->columnSpanFull()
                                    ->label(__('Description'))
                                    ->columnSpanFull(),
                                Forms\Components\Checkbox::make('is_published')
                                    ->columnSpanFull()
                                    ->default(true)
                                    ->label(__('Is Published?'))
                                    ->required(),
                                Forms\Components\Checkbox::make('is_featured')
                                    ->columnSpanFull()
                                    ->label(__('Is Featured?'))
                                    ->required(),
                            ])->columns(2),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(__('id')),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('feature_product_image')
                    ->toggleable(true, false)
                    ->circular()
                    ->simpleLightbox()
                    ->placeholder('-')
                    ->collection('feature_product_image')
                    ->label(__('products.Product Image')),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('second_feature_product_image')
                    ->toggleable(true, false)
                    ->circular()
                    ->simpleLightbox()
                    ->placeholder('-')
                    ->collection('second_feature_product_image')
                    ->label(__('Second Feature Image')),

                Tables\Columns\SpatieMediaLibraryImageColumn::make('sizes_image')
                    ->toggleable(true, false)
                    ->circular()
                    ->simpleLightbox()
                    ->placeholder('-')
                    ->collection('sizes_image')
                    ->label(__('Sizes Image')),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('products.User')),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('products.Category'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('label.title')
                    ->label(__('label')),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('products.Product Name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('summary')
                    ->placeholder('-')
                    ->label(__('Small description'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('products.Price'))
                    ->formatStateUsing(function ($state) {
                        $currency = Setting::getCurrency();
                        $symbol = $currency?->symbol ?? '';

                        $locale = app()->getLocale();
                        return $locale === 'ar' ? "{$state} {$symbol}" : "{$symbol} {$state}";
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sku')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('SKU')),

                Tables\Columns\TextColumn::make('colors.name')
                    ->placeholder('-')
                    ->label(__('Colors'))
                    ->limitList(2)
                    ->badge(),

                Tables\Columns\TextColumn::make('sizes.name')
                    ->placeholder('-')
                    ->label(__('Sizes'))
                    ->limitList(2)
                    ->badge(),

                Tables\Columns\TextColumn::make('slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('products.Slug'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('meta_title')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('products.Meta Title'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('meta_description')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('products.Meta Description'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('after_discount_price')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('products.After Discount Price'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_start')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('products.Discount Start'))
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_end')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('products.Discount End'))
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('views')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('products.Views'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sales')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('products.Sales'))
                    ->numeric()
                    ->sortable(),

                RatingColumn::make('fake_average_rating')
                    ->label(__('products.Rating')),

                Tables\Columns\IconColumn::make('is_published')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default(true)
                    ->label(__('products.Is Published'))
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('products.Is Featured'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('products.Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('products.Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make(
                    array_merge([
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\DeleteAction::make(),
                    ], ProductActionsService::getActions())
                ) ->label(__('Actions'))
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('media'); // Count media instead of loading full records
    }

    public static function getRelations(): array
    {
       return [
           BundlesRelationManager::class
       ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
