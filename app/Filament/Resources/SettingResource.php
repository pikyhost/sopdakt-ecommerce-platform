<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use libphonenumber\PhoneNumberUtil;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class SettingResource extends Resource
{
    use Translatable;

    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string
    {
        return __('Settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings Management');
    }

    public static function getModelLabel(): string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Website Settings');
    }

    public static function getPluralLabel(): string
    {
        return __('Settings');
    }

    public static function getLabel(): string
    {
        return __('Settings');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('site_name')
                    ->label(__('Website Name')),

                TextColumn::make('phone')
                    ->label(__('phone')),

                TextColumn::make('email')
                    ->label(__('email')),

                ImageColumn::make('logo_en')
                    ->label(__('Logo (English)')),

                ImageColumn::make('logo_ar')
                    ->label(__('Logo (Arabic)')),

                ImageColumn::make('dark_logo_en')
                    ->label(__('Dark Logo (English)')),

                ImageColumn::make('dark_logo_ar')
                    ->label(__('Dark Logo (Arabic)')),

                ImageColumn::make('favicon')
                    ->label(__('Favicon (English & Arabic)')),

                TextColumn::make('facebook')
                    ->label(__('facebook'))
                    ->url(fn ($record) => $record->facebook, true)
                    ->openUrlInNewTab(),

                TextColumn::make('youtube')
                    ->label(__('youtube'))
                    ->url(fn ($record) => $record->youtube, true)
                    ->openUrlInNewTab(),

                TextColumn::make('instagram')
                    ->label(__('instagram'))
                    ->url(fn ($record) => $record->instagram, true)
                    ->openUrlInNewTab(),

                TextColumn::make('x')
                    ->label(__('x'))
                    ->url(fn ($record) => $record->x, true)
                    ->openUrlInNewTab(),

                TextColumn::make('snapchat')
                    ->label(__('snapchat'))
                    ->url(fn ($record) => $record->snapchat, true)
                    ->openUrlInNewTab(),

                TextColumn::make('tiktok')
                    ->label(__('tiktok'))
                    ->url(fn ($record) => $record->tiktok, true)
                    ->openUrlInNewTab(),

                TextColumn::make('tax_percentage')
                    ->label(__('Tax Percentage'))
                    ->suffix('%'),

                TextColumn::make('currency.name')
                    ->label(__('Currency')),

                IconColumn::make('shipping_type_enabled')
                    ->label(__('Enable Shipping Types'))
                    ->boolean(),

                IconColumn::make('shipping_locations_enabled')
                    ->label(__('Enable Shipping Locations'))
                    ->boolean(),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Website & Contact Information'))
                    ->collapsed(true)
                    ->description(__('Update website name and contact info'))
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->columnSpanFull()
                            ->label(__('Website Name'))
                            ->afterStateUpdated(function ($state) {
                                // Update APP_NAME in .env file
                                $envPath = base_path('.env');
                                $envContent = File::get($envPath);

                                // Clean quotes and prepare new line
                                $cleanValue = trim($state);
                                $envValue = str_contains($cleanValue, ' ') ? "\"{$cleanValue}\"" : $cleanValue;

                                // Replace APP_NAME line
                                $envContent = preg_replace('/^APP_NAME=.*$/m', "APP_NAME={$envValue}", $envContent);

                                // Write back to .env
                                File::put($envPath, $envContent);
                            }),

                        PhoneInput::make('phone')
                            ->columnSpanFull()
                            ->separateDialCode(true)
                            ->enableIpLookup(true)
                            ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
                            ->required()
                            ->rules([
                                // Dynamic validation based on country code
                                fn ($get) => function ($attribute, $value, $fail) use ($get) {
                                    // Get the country code from the countryStatePath
                                    $countryCode =  geoip(request()->ip())['country_code2']; // Ensure uppercase and fallback to EG

                                    // Define country-specific length rules (total length in E164 format, including +)
                                    $lengthRules = [
                                        // Gulf countries
                                        'AE' => ['min' => 12, 'max' => 12], // UAE: +971501234567
                                        'SA' => ['min' => 13, 'max' => 13], // Saudi Arabia: +966512345678
                                        'KW' => ['min' => 12, 'max' => 12], // Kuwait: +96512345678
                                        'OM' => ['min' => 12, 'max' => 12], // Oman: +96891234567
                                        'QA' => ['min' => 11, 'max' => 11], // Qatar: +9741234567
                                        'BH' => ['min' => 11, 'max' => 11], // Bahrain: +9731234567

                                        // North African countries
                                        'EG' => ['min' => 13, 'max' => 13], // Egypt: +201234567890
                                        'LY' => ['min' => 12, 'max' => 12], // Libya: +218912345678
                                        'MA' => ['min' => 13, 'max' => 13], // Morocco: +212612345678
                                        'TN' => ['min' => 12, 'max' => 12], // Tunisia: +21612345678
                                        'DZ' => ['min' => 13, 'max' => 13], // Algeria: +213612345678

                                        // Western countries
                                        'US' => ['min' => 12, 'max' => 12], // USA: +12025550123
                                        'GB' => ['min' => 13, 'max' => 13], // UK: +447912345678
                                        'CA' => ['min' => 12, 'max' => 12], // Canada: +15195550123
                                        'AU' => ['min' => 12, 'max' => 12], // Australia: +61412345678
                                        'DE' => ['min' => 13, 'max' => 13], // Germany: +4915123456789
                                        'FR' => ['min' => 13, 'max' => 13], // France: +33612345678
                                    ];

                                    // Use rules for the selected country or fallback to Egypt
                                    $rules = $lengthRules[$countryCode] ?? $lengthRules['EG'];

                                    // Combine the dial code and phone number to validate the full E164 number
                                    $fullNumber = $get('phone_dial_code') . $value; // Assuming dial code is stored in phone_dial_code
                                    $length = strlen($fullNumber);

                                    if ($length < $rules['min'] || $length > $rules['max']) {
                                        $fail(__("The phone number must be :length characters for :country.", [
                                            'length' => $rules['min'],
                                            'country' => $countryCode,
                                        ]));
                                    }

                                    // Validate phone number format using libphonenumber
                                    $phoneUtil = PhoneNumberUtil::getInstance();
                                    try {
                                        $phoneNumber = $phoneUtil->parse($fullNumber, $countryCode);
                                        if (!$phoneUtil->isValidNumber($phoneNumber)) {
                                            $fail(__("The phone number is not valid for :country.", ['country' => $countryCode]));
                                        }
                                    } catch (\Libphonenumber\NumberParseException $e) {
                                        $fail(__("The phone number format is invalid."));
                                    }
                                },
                            ])
                            ->label(__('Phone')),

                        Forms\Components\TextInput::make('email')
                            ->columnSpanFull()
                            ->label(__('email'))
                            ->email(), //address

                        Forms\Components\Textarea::make('address')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->label(__('Address')),
                    ]),

                Forms\Components\Section::make(__('Brand Colors'))
                    ->collapsed(true)
                    ->description(__('These colors will be applied throughout the frontend design'))
                    ->schema([
                        Forms\Components\ColorPicker::make('primary_color')
                            ->label(__('Primary Brand Color'))
                            ->nullable()
                            ->helperText(__('Applied to: Primary buttons, headers, main CTAs, and key interactive elements (Hex code)')),

                        Forms\Components\ColorPicker::make('secondary_color')
                            ->label(__('Secondary Brand Color'))
                            ->nullable()
                            ->helperText(__('Applied to: Secondary buttons, accents, borders, and complementary design elements (Hex code)')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('Logos'))
                    ->collapsed(true)
                    ->description(__('Upload logos for different languages'))
                    ->schema([
                        FileUpload::make('logo_en')
                            ->image()
                            ->imageEditor()
                            ->label(__('Logo (English)')),

                        FileUpload::make('logo_ar')
                            ->image()
                            ->imageEditor()
                            ->label(__('Logo (Arabic)')),

                        FileUpload::make('dark_logo_en')
                            ->image()
                            ->imageEditor()
                            ->label(__('Dark Logo (English)')),

                        FileUpload::make('dark_logo_ar')
                            ->image()
                            ->imageEditor()
                            ->label(__('Dark Logo (Arabic)')),
                    ])->columns(2),

                Forms\Components\Section::make(__('Favicon'))
                    ->collapsed(true)
                    ->description(__('Upload website favicon'))
                    ->schema([
                        FileUpload::make('favicon')
                            ->columnSpanFull()
                            ->image()
                            ->imageEditor()
                            ->label(__('Favicon (English & Arabic)'))
                            ->maxSize(5120),
                    ])->columns(1),

                Forms\Components\Section::make(__('social_media'))
                    ->collapsed(true)
                    ->description(__('social_media_description'))
                    ->schema([
                        Forms\Components\TextInput::make('facebook')
                            ->label(__('facebook'))
                            ->url(),

                        Forms\Components\TextInput::make('youtube')
                            ->label(__('youtube'))
                            ->url(),

                        Forms\Components\TextInput::make('instagram')
                            ->label(__('instagram'))
                            ->url(),

                        Forms\Components\TextInput::make('x')
                            ->label(__('x'))
                            ->url(), // Twitter (X)

                        Forms\Components\TextInput::make('snapchat')
                            ->label(__('snapchat'))
                            ->url(),

                        Forms\Components\TextInput::make('tiktok')
                            ->label(__('tiktok'))
                            ->url(),
                    ])->columns(2),

                Forms\Components\Section::make(__('General Settings'))
                    ->collapsed(true)
                    ->description(__('Manage general settings for your store.'))
                    ->schema([
                        Select::make('country_id')
                            ->required()
                            ->label(__('Default Country'))
                            ->options(Country::pluck('name', 'id')),

                        Select::make('currency_id')
                            ->label(__('Currency'))
                            ->relationship('currency', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Currency $record) => "{$record->name} ({$record->symbol})")
                            ->required()
                            ->helperText(__('Select the default currency for transactions.')),

                        TextInput::make('tax_percentage')
                            ->numeric()
                            ->prefix("%")
                            ->label(__('Tax Percentage'))
                            ->required()
                            ->helperText(__('Enter the applicable tax percentage for purchases')),

                        TextInput::make('minimum_stock_level')
                            ->numeric()
                            ->label(__('Minimum Stock Level'))
                            ->required()
                            ->helperText(__('Alert when product quantity is equal or below this value')),

                        TextInput::make('max_cart_quantity')
                            ->numeric()
                            ->label(__('Max Cart Quantity'))
                            ->required()
                            ->helperText(__('This value is the Max Cart Quantity allowed to user to add product at  cart')),

                        Forms\Components\TextInput::make('free_shipping_threshold')
                            ->label(__('settings.free_shipping_threshold.label'))
                            ->helperText(__('settings.free_shipping_threshold.helper'))
                            ->numeric()
                            ->required()
                            ->minValue(0),

                        Forms\Components\Checkbox::make('shipping_type_enabled')
                            ->columnSpanFull()
                            ->label(__('Enable Shipping Types'))
                            ->default(true)
                            ->helperText(__('Enable or disable shipping type selection on checkout')),

                        Forms\Components\Checkbox::make('shipping_locations_enabled')
                            ->columnSpanFull()
                            ->label(__('Enable Shipping Locations'))
                            ->default(true)
                            ->helperText(__('Enable or disable shipping locations selection on checkout.')),
                    ])->columns(3),

                Forms\Components\Section::make(__('Shipping Providers'))
                    ->visible(fn () => Auth::user()->hasRole('super_admin'))
                    ->description(__('Enable/disable available shipping partners and configure their visibility at checkout'))
                    ->collapsed(true)
                    ->schema([
                        Forms\Components\Checkbox::make('enable_jnt')
                            ->label(__('Enable J&T'))
                            ->default(false)
                            ->helperText(__('Enable J&T Express as a shipping option for customers.'))
                            ->visible(fn () => Auth::user()->hasRole('super_admin')),

                        Forms\Components\Checkbox::make('enable_aramex')
                            ->label(__('Enable Aramex'))
                            ->default(false)
                            ->helperText(__('Enable Aramex as a shipping option for customers.'))
                            ->visible(fn () => Auth::user()->hasRole('super_admin')),

                        Forms\Components\Checkbox::make('enable_bosta')
                            ->label(__('Enable Bosta'))
                            ->default(false)
                            ->helperText(__('Enable Bosta as a shipping option for customers.'))
                            ->visible(fn () => Auth::user()->hasRole('super_admin')),
                    ])
                    ->columns(3),

                Section::make(__('Pixel Settings & Google Analysis'))

                    ->collapsed(true)
                    ->schema([
                        Textarea::make('google_pixel')
                            ->label(__('Google Pixel Code'))
                            ->rows(4),

                        Textarea::make('meta_pixel')
                            ->label(__('Meta Pixel Code'))
                            ->rows(4),


                        Textarea::make('google_analytics')
                            ->label(__('Google Analytics Code'))
                            ->rows(4),
                    ])
                    ->columns(1),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
