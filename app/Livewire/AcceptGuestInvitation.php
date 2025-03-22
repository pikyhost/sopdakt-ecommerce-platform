<?php

namespace App\Livewire;

use App\Models\Invitation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\SimplePage;
use Illuminate\Validation\Rules\Password;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class AcceptGuestInvitation extends SimplePage
{
    use InteractsWithForms;

    protected static string $view = 'livewire.accept-guest-invitation';

    public int $invitation;

    private Invitation $invitationModel;

    public ?array $data = [];

    public function mount(): void
    {
        $this->invitationModel = Invitation::findOrFail($this->invitation);

        $this->form->fill([
            'email' => $this->invitationModel->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),

                TextInput::make('email')
                    ->label(__('Email'))
                    ->disabled(),

                PhoneInput::make('phone')
                    ->enableIpLookup(true)
                    ->required()
                    ->rules(['max:20', 'unique:users,phone'])
                    ->label(__('Phone')),

                TextInput::make('password')
                    ->label(__('Password'))
                    ->password()
                    ->required()
                    ->rule(Password::default()),

                Radio::make('preferred_language')
                    ->label(__('Preferred Language'))
                    ->options([
                        'en' => __('English'),
                        'ar' => __('Arabic'),
                    ])
                    ->default(fn () => request()->getPreferredLanguage(['en', 'ar']) ?: 'en')
                    ->columns(2)
                    ->required(),

                Checkbox::make('accept_terms')
                    ->label(fn () => new \Illuminate\Support\HtmlString(
                        __('I accept the <a href="/terms" target="_blank">Terms & Conditions</a>')
                    ))
                    ->required(),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $this->invitationModel = Invitation::findOrFail($this->invitation);

        $user = User::create([
            'name' => $this->form->getState()['name'],
            'phone' => $this->form->getState()['phone'],
            'preferred_language' => $this->form->getState()['preferred_language'],
            'password' => bcrypt($this->form->getState()['password']),
            'email' => $this->invitationModel->email,
        ]);

        auth()->login($user);
        $this->invitationModel->delete();

        $this->redirect('/guest/dashboard');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('register')
                ->label(__('Register'))
                ->submit('create'),
        ];
    }

    public function getHeading(): string
    {
        return __('Accept Guest Invitation');
    }
}
