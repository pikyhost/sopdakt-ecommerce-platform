<?php

namespace App\Livewire;

use App\Enums\UserRole;
use App\Filament\Pages\Dashboard;
use App\Models\Invitation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AcceptInvitation extends SimplePage
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static string $view = 'livewire.accept-invitation';

    public int $invitation;

    private Invitation $invitationModel;

    public ?array $data = [];

    public function mount(): void
    {
        $this->invitationModel = Invitation::findOrFail($this->invitation);

        $roleIds = $this->invitationModel->roles ?? [];

        $roles = \Spatie\Permission\Models\Role::whereIn('id', $roleIds)->pluck('name')->toArray();

        if (! in_array(UserRole::PanelUser->value, $roles)) {
            $roles[] = 'Panel User';
        }

        $roleNames = $this->formatRoles($roles);

        $this->form->fill([
            'email' => $this->invitationModel->email,
            'role' => $roleNames,
        ]);
    }

    private function formatRoles(array $roles): string
    {
        if (count($roles) === 1) {
            return $roles[0];
        }

        return implode(', ', $roles);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-panels::pages/auth/register.form.name.label'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('role')
                    ->formatStateUsing(fn ($state) => Str::headline($state))
                    ->label('Roles')
                    ->disabled(),
                TextInput::make('email')
                    ->label(__('filament-panels::pages/auth/register.form.email.label'))
                    ->disabled(),
                TextInput::make('password')
                    ->revealable()
                    ->label(__('filament-panels::pages/auth/register.form.password.label'))
                    ->password()
                    ->required()
                    ->rule(Password::default())
                    ->same('passwordConfirmation')
                    ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute')),
                TextInput::make('passwordConfirmation')
                    ->revealable()
                    ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
                    ->password()
                    ->required()
                    ->dehydrated(false),
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $this->invitationModel = Invitation::find($this->invitation);

        $user = User::create([
            'name' => $this->form->getState()['name'],
            'password' => bcrypt($this->form->getState()['password']),
            'email' => $this->invitationModel->email,
        ]);

        $roleIds = $this->invitationModel->roles ?? [];

        $roles = \Spatie\Permission\Models\Role::whereIn('id', $roleIds)->pluck('name')->toArray();

        if (! in_array(UserRole::PanelUser->value, $roles)) {
            $roles[] = UserRole::PanelUser->value;
        }

        foreach ($roles as $role) {
            if (\Spatie\Permission\Models\Role::findByName($role, 'web')) {
                $user->assignRole($role);
            } else {
                logger("Role '{$role}' does not exist.");
            }
        }

        auth()->login($user);
        $this->invitationModel->delete();

        $this->redirect(Dashboard::getUrl());
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('register')
                ->label(__('filament-panels::pages/auth/register.form.actions.register.label'))
                ->submit('create'),
        ];
    }

    public function getHeading(): string
    {
        return 'Accept Invitation';
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function getSubHeading(): string
    {
        return 'Create your account and join now!';
    }
}
