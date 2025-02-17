<?php

namespace App\Filament\Client\Pages\Auth;

use App\Enums\UserRole;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ClientRegister extends BaseRegister
{
    protected static string $view = 'filament.client.pages.auth.register';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getTermsAndConditionsComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getTermsAndConditionsComponent()
    {
        // Get the current locale (ar or en)
        $locale = App::getLocale();

        // Define URLs for Terms & Privacy Policy pages
        $termsUrl = $locale === 'ar' ? '/ar/terms-and-conditions' : '/terms-and-conditions';
        $privacyUrl = $locale === 'ar' ? '/ar/privacy-and-policy' : '/privacy-and-policy';

        // Get the translated string
        $translatedString = __('custom.terms_and_conditions');

        // Replace placeholders {terms} and {privacy} with actual links
        $translatedString = Str::replace(
            ['{terms}', '{privacy}'],
            [
                "<a href=\"$termsUrl\" class=\"text-blue-600 underline hover:text-blue-700 font-medium transition-colors duration-200 ease-in-out\" target=\"_blank\">" . __('custom.terms_link') . "</a>",
                "<a href=\"$privacyUrl\" class=\"text-blue-600 underline hover:text-blue-700 font-medium transition-colors duration-200 ease-in-out\" target=\"_blank\">" . __('custom.privacy_link') . "</a>"
            ],
            $translatedString
        );

        return Checkbox::make('accept')
            ->label(fn () => new HtmlString($translatedString))
            ->required()
            ->dehydrated(false);
    }


    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/register.form.email.label'))
            ->required()
            ->maxLength(255)
            ->email()
            ->rules(['email:rfc,dns'])
            ->unique($this->getUserModel());
    }

    protected function getPhoneFormComponent(): Component
    {
        return PhoneInput::make('phone')
            ->required()
            ->rules([
                'max:20', // Match database column limit
                'unique:users,phone', // Ensure uniqueness in the `users` table
            ])
            ->label(__('profile.phone'))
            ->columnSpanFull();
    }


    protected function handleRegistration(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Call the parent registration logic
        $user = parent::handleRegistration($data);

        $user->assignRole(UserRole::Client->value);

        return $user;
    }

    public function hasLogo(): bool
    {
        return true; // Disable the logo for this specific page
    }
}
