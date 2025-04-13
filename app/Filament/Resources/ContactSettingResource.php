<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactSettingResource\Pages;
use App\Models\ContactSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactSettingResource extends Resource
{
    protected static ?string $model = ContactSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.Groups.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.Labels.contact_settings');
    }

    public static function getModelLabel(): string
    {
        return __('models.contact_setting.singular');
    }

    public static function getPluralLabel(): ?string
    {
        return __('models.contact_setting.plural');
    }

    public static function getLabel(): ?string
    {
        return __('models.contact_setting.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.contact_setting.plural_model');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label(__('fields.key'))
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('value')
                    ->label(__('fields.value'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->columnSpanFull()
                    ->label(__('fields.key')),
                Tables\Columns\TextColumn::make('value')
                    ->columnSpanFull()
                    ->label(__('fields.value'))
                    ->limit(50),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('actions.edit')),
                Tables\Actions\DeleteAction::make()->label(__('actions.delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label(__('actions.bulk_delete')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContactSettings::route('/'),
        ];
    }
}
