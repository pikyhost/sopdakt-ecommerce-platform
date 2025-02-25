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

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?int $navigationSort = 1;

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
        return __('Global Settings');
    }

    public static function getPluralLabel(): string
    {
        return __('Settings');
    }

    public static function getLabel(): string
    {
        return __('Settings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Website Information'))
                    ->description(__('Update website name and currency'))
                    ->schema([
                        TextInput::make('site_name_en')
                            ->label(__('Website Name (English)'))
                            ->required(),

                        TextInput::make('site_name_ar')
                            ->label(__('Website Name (Arabic)'))
                            ->required(),

                        Select::make('currency_id')
                            ->label(__('Currency'))
                            ->relationship('currency', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Currency $record) => "{$record->name} ({$record->symbol})")
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make(__('Logos'))
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
                    ->description(__('Upload website favicon'))
                    ->schema([
                        FileUpload::make('favicon_en')
                            ->image()
                            ->imageEditor()
                            ->label(__('Favicon (English)')),

                        FileUpload::make('favicon_ar')
                            ->image()
                            ->imageEditor()
                            ->label(__('Favicon (Arabic)')),
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
