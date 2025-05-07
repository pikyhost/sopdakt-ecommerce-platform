<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\ContactMessageResource\Pages\ManageContactMessages;
use App\Filament\Client\Resources\ContactMessageResource\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use libphonenumber\PhoneNumberUtil;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

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
                    ->separateDialCode(true)
                    ->enableIpLookup(true)
                    ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
                    ->countryStatePath('phone_country') // Bind country code to a state path
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
            ->schema([
                Components\Section::make([
                    Components\TextEntry::make('name')
                        ->label(__('fields.name')),

                    PhoneEntry::make('phone')
                        ->label(__('Phone number')),

                    Components\TextEntry::make('email')
                        ->label(__('fields.email')),

                    Components\TextEntry::make('subject')
                        ->label(__('fields.subject')),

                    Components\TextEntry::make('message')
                        ->label(__('fields.message')),
                ])
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
