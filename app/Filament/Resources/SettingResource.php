<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Currency;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\File;

class SettingResource extends Resource
{
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
                Forms\Components\TextInput::make('site_name')
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
                        TextInput::make('site_name')
                            ->label(__('Website Name')),

                        Forms\Components\TextInput::make('phone')
                            ->label(__('phone')),

                        Forms\Components\TextInput::make('email')
                            ->label(__('email'))
                            ->email(),

                    ])->columns(2),

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
                            ->label(__('Favicon (English & Arabic)')),
                    ])->columns(2),

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
                        TextInput::make('tax_percentage')
                            ->numeric()
                            ->prefix("%")
                            ->label(__('Tax Percentage'))
                            ->required()
                            ->helperText(__('Enter the applicable tax percentage for purchases.')),

                        Select::make('currency_id')
                            ->label(__('Currency'))
                            ->relationship('currency', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Currency $record) => "{$record->name} ({$record->symbol})")
                            ->required()
                            ->helperText(__('Select the default currency for transactions.')),

                        Forms\Components\Checkbox::make('shipping_type_enabled')
                            ->label(__('Enable Shipping Types'))
                            ->default(true)
                            ->helperText(__('Enable or disable shipping type selection on checkout.')),

                        Forms\Components\Checkbox::make('shipping_locations_enabled')
                            ->label(__('Enable Shipping Locations'))
                            ->default(true)
                            ->helperText(__('Enable or disable shipping Locations selection on checkout.')),
                    ])->columns(2),
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
