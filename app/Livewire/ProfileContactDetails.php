<?php

namespace App\Livewire;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ProfileContactDetails extends MyProfileComponent
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
                    ->unique(ignoreRecord: true)
                    ->label(__('phone'))
                    ->nullable(),

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
}
