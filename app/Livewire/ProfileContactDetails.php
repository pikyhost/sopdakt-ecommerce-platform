<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use libphonenumber\PhoneNumberUtil;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ProfileContactDetails extends MyProfileComponent implements HasActions, HasForms
{
    protected string $view = 'livewire.profile-contact-details';

    public static $sort = 2;

    public array $only = ['phone', 'preferred_language', 'second_phone', 'desc_for_comment'];

    public array $data;

    public $user;

    public $userClass;

    public function mount()
    {
        $this->user = auth()->user();

        if (!$this->user) {
            abort(403, 'User not authenticated');
        }

        $this->userClass = get_class($this->user);

        // Ensure 'addresses' relationship is eager-loaded to prevent issues
        $this->user->load('addresses');

        $this->form->fill($this->user->only($this->only));
    }


    public function form(Form $form): Form
    {
        return $form
            ->model($this->user) // Bind the form to the user model
            ->schema([
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
                    ->unique(table: 'users', column: 'phone', ignoreRecord: true)
                    ->unique(table: 'users', column: 'second_phone', ignoreRecord: true)
                    ->label(__('Phone')),

                PhoneInput::make('second_phone')
                    ->separateDialCode(true)
                    ->enableIpLookup(true)
                    ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
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
                    ->columnSpanFull()
                    ->unique(table: 'users', column: 'phone', ignoreRecord: true)
                    ->unique(table: 'users', column: 'second_phone', ignoreRecord: true)
                    ->label(__('Second Phone Number')),

                Repeater::make('addresses')
                    ->relationship('addresses', fn () => $this->user->addresses())
                    ->columnSpanFull()
                    ->label(__('Addresses'))
                    ->schema([
                        TextInput::make('address_name')
                            ->columnSpanFull()
                            ->label('Address Name (e.g. Home, Work)')
                            ->required(),

                        Select::make('country_id')
                            ->columnSpanFull()
                            ->label(__('Country'))
                            ->options(Country::pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(function (callable $set, Get $get) {
                                $set('governorate_id', null);
                                $set('city_id', null);
                            }),

                        Select::make('governorate_id')
                            ->columnSpanFull()
                            ->label(__('Governorate'))
                            ->options(function (Get $get) {
                                return Governorate::where('country_id', $get('country_id'))->pluck('name', 'id');
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                $set('city_id', null);
                            }),

                        Select::make('city_id')
                            ->columnSpanFull()
                            ->label(__('City'))
                            ->options(function (Get $get) {
                                return City::where('governorate_id', $get('governorate_id'))->pluck('name', 'id');
                            })
                            ->live()
                            ->placeholder(function (Get $get) {
                                return empty($get('governorate_id')) ? __('Select a governorate first') : 'Select a city';
                            }),

                        TextArea::make('address')
                            ->columnSpanFull()
                            ->label(__('profile.address'))
                            ->nullable(),

                        Checkbox::make('is_primary')
                            ->distinct()
                            ->columnSpanFull()
                            ->label(__('Primary Address'))
                            ->default(false),
                    ])
                    ->columns(2)
                    ->addable()
                    ->deletable()
                    ->reorderable(),

                Textarea::make('desc_for_comment')
                    ->label('Description  for comment (Blogs)'),

                Radio::make('preferred_language')
                    ->label(__('Preferred Language'))
                    ->options([
                        'en' => __('English'),
                        'ar' => __('Arabic'),
                    ])
                    ->formatStateUsing(fn () => auth()->user()?->preferred_language ?? $this->getBrowserPreferredLanguage())
                    ->columns(2),
            ])
            ->statePath('data');
    }

    /**
     * Get the browser's preferred language.
     */
    protected function getBrowserPreferredLanguage(): string
    {
        $preferredLanguages = request()->getPreferredLanguage(['en', 'ar']);
        return $preferredLanguages ?: 'en'; // Default to English if no match found
    }

    public function submit(): void
    {
        $data = collect($this->form->getState())->only($this->only)->all();
        $this->user->update($data);

        Notification::make()
            ->success()
            ->title(__('profile.update_success'))
            ->send();
    }

    public function submitFormAction(): Action
    {
        return Action::make('submit')
            ->label(__('Update'))
            ->submit('submit');
    }
}
