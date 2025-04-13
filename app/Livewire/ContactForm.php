<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\ContactMessageNotifier;
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
            'user_id' => auth()->id(),
        ]);

        $this->reset(['name', 'email', 'subject', 'message']);
        $this->successMessage = __('contact.success_message');

        ContactMessageNotifier::notify($message);
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
