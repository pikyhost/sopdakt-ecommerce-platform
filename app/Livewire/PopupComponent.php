<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use App\Models\Popup;
use Illuminate\Support\Carbon;
use App\Models\Invitation;
use App\Enums\UserRole;
use App\Mail\TeamInvitationMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class PopupComponent extends Component
{
    public $showPopup = false;
    public $popupData;
    public $dontShowAgain = false;
    public string $email = '';
    public $allPopups = [];
    public $currentIndex = 0;

    protected $listeners = [
        'show-popup' => 'showPopup',
        'close-popup' => 'closePopup',
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
            ->filter(function ($popup) {
                // If it's a join-us popup, show only to guests, not on /register
                if ($popup->is_join_us) {
                    return auth()->guest() && $this->getCurrentPath() !== 'register' && $this->isPopupEligible($popup);
                }

                return $this->isPopupEligible($popup);
            })
            ->values();

        if ($this->allPopups->isNotEmpty()) {
            $this->popupData = $this->allPopups[$this->currentIndex];
            $this->initPopup();
        }
    }


    public function initPopup()
    {
        Log::info('Popup Data Loaded:', [
            'popup_id' => $this->popupData?->id,
            'delay' => $this->popupData?->delay_seconds,
            'duration' => $this->popupData?->duration_seconds,
        ]);

        $this->dispatch('init-popup', [
            'delay' => ($this->popupData->delay_seconds ?? 5) * 1000,
            'duration' => ($this->popupData->duration_seconds ?? 30) * 1000,
        ]);
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

    protected function shouldShowOnCurrentPage($popup): bool
    {
        $currentPath = $this->getCurrentPath();
        $pages = collect(is_array($popup->specific_pages) ? $popup->specific_pages : json_decode($popup->specific_pages ?? '[]', true))
            ->map('trim')
            ->filter();

        return match ($popup->display_rules) {
            'all_pages' => true,
            'specific_pages' => $pages->contains($currentPath),
            'page_group' => $pages->contains(fn($prefix) => str_starts_with($currentPath, $prefix)),
            'all_except_specific' => !$pages->contains($currentPath),
            'all_except_group' => !$pages->contains(fn($prefix) => str_starts_with($currentPath, $prefix)),
            default => false,
        };
    }

    public function showPopup()
    {
        $this->showPopup = true;
    }

    public function closePopup()
    {
        $this->showPopup = false;

        session()->put('popup_blocked', true);

        if ($this->dontShowAgain) {
            $days = $this->popupData->dont_show_again_days ?? 30;
            cookie()->queue('dont_show_popup', true, 60 * 24 * $days);
        }

        if ($this->popupData) {
            cookie()->queue('last_shown_popup_' . $this->popupData->id, now()->toDateTimeString());
        }

        $this->showNextPopup();
    }

    public function showNextPopup()
    {
        $this->currentIndex++;

        if (isset($this->allPopups[$this->currentIndex])) {
            $this->popupData = $this->allPopups[$this->currentIndex];
            $this->initPopup();
        }
    }

    public function submitEmail()
    {
        if (!$this->popupData->email_needed) {
            return;
        }

        $this->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
                Rule::unique('invitations', 'email'),
            ],
        ]);

        // If this popup is a join-us invitation type
        if ($this->popupData->is_join_us) {
            $defaultRoleId = Role::where('name', UserRole::Client->value)->value('id');

            $invitation = Invitation::create([
                'email' => $this->email,
                'role_id' => $defaultRoleId, // ✅ fixed here
            ]);

            Mail::to($invitation->email)->send(new TeamInvitationMail($invitation));

            session()->flash('message', __('notification.invited_success'));
        } else {
            // Regular newsletter flow
            session()->flash('message', __('Thanks for joining our newsletter!'));
        }

        $this->reset('email');
        $this->showPopup = false;

        $this->showNextPopup();
    }


    protected function getCurrentPath(): string
    {
        return trim(preg_replace('/^([a-z]{2})\//', '', request()->path()), '/');
    }

    public function render()
    {
        return view('livewire.popup-component');
    }
}
