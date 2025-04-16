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
use Illuminate\Validation\Rule;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ProfileContactDetails extends MyProfileComponent implements HasActions, HasForms
{
    protected string $view = 'livewire.profile-contact-details';

    public static $sort = 2;

    public array $only = ['phone', 'preferred_language', 'second_phone'];

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
                    ->separateDialCode(true) // Shows flag and +20 separately
                    ->enableIpLookup(true) // Enable IP-based country detection
                    ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
                    ->nullable()
                    ->rules([
                        'max:20', // Match database column limit
                        Rule::unique('users', 'phone')->ignore(auth()->id()), // Ignore the current user in uniqueness check
                    ])
                    ->label(__('profile.phone'))
                    ->columnSpanFull(),

                PhoneInput::make('second_phone')
                    ->different('phone')
                    ->separateDialCode(true) // Shows flag and +20 separately
                    ->enableIpLookup(true) // Enable IP-based country detection
                    ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
                    ->nullable()
                    ->rules([
                        'max:20',
                        Rule::unique('users')->where(function ($query) {
                            $query->where('phone', request('phone'))
                                ->orWhere('second_phone', request('phone'));
                        }),
                    ])
                    ->label(__('Secondary Phone'))
                    ->columnSpanFull(),

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

                Radio::make('preferred_language')
                    ->label(__('Preferred Language'))
                    ->options([
                        'en' => __('English'),
                        'ar' => __('Arabic'),
                    ])
                    ->formatStateUsing(fn () => auth()->user()?->preferred_language ?? $this->getBrowserPreferredLanguage())
                    ->columns(2)

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
