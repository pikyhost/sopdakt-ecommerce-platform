<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
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

    public static function getModelLabel(): string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Settings');
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
                Forms\Components\Section::make(__('website_name_section'))
                    ->description(__('website_name_description'))
                    ->schema([
                        TextInput::make('value.name.en')
                            ->label(__('website_name_en'))
                            ->required(),

                        TextInput::make('value.name.ar')
                            ->label(__('website_name_ar'))
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make(__('logo_section'))
                    ->description(__('logo_description'))
                    ->schema([
                        FileUpload::make('value.logo.en')
                            ->label(__('logo_en')),

                        FileUpload::make('value.logo.ar')
                            ->label(__('logo_ar')),
                    ])->columns(2),

                Forms\Components\Section::make(__('favicon_section'))
                    ->description(__('favicon_description'))
                    ->schema([
                        FileUpload::make('value.favicon.en')
                            ->label(__('favicon_en')),

                        FileUpload::make('value.favicon.ar')
                            ->label(__('favicon_ar')),
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
