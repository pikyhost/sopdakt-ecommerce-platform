<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ProfileContactDetails extends MyProfileComponent implements HasActions, HasForms
{
    protected string $view = 'livewire.profile-contact-details';

    public static $sort = 2;

    public array $only = ['phone', 'address'];

    public array $data;

    public $user;

    public $userClass;

    public function mount()
    {
        $this->user = auth()->user();
        $this->userClass = get_class($this->user);

        $this->form->fill($this->user->only($this->only));
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                PhoneInput::make('phone')
                    ->enableIpLookup(true) // Enable IP-based country detection
                    ->initialCountry(fn () => geoip(request()->ip())['country_code2'] ?? 'US')
                    ->nullable()
                    ->rules([
                        'max:20', // Match database column limit
                        'unique:users,phone', // Ensure uniqueness in the `users` table
                    ])
                    ->label(__('profile.phone'))
                    ->columnSpanFull(),
                
                Select::make('country_id')
                    ->required()
                    ->label(__('Country'))
                    ->options(Country::pluck('name', 'id'))
                    ->live()
                    ->afterStateUpdated(function (callable $set, Get $get) {
                        $set('governorate_id', null);
                        $set('city_id', null);
                    }),

                Select::make('governorate_id')
                    ->required()
                    ->label(__('Governorate'))
                    ->options(function (Get $get) {
                        return Governorate::where('country_id', $get('country_id'))->pluck('name', 'id');
                    })
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $set('city_id', null);
                    }),

                Select::make('city_id')
                    ->label(__('City'))
                    ->options(function (Get $get) {
                        return City::where('governorate_id', $get('governorate_id'))->pluck('name', 'id');
                    })
                    ->live()
                    ->placeholder(function (Get $get) {
                        return empty($get('governorate_id')) ? __('Select a governorate first') : 'Select a city';
                    }),

                TextArea::make('address')
                    ->label(__('profile.address'))
                    ->nullable(),

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
