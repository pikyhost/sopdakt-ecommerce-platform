<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Popup;

class PopupComponent extends Component
{
    public $showPopup = false;
    public $popupData;
    public $dontShowAgain = false;

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
                    'delay' => $this->popupData->delay_seconds * 1000
                ]);
            }
        }
    }

    protected function shouldShowOnCurrentPage()
    {
        $currentPath = request()->path();

        switch ($this->popupData->display_rules) {
            case 'all_pages':
                return true;
            case 'specific_pages':
                $pages = explode("\n", $this->popupData->specific_pages);
                return in_array($currentPath, array_map('trim', $pages));
            case 'page_group':
                // Implement your page group logic here
                return $this->checkPageGroup($currentPath);
            default:
                return false;
        }
    }

    public function closePopup()
    {
        $this->showPopup = false;

        if ($this->dontShowAgain) {
            // Set cookie to not show again for 30 days
            cookie()->queue('dont_show_popup', true, 60 * 24 * 30);
        }
    }

    public function render()
    {
        return view('livewire.popup-component');
    }
}
