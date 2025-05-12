<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlockedPhoneNumberResource\Pages;
use App\Models\BlockedPhoneNumber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use libphonenumber\PhoneNumberUtil;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class BlockedPhoneNumberResource extends Resource
{
    use Translatable;

    protected static ?string $model = BlockedPhoneNumber::class;

    protected static ?string $navigationIcon = 'heroicon-o-no-symbol';

    public static function getNavigationLabel(): string
    {
        return __('blocked_phones.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('blocked_phones.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('blocked_phones.plural');
    }

    public static function getPluralLabel(): ?string
    {
        return __('blocked_phones.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                PhoneInput::make('phone_number')
                    ->rules([
                        'numeric',
                        // Dynamic validation based on country code
                        fn ($get) => function ($attribute, $value, $fail) use ($get) {
                            // Get the country code from the countryStatePath
                            $countryCode =  geoip(request()->ip())['country_code2'] ?? 'US'; // Ensure uppercase and fallback to EG

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
                    ->columnSpanFull()
                    ->enableIpLookup(true) // Enable IP-based country detection
                    ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
                    ->label(__('blocked_phones.phone_number'))
                    ->separateDialCode(true) // Shows flag and +20 separately
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('reason')
                    ->columnSpanFull()
                    ->label(__('Reason')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(false)
            ->columns([
                Tables\Columns\TextColumn::make('phone_number')
                    ->copyable()
                    ->label(__('blocked_phones.phone_number'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('reason')
                    ->label(__('blocked_phones.note'))
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    })
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('blocked_phones.created_at'))
                    ->formatStateUsing(fn ($state) => $state?->timezone('Africa/Cairo')->toDateTimeString())
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBlockedPhoneNumbers::route('/'),
        ];
    }
}
