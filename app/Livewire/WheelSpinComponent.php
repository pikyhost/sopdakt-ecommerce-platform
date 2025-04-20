<?php


namespace App\Livewire;

use App\Models\Wheel;
use App\Models\WheelPrize;
use App\Models\WheelSpin;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WheelSpinComponent extends Component
{
    public Wheel $wheel;
    public ?WheelPrize $wonPrize = null;
    public bool $canSpin = true;

    public function mount(Wheel $wheel)
    {
        $this->wheel = $wheel;

        // تحقق من عدد المرات التي لف فيها المستخدم
        $lastSpin = WheelSpin::where('user_id', Auth::id())
            ->where('wheel_id', $wheel->id)
            ->latest()
            ->first();

        if ($lastSpin && now()->diffInHours($lastSpin->created_at) < $wheel->spins_duration) {
            $this->canSpin = false;
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

        // تحديد الجائزة بناءً على الاحتمال
        $selected = $prizes->randomWeighted(fn ($item) => $item->probability);

        // تسجيل عملية اللف
        $spin = WheelSpin::create([
            'user_id' => Auth::id(),
            'wheel_id' => $this->wheel->id,
            'wheel_prize_id' => $selected->id,
            'is_winner' => true,
        ]);

        $this->wonPrize = $selected;
        $this->canSpin = false;
    }

    public function render()
    {
        return view('livewire.wheel-spin-component');
    }
}
