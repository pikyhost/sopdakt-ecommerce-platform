<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\ContactMessageResource\Pages;
use App\Helpers\GeneralHelper;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.Groups.communication');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.Labels.contact_messages');
    }

    public static function getModelLabel(): string
    {
        return __('models.contact_message.singular');
    }

    public static function getPluralLabel(): ?string
    {
        return __('models.contact_message.plural');
    }

    public static function getLabel(): ?string
    {
        return __('models.contact_message.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('models.contact_message.plural_model');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('fields.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject')
                    ->label(__('fields.subject'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('message')
                    ->label(__('fields.message'))
                    ->required(),
                Forms\Components\TextInput::make('ip_address')
                    ->label(__('fields.ip_address'))
                    ->maxLength(45),
                Forms\Components\Placeholder::make('sender_country')
                    ->label(__('fields.sender_country'))
                    ->content(function () {
                        $countryId = GeneralHelper::getCountryId();
                        $countryName = \App\Models\Country::find($countryId)?->name;

                        return $countryName
                            ? __('messages.sender_from') . ' ' . $countryName
                            : __('messages.country_unknown');
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('fields.name')),
                Tables\Columns\TextColumn::make('email')->label(__('fields.email')),
                Tables\Columns\TextColumn::make('subject')->label(__('fields.subject')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('fields.created_at'))
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('actions.view')),
                Tables\Actions\DeleteAction::make()->label(__('actions.delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label(__('actions.bulk_delete')),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\TextEntry::make('name')
                    ->label(__('fields.name')),

                Components\TextEntry::make('email')
                    ->label(__('fields.email')),

                Components\TextEntry::make('subject')
                    ->label(__('fields.subject')),

                Components\TextEntry::make('message')
                    ->label(__('fields.message')),

                Components\TextEntry::make('ip_address')
                    ->label(__('fields.ip_address')),

                Components\TextEntry::make('sender_country')
                    ->label(__('fields.sender_country'))
                    ->state(function ($record) {
                        $countryId = GeneralHelper::getCountryId(); // or from $record if stored
                        $countryName = \App\Models\Country::find($countryId)?->name;

                        return $countryName
                            ? __('messages.sender_from') . ' ' . $countryName
                            : __('messages.country_unknown');
                    }),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
