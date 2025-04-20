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
    public bool $isSpinning = false;
    public int $rotation = 0;
    public int $finalRotation = 0;
    public array $wheelSegments = [];
    public string $spinMessage = '';

    public function mount(Wheel $wheel)
    {
        $this->wheel = $wheel->load('prizes');
        $this->prepareWheelSegments();
        $this->checkSpinAvailability();
    }

    protected function prepareWheelSegments()
    {
        $this->wheelSegments = $this->wheel->prizes
            ->where('is_available', true)
            ->map(function ($prize, $index) {
                return [
                    'id' => $prize->id,
                    'name' => $prize->name,
                    'color' => $this->getSegmentColor($index),
                    'textColor' => $this->getTextColor($index),
                    'angle' => 360 / $this->wheel->prizes->count(),
                ];
            })->toArray();
    }

    protected function checkSpinAvailability()
    {
        $lastSpin = WheelSpin::where('user_id', Auth::id())
            ->where('wheel_id', $this->wheel->id)
            ->latest()
            ->first();

        if ($lastSpin && now()->diffInHours($lastSpin->created_at) < $this->wheel->spins_duration) {
            $this->canSpin = false;
            $nextSpinTime = $lastSpin->created_at->addHours($this->wheel->spins_duration);
            $this->spinMessage = __('Next spin available at :time', ['time' => $nextSpinTime->format('h:i A')]);
        } else {
            $this->spinMessage = __('You have a spin available!');
        }
    }

    public function spin()
    {
        if (!$this->canSpin || $this->isSpinning) {
            return;
        }

        $this->isSpinning = true;
        $availablePrizes = $this->wheel->prizes->where('is_available', true);

        if ($availablePrizes->isEmpty()) {
            $this->isSpinning = false;
            $this->dispatch('notify', message: __('No prizes available currently.'), type: 'error');
            return;
        }

        $selectedPrize = $this->getRandomPrize($availablePrizes);

        if (!$selectedPrize) {
            $this->isSpinning = false;
            $this->dispatch('notify', message: __('Error selecting prize.'), type: 'error');
            return;
        }

        // Calculate final rotation (5 full rotations + segment position)
        $segmentCount = count($this->wheelSegments);
        $segmentAngle = 360 / $segmentCount;
        $prizeIndex = array_search($selectedPrize->id, array_column($this->wheelSegments, 'id'));
        $this->finalRotation = 1800 + (360 - ($prizeIndex * $segmentAngle + $segmentAngle / 2));

        // Record the spin
        WheelSpin::create([
            'user_id' => Auth::id(),
            'wheel_id' => $this->wheel->id,
            'wheel_prize_id' => $selectedPrize->id,
            'is_winner' => true,
        ]);

        // Dispatch event when spinning completes
        $this->dispatch('spin-complete', prizeId: $selectedPrize->id);
    }

    private function getRandomPrize($prizes)
    {
        $totalWeight = $prizes->sum('probability');
        if ($totalWeight <= 0) return null;

        $random = rand(1, $totalWeight);
        $current = 0;

        foreach ($prizes as $prize) {
            $current += $prize->probability;
            if ($random <= $current) return $prize;
        }

        return null;
    }

    protected function getSegmentColor($index)
    {
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
        return $colors[$index % count($colors)];
    }

    protected function getTextColor($index)
    {
        return $index % 2 === 0 ? '#FFFFFF' : '#000000';
    }

    public function render()
    {
        return view('livewire.wheel-spin-component');
    }
}
