<?php

namespace App\Livewire;

use App\Models\Prize;
use App\Models\Spin;
use App\Models\Wheel;
use App\Models\WheelSpin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WheelSpinComponent extends Component
{
    public Wheel $wheel;
    public $wonPrize = null;
    public $canSpin = true;
    public $cooldownTime = null;
    public $userSpinCount = 0;

    public function mount(Wheel $wheel)
    {
        $this->wheel = $wheel;

        if (Auth::check()) {
            $user = Auth::user();
            $this->userSpinCount = WheelSpin::where('user_id', $user->id)
                ->where('wheel_id', $wheel->id)
                ->count();

            if ($wheel->spins_per_user === 1 && $this->userSpinCount >= 1) {
                $this->canSpin = false;
            } elseif ($wheel->spins_per_user > 1) {
                $lastSpin = WheelSpin::where('user_id', $user->id)
                    ->where('wheel_id', $wheel->id)
                    ->latest()
                    ->first();

                if ($lastSpin) {
                    $nextAvailableSpin = $lastSpin->created_at->addHours($wheel->spins_duration);
                    if (now()->lt($nextAvailableSpin)) {
                        $this->canSpin = false;
                        $this->cooldownTime = $nextAvailableSpin;
                    }
                }
            }
        } else {
            // Guest spin control
            $guestSpinTime = session("wheel_spin.{$wheel->id}");

            if ($guestSpinTime) {
                if ($wheel->spins_per_user === 1) {
                    $this->canSpin = false;
                } elseif ($wheel->spins_per_user > 1) {
                    $nextAvailableSpin = Carbon::parse($guestSpinTime)->addHours($wheel->spins_duration);
                    if (now()->lt($nextAvailableSpin)) {
                        $this->canSpin = false;
                        $this->cooldownTime = $nextAvailableSpin;
                    }
                }
            }
        }
    }

    public function spin()
    {
        if (!$this->canSpin) {
            session()->flash('error', 'لا يمكنك التدوير حالياً.');
            return;
        }

        $availablePrizes = $this->wheel->prizes()->where('is_available', true)->get();

        if ($availablePrizes->isEmpty()) {
            session()->flash('error', 'لا توجد جوائز متاحة حالياً.');
            return;
        }

        $this->wonPrize = $availablePrizes->random();
        $this->wonPrize->is_available = false;
        $this->wonPrize->save();

        if (Auth::check()) {
            WheelSpin::create([
                'user_id' => Auth::id(),
                'wheel_id' => $this->wheel->id,
                'prize_id' => $this->wonPrize->id,
            ]);
        } else {
            // Store guest spin time in session
            session(["wheel_spin.{$this->wheel->id}" => now()]);
        }

        $this->canSpin = false;

        if ($this->wheel->spins_per_user === 1) {
            $this->userSpinCount++;
        } else {
            $this->cooldownTime = now()->addHours($this->wheel->spins_duration);
        }
    }

    public function render()
    {
        return view('livewire.wheel-spin-component');
    }
}
