<?php

namespace App\Filament\Resources;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use App\Models\LandingPageNavbarItems;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\LandingPageSettingResource\Pages;

class LandingPageSettingResource extends Resource
{
    protected static ?string $model = LandingPageNavbarItems::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function getNavigationGroup(): ?string
    {
        return __('Settings Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('landing_page_order.settings.landing_page_settings');
    }

    public static function getModelLabel(): string
    {
        return __('landing_page_order.settings.landing_page_setting');
    }

    public static function getPluralLabel(): ?string
    {
        return __('landing_page_order.settings.landing_page_settings');
    }

    public static function getLabel(): ?string
    {
        return __('landing_page_order.settings.landing_page_setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('landing_page_order.settings.landing_page_settings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label(__('landing_page_order.settings.name'))->required()->disabled(),
                TextInput::make('display_name')->label(__('landing_page_order.settings.display_name'))->required(),
                Toggle::make('status')->label(__('landing_page_order.settings.status'))->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('landing_page_order.settings.name'))->sortable(),
                TextColumn::make('display_name')->label(__('landing_page_order.settings.display_name'))->sortable(),
                ToggleColumn::make('status')->label(__('landing_page_order.settings.status'))->sortable(),
            ])
            ->filters([
                TernaryFilter::make('status'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandingPageSettings::route('/'),
            'create' => Pages\CreateLandingPageSetting::route('/create'),
            'edit' => Pages\EditLandingPageSetting::route('/{record}/edit'),
        ];
    }
}
