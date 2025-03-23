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
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Validation\Rules\Password;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class AcceptGuestInvitation extends SimplePage
{
    use InteractsWithFormActions, InteractsWithForms;

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
                TextInput::make('password')
                    ->label(__('Password'))
                    ->password()
                    ->required()
                    ->rule(Password::default()),

                $this->getPreferredLanguageFormComponent(),

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

    public function getHeading(): string
    {
        return __('Accept Guest Invitation');
    }

    public function hasLogo(): bool
    {
        return true;
    }

    public function getSubHeading(): string
    {
        return __('Create your account and join now!');
    }

    protected function getPreferredLanguageFormComponent()
    {
        return Radio::make('preferred_language')
            ->label(__('Preferred Language'))
            ->options([
                'en' => __('English'),
                'ar' => __('Arabic'),
            ])
            ->default(fn () => $this->getBrowserPreferredLanguage())
            ->columns(2)
            ->required();
    }

    /**
     * Get the browser's preferred language.
     */
    protected function getBrowserPreferredLanguage(): string
    {
        $preferredLanguages = request()->getPreferredLanguage(['en', 'ar']);
        return $preferredLanguages ?: 'en'; // Default to English if no match found
    }
}
