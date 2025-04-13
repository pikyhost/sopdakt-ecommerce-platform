<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;

class ContactForm extends Component
{
    public $name;
    public $email;
    public $subject;
    public $message;
    public $successMessage = null;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'subject' => 'nullable|string|max:255',
        'message' => 'required|string|min:10',
    ];

    public function mount()
    {
        if (auth()->check()) {
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    public function submit()
    {
        $this->validate();

        // Save message
        $message = ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip_address' => request()->ip(),
        ]);

        $this->reset(['name', 'email', 'subject', 'message']);
        $this->successMessage = __('contact.success_message');

        // Get admin and super admin users
        $adminUsers = User::role(['admin', 'super_admin'])->get();

        // Admin notification
        Notification::make()
            ->title(__('contact_message_notification.admin.title'))
            ->success()
            ->body(__('contact_message_notification.admin.body', ['name' => $message->name]))
            ->actions([
                Action::make('view')
                    ->label(__('contact_message_notification.admin.view_button'))
                    ->url(route('filament.admin.resources.contact-messages.view', ['record' => $message->id]))
                    ->markAsRead(),
            ])
            ->sendToDatabase($adminUsers);

        // User notification (only if authenticated)
        if (auth()->check()) {
            Notification::make()
                ->title(__('contact_message_notification.user.title'))
                ->success()
                ->body(__('contact_message_notification.user.body'))
                ->actions([
                    Action::make('view')
                        ->label(__('contact_message_notification.user.view_button'))
                        ->url(route('client.contact-messages.show', ['id' => $message->id]))
                        ->markAsRead(),
                ])
                ->sendToDatabase(auth()->user());
        }
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
