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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;

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
                    ->default(fn() => auth()->user()->name)
                    ->columnSpanFull()
                    ->label(__('fields.name'))
                    ->required()
                    ->maxLength(255),
                PhoneInput::make('phone')
                    ->columnSpanFull()
                    ->default(fn() => auth()->user()->phone)
                    ->enableIpLookup(true) // Enable IP-based country detection
                    ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
                    ->required()
                    ->rules([
                        'max:20', // Match database column limit
                    ])
                    ->label(__('Phone Number')),
                Forms\Components\TextInput::make('email')
                    ->default(fn() => auth()->user()->email)
                    ->columnSpanFull()
                    ->label(__('fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('subject')
                    ->columnSpanFull()
                    ->label(__('fields.subject'))
                    ->maxLength(255),
                Forms\Components\Textarea::make('message')
                    ->label(__('fields.message'))
                    ->columnSpanFull()
                    ->required(),
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
            ->columns(2)
            ->schema([
                Components\Section::make()
                    ->schema([
                        Components\TextEntry::make('user.name')
                            ->badge()
                            ->color('success')
                            ->url(function ($record) {
                                return url('/admin/users/'.$record->user_id);
                            })
                            ->label(__('From Account')),

                        Components\TextEntry::make('name')
                            ->label(__('fields.name')),

                        PhoneEntry::make('phone')
                            ->label(__('Phone number')),

                        Components\TextEntry::make('email')
                            ->label(__('fields.email')),

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
                    ])->columns(2)
                    ->heading(__('Sender Information')),

                Components\Section::make()
                    ->schema([
                        Components\TextEntry::make('subject')
                            ->label(__('fields.subject'))
                            ->columnSpanFull(),

                        Components\TextEntry::make('message')
                            ->label(__('fields.message'))
                            ->columnSpanFull(),
                    ])
                    ->heading(__('Message Details'))
                    ->columns(2),
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
