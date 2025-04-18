<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Popup;

class PopupComponent extends Component
{
    public $showPopup = false;
    public $popupData;
    public $dontShowAgain = false;
    public string $email = '';

    public function mount()
    {
        // Check if user has opted not to see the popup
        if (request()->cookie('dont_show_popup')) {
            return;
        }

        // Get active popup
        $this->popupData = Popup::where('is_active', true)->first();

        if ($this->popupData) {
            // Check display rules
            if ($this->shouldShowOnCurrentPage()) {
                // Show popup after delay
                $this->dispatch('init-popup', [
                    'delay' => $this->popupData->delay_seconds * 1000,
                    'duration' => ($this->popupData->duration_seconds ?? 0) * 1000,
                ]);
            }
        }
    }

    public function submitEmail()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        // Handle  subscription logic here...
        // NewsletterSubscription::create([...]);

        session()->flash('message', 'Thanks for joining our newsletter!');
        $this->reset('email');
        $this->showPopup = false;
    }

    protected function shouldShowOnCurrentPage()
    {
        $currentPath = $this->getCurrentPath();

        $rules = $this->popupData->display_rules;
        $pages = collect($this->popupData->specific_pages ?? [])->pluck('value')->map('trim')->filter();

        return match ($rules) {
            'all_pages' => true,
            'specific_pages' => $pages->contains($currentPath),
            'page_group' => $pages->contains(fn($prefix) => str_starts_with($currentPath, $prefix)),
            default => false,
        };
    }

    protected function getCurrentPath()
    {
        $path = request()->path(); // مثلاً: en/products/123
        $locale = app()->getLocale(); // مثلاً: en

        return preg_replace("#^{$locale}/#", '', $path); // يعيد: products/123
    }


    public function closePopup()
    {
        $this->showPopup = false;

        if ($this->dontShowAgain) {
            $days = $this->popupData->dont_show_again_days ?? 30;
            cookie()->queue('dont_show_popup', true, 60 * 24 * $days);
        }

    }

    public function render()
    {
        return view('livewire.popup-component');
    }
}
