<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wheel;
use App\Models\WheelPrize;
use App\Models\WheelSpin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class WheelSpinPopup extends Component
{
    public $wheel;
    public $prizes = [];
    public $spinning = false;
    public $winnerPrize;
    public $showResult = false;
    public $sessionId;

    public function mount()
    {
        // Set or get session ID for guest tracking
        $this->sessionId = Cookie::get('wheel_session_id');

        if (!$this->sessionId) {
            $this->sessionId = (string) Str::uuid();
            Cookie::queue('wheel_session_id', $this->sessionId, 60 * 24 * 30); // Store for 30 days
        }

        $this->wheel = Wheel::where('is_active', true)
            ->where(function ($q) {
                $now = now();
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) {
                $now = now();
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->latest()->first();

        if ($this->wheel) {
            $this->prizes = $this->wheel->wheelPrizes()->where('is_available', true)->get();
        }
    }

    public function spin()
    {
        if (!$this->wheel || $this->spinning) return;

        // Check if user has exceeded their spins
        $query = WheelSpin::where('wheel_id', $this->wheel->id);
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $this->sessionId);
        }

        $recentSpins = $query->where('created_at', '>=', now()->subHours($this->wheel->spins_duration))->count();

        if ($recentSpins >= $this->wheel->spins_per_user) {
            $this->addError('spin', 'لقد استهلكت كل محاولاتك. جرب لاحقًا!');
            return;
        }

        $this->spinning = true;

        $prize = $this->prizes->flatMap(function ($prize) {
            return collect(array_fill(0, $prize->probability, $prize));
        })->shuffle()->first();

        $this->winnerPrize = $prize;

        // Save spin with session_id or user_id
        WheelSpin::create([
            'user_id' => Auth::id(),
            'session_id' => Auth::check() ? null : $this->sessionId,
            'wheel_id' => $this->wheel->id,
            'wheel_prize_id' => $prize->id ?? null,
            'is_winner' => $prize && $prize->type !== 'none',
        ]);

        $this->dispatch('spin-start', prizeId: $prize->id);

        $this->dispatch('spin-finished', prizeId: $prize->id);
    }

    public function render()
    {
        return view('livewire.wheel-spin-popup');
    }
}
