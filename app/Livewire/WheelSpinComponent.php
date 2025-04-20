<?php

namespace App\Livewire;

use App\Models\Contact;
use App\Models\Wheel;
use App\Models\WheelPrize;
use App\Models\WheelSpin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Livewire\Component;

class WheelSpinComponent extends Component
{
    public Wheel $wheel;
    public ?WheelPrize $wonPrize = null;
    public bool $canSpin = true;
    public bool $hasWonBefore = false;
    public bool $hasReachedSpinLimit = false;
    public int $remainingSpins = 0;
    public bool $showPopup = false;

    public function mount()
    {
        $this->wheel = Wheel::where('is_active', true)->first();

        if (!$this->wheel) {
            return;
        }

        if (!$this->shouldShowOnCurrentPage()) {
            return;
        }

        $this->showPopup = $this->isPopupEligible();

        if (!$this->showPopup) {
            return;
        }

        if (Auth::check()) {
            $userId = Auth::id();

            $spins = WheelSpin::where('user_id', $userId)
                ->where('wheel_id', $this->wheel->id)
                ->where('created_at', '>=', now()->subHours($this->wheel->spins_duration))
                ->get();

            $this->hasWonBefore = $spins->contains('is_winner', true);
            $this->remainingSpins = max(0, $this->wheel->spins_per_user - $spins->count());
            $this->canSpin = $this->remainingSpins > 0 && !$this->hasWonBefore;
            $this->hasReachedSpinLimit = $this->remainingSpins <= 0;
        } else {
            $sessionId = Cookie::get('guest_session_id');

            if (!$sessionId) {
                $sessionId = (string) Str::uuid();
                Cookie::queue('guest_session_id', $sessionId, 60 * 24 * 30); // 30 days
            }

            $spins = WheelSpin::where('session_id', $sessionId)
                ->where('wheel_id', $this->wheel->id)
                ->where('created_at', '>=', now()->subHours($this->wheel->spins_duration))
                ->get();

            $this->hasWonBefore = $spins->contains('is_winner', true);
            $this->remainingSpins = max(0, $this->wheel->spins_per_user - $spins->count());
            $this->canSpin = $this->remainingSpins > 0 && !$this->hasWonBefore;
            $this->hasReachedSpinLimit = $this->remainingSpins <= 0;
        }
    }

    public function spin()
    {
        if (!$this->canSpin) {
            session()->flash('error', 'لا يمكنك اللف الآن. يرجى الانتظار.');
            return;
        }

        $prizes = $this->wheel->prizes()->where('is_available', true)->get();

        if ($prizes->isEmpty()) {
            session()->flash('error', 'لا توجد جوائز متاحة حالياً.');
            return;
        }

        $selected = $this->getRandomPrize($prizes);

        if (!$selected) {
            session()->flash('error', 'حدث خطأ أثناء اختيار الجائزة.');
            return;
        }

        $spinData = [
            'wheel_id' => $this->wheel->id,
            'wheel_prize_id' => $selected->id,
            'is_winner' => true,
        ];

        if (Auth::check()) {
            $spinData['user_id'] = Auth::id();
        } else {
            $sessionId = Cookie::get('guest_session_id');

            if (!$sessionId) {
                $sessionId = (string) Str::uuid();
                Cookie::queue('guest_session_id', $sessionId, 60 * 24 * 30); // 30 days
            }

            $spinData['session_id'] = $sessionId;

            $contact = Contact::firstOrCreate(
                ['session_id' => $sessionId],
                ['created_at' => now(), 'updated_at' => now()]
            );

            $spinData['contact_id'] = $contact->id;
        }

        WheelSpin::create($spinData);

        $this->wonPrize = $selected;
        $this->canSpin = false;
        $this->hasWonBefore = true;
        $this->remainingSpins--;
    }

    private function getRandomPrize($prizes)
    {
        $totalWeight = $prizes->sum('probability');

        if ($totalWeight <= 0) {
            return null;
        }

        $random = rand(1, $totalWeight);
        $current = 0;

        foreach ($prizes as $prize) {
            $current += $prize->probability;
            if ($random <= $current) {
                return $prize;
            }
        }

        return null;
    }

    private function isPopupEligible(): bool
    {
        $lastShown = request()->cookie('last_shown_wheel_' . $this->wheel->id);
        $minutes = 60; // Show interval in minutes

        return !$lastShown || now()->diffInMinutes(\Carbon\Carbon::parse($lastShown)) >= $minutes;
    }

    private function shouldShowOnCurrentPage(): bool
    {
        $currentPath = request()->path();
        $pages = collect(is_array($this->wheel->specific_pages) ? $this->wheel->specific_pages : json_decode($this->wheel->specific_pages ?? '[]', true))
            ->map('trim')
            ->filter();

        return match ($this->wheel->display_rules) {
            'all_pages' => true,
            'specific_pages' => $pages->contains($currentPath),
            'page_group' => $pages->contains(fn($prefix) => str_starts_with($currentPath, $prefix)),
            'all_except_specific' => !$pages->contains($currentPath),
            'all_except_group' => !$pages->contains(fn($prefix) => str_starts_with($currentPath, $prefix)),
            default => false,
        };
    }

    public function render()
    {
        return view('livewire.wheel-spin-component');
    }
}
