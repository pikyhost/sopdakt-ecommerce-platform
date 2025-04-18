<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Popup;
use Illuminate\Support\Carbon;

class PopupComponent extends Component
{
    public $showPopup = false;
    public $popupData;
    public $dontShowAgain = false;
    public string $email = '';

    protected $listeners = [
        'show-popup' => 'showPopupWindow',
        'auto-close-popup' => 'closePopup',
    ];

    public function mount()
    {
        if (request()->cookie('dont_show_popup')) {
            return;
        }

        // Get all active popups ordered by priority
        $popups = Popup::where('is_active', true)
            ->orderBy('popup_order')
            ->get();

        foreach ($popups as $popup) {
            $this->popupData = $popup;

            if (!$this->shouldShowOnCurrentPage()) {
                continue;
            }

            // Check last shown time for this popup
            $lastShown = request()->cookie('last_shown_popup_' . $popup->id);
            $minutes = $popup->show_interval_minutes;

            if ($lastShown && now()->diffInMinutes(Carbon::parse($lastShown)) < $minutes) {
                continue;
            }

            // Set the popup to be shown
            $this->popupData = $popup;

            // Dispatch browser event with delay and duration
            $this->dispatch('init-popup', [
                'delay' => $this->popupData->delay_seconds * 1000,
                'duration' => ($this->popupData->duration_seconds ?? 0) * 1000,
            ]);
            break;
        }
    }

    public function showPopupWindow()
    {
        $this->showPopup = true;
    }

    public function closePopup()
    {
        $this->showPopup = false;

        if ($this->dontShowAgain) {
            $days = $this->popupData->dont_show_again_days ?? 30;
            cookie()->queue('dont_show_popup', true, 60 * 24 * $days);
        }

        if ($this->popupData) {
            cookie()->queue('last_shown_popup_' . $this->popupData->id, now()->toDateTimeString(), 60 * 24);
        }
    }

    public function submitEmail()
    {
        if (!$this->popupData->email_needed) {
            return;
        }

        $this->validate([
            'email' => 'required|email',
        ]);

        // Optional: Store to newsletter DB or external service
        // NewsletterSubscription::create([...]);

        session()->flash('message', 'Thanks for joining our newsletter!');
        $this->reset('email');
        $this->showPopup = false;
    }

    protected function shouldShowOnCurrentPage()
    {
        $currentPath = $this->getCurrentPath();
        $pages = collect(json_decode($this->popupData->specific_pages ?? '[]'))->map('trim')->filter();

        return match ($this->popupData->display_rules) {
            'all_pages' => true,
            'specific_pages' => $pages->contains($currentPath),
            'page_group' => $pages->contains(fn($prefix) => str_starts_with($currentPath, $prefix)),
            'all_except_specific' => !$pages->contains($currentPath),
            'all_except_group' => !$pages->contains(fn($prefix) => str_starts_with($currentPath, $prefix)),
            default => false,
        };
    }

    protected function getCurrentPath(): string
    {
        $path = request()->path(); // e.g., "en/products/123"
        $locale = app()->getLocale(); // e.g., "en"

        return preg_replace("#^{$locale}/#", '', $path); // returns "products/123"
    }

    public function render()
    {
        return view('livewire.popup-component');
    }
}
