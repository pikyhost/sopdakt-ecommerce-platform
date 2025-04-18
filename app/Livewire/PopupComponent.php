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
    public $allPopups = [];
    public $currentIndex = 0;

    protected $listeners = [
        'show-popup' => 'showPopupWindow',
        'auto-close-popup' => 'closePopup',
        'next-popup' => 'showNextPopup',
    ];

    public function mount()
    {
        if (session()->get('popup_blocked') || request()->cookie('dont_show_popup')) {
            return;
        }

        $this->allPopups = Popup::where('is_active', true)
            ->orderBy('popup_order')
            ->get()
            ->filter(fn ($popup) => $this->isPopupEligible($popup))
            ->values();

        if ($this->allPopups->isNotEmpty()) {
            $this->popupData = $this->allPopups[$this->currentIndex];
            $this->dispatchPopup();
        }
    }

    protected function isPopupEligible($popup): bool
    {
        if (!$this->shouldShowOnCurrentPage($popup)) {
            return false;
        }

        $lastShown = request()->cookie('last_shown_popup_' . $popup->id);
        $minutes = $popup->show_interval_minutes;

        return !$lastShown || now()->diffInMinutes(Carbon::parse($lastShown)) >= $minutes;
    }

    protected function dispatchPopup()
    {
        $this->dispatch('init-popup', [
            'delay' => ($this->popupData->delay_seconds ?? 0) * 1000,
            'duration' => ($this->popupData->duration_seconds ?? 0) * 1000,
        ]);
    }

    protected function shouldShowOnCurrentPage($popup): bool
    {
        $currentPath = $this->getCurrentPath();
        $pages = collect(json_decode($popup->specific_pages ?? '[]'))->map('trim')->filter();

        return match ($popup->display_rules) {
            'all_pages' => true,
            'specific_pages' => $pages->contains($currentPath),
            'page_group' => $pages->contains(fn($prefix) => str_starts_with($currentPath, $prefix)),
            'all_except_specific' => !$pages->contains($currentPath),
            'all_except_group' => !$pages->contains(fn($prefix) => str_starts_with($currentPath, $prefix)),
            default => false,
        };
    }

    public function showPopupWindow()
    {
        $this->showPopup = true;
    }

    public function closePopup()
    {
        $this->showPopup = false;

        // Session block for current visit
        session()->put('popup_blocked', true);

        // Cookie for long-term block
        if ($this->dontShowAgain) {
            $days = $this->popupData->dont_show_again_days ?? 30;
            cookie()->queue('dont_show_popup', true, 60 * 24 * $days);
        }

        // Track last shown time
        if ($this->popupData) {
            cookie()->queue('last_shown_popup_' . $this->popupData->id, now()->toDateTimeString());
        }

        // Move to next popup if available
        $this->showNextPopup();
    }

    public function showNextPopup()
    {
        $this->currentIndex++;

        if (isset($this->allPopups[$this->currentIndex])) {
            $this->popupData = $this->allPopups[$this->currentIndex];
            $this->dispatchPopup();
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

        // Store or process email

        session()->flash('message', 'Thanks for joining our newsletter!');
        $this->reset('email');
        $this->showPopup = false;

        $this->showNextPopup();
    }

    protected function getCurrentPath(): string
    {
        $path = request()->path();
        $locale = app()->getLocale();

        return preg_replace("#^{$locale}/#", '', $path);
    }

    public function render()
    {
        return view('livewire.popup-component');
    }
}
