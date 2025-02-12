<?php

namespace App\Livewire;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Jeffgreco13\FilamentBreezy\Livewire\MyProfileComponent;

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
                TextInput::make('phone')
                    ->unique(ignoreRecord: true)
                    ->label(__('profile.phone'))
                    ->nullable()
                    ->tel(),
                TextArea::make('address')
                    ->label(__('profile.address'))
                    ->nullable(),
            ])
            ->statePath('data');
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
