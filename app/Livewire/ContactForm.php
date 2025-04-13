<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ContactMessage;
use Filament\Notifications\Notification;

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

    public function submit()
    {
        $this->validate();

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip_address' => request()->ip(),
        ]);

        // Reset form fields
        $this->reset(['name', 'email', 'subject', 'message']);

        // Set success message
        $this->successMessage = __('Your message has been sent successfully!');

        // Send notification filament database here
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
