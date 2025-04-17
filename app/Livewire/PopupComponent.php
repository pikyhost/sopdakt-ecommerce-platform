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
        if (request()->cookie('dont_show_popup')) {
            return;
        }

        $this->popupData = Popup::where('is_active', true)->first();

        if ($this->popupData) {
            if ($this->shouldShowOnCurrentPage()) {
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
                return $this->checkPageGroup($currentPath);
            default:
                return false;
        }
    }

    public function closePopup()
    {
        $this->showPopup = false;

        if ($this->dontShowAgain) {
            cookie()->queue('dont_show_popup', true, 60 * 24 * 30);
        }
    }

    public function render()
    {
        return view('livewire.popup-component');
    }
}
