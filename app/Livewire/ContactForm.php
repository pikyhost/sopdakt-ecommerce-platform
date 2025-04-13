<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

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

        $message = ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip_address' => request()->ip(),
            'user_id' => auth()->id(), // add this line
        ]);


        $this->reset(['name', 'email', 'subject', 'message']);
        $this->successMessage = __('contact.success_message');

        $adminUsers = User::role(['admin', 'super_admin'])->get();

        // Determine if sender is admin or super admin
        $isAdminSender = auth()->check() && auth()->user()->hasAnyRole(['admin', 'super_admin']);

// Get all admins and super admins
        $adminUsers = User::role(['admin', 'super_admin'])->get();

// Send notification to each admin with their preferred locale
        foreach ($adminUsers as $admin) {
            App::setLocale($admin->locale ?? config('app.locale')); // Fallback to default locale

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
                ->sendToDatabase($admin);
        }

// If the sender is a client (not admin), notify them too
        if (auth()->check() && ! $isAdminSender) {
            App::setLocale(auth()->user()->locale ?? config('app.locale'));

            Notification::make()
                ->title(__('contact_message_notification.user.title'))
                ->success()
                ->body(__('contact_message_notification.user.body'))
                ->actions([
                    Action::make('view')
                        ->label(__('contact_message_notification.user.view_button'))
                        ->url(route('filament.client.resources.contact-messages.view', ['record' => $message->id]))
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
