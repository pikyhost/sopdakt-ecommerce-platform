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

    protected static ?string $navigationLabel = 'Landing Page Setting';

    public static function getNavigationGroup(): ?string
    {
        return __('Settings Management'); //Products Management
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Name')->required(),
                TextInput::make('display_name')->label('Display Name')->required(),
                Toggle::make('status')->label('Status')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Section Name')->sortable(),
                TextColumn::make('display_name')->label('Display Name')->sortable(),
                ToggleColumn::make('status')->label('Status')->sortable(),
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
