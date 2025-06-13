<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wheel;
use App\Models\WheelPreference;
use App\Models\WheelSpin;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WheelController extends Controller
{
    /**
     * Get active wheel for current page
     */
    public function getActiveWheel(Request $request)
    {
        $currentPath = $request->input('current_path', '/');
        $user = Auth::guard('sanctum')->user();
        $sessionId = $request->header('x-session-id');

        $wheel = Wheel::where('is_active', true)
            ->orderBy('popup_order')
            ->with(['prizes' => function($query) {
                $query->where('is_active', true)->orderBy('probability');
            }])
            ->get()
            ->reject(function ($wheel) use ($user, $sessionId) {
                return $wheel->shouldBeHiddenFor($user, $sessionId);
            })
            ->first(function ($wheel) use ($currentPath) {
                return $wheel->shouldDisplayOnPage($currentPath);
            });

        if (!$wheel) {
            return response()->json([
                'success' => false,
                'message' => 'No active wheel available for this page.',
                'show_wheel' => false
            ], 200);
        }

        // Prepare wheel data with translations
        $wheelData = $wheel->makeHidden(['created_at', 'updated_at'])->toArray();
        $wheelData['name'] = $wheel->getTranslation('name', app()->getLocale());
        $wheelData['description'] = $wheel->getTranslation('description', app()->getLocale());

        // Prepare prizes with translations
        $wheelData['prizes'] = $wheel->prizes->map(function ($prize) {
            return [
                'id' => $prize->id,
                'name' => $prize->getTranslation('name', app()->getLocale()),
                'probability' => $prize->probability,
                'coupon_id' => $prize->coupon_id,
                'is_active' => $prize->is_active,
            ];
        });

        return response()->json([
            'success' => true,
            'wheel' => $wheelData,
            'require_phone' => $wheel->require_phone,
            'show_wheel' => true,
            'display_settings' => [
                'show_interval_minutes' => $wheel->show_interval_minutes,
                'delay_seconds' => $wheel->delay_seconds,
                'duration_seconds' => $wheel->duration_seconds,
//                'dont_show_again_days' => $wheel->dont_show_again_days,
            ]
        ]);
    }

    /**
     * Perform a wheel spin
     */
    public function spin(Request $request)
    {
        $wheel = Wheel::where('is_active', true)->first();

        if (!$wheel) {
            return response()->json([
                'success' => false,
                'message' => 'No active wheel available.'
            ], 400);
        }

        // Validate phone if required
        if ($wheel->require_phone) {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|min:10'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number is required and must be at least 10 characters.',
                    'errors' => $validator->errors()
                ], 422);
            }
        }

        // Check eligibility
        $eligibilityResponse = $this->checkEligibility($request);
        $eligibilityData = json_decode($eligibilityResponse->getContent(), true);

        if (!$eligibilityData['can_spin']) {
            return response()->json([
                'success' => false,
                'message' => $eligibilityData['message'],
                'time_remaining' => $eligibilityData['time_remaining']
            ], 400);
        }

        // Get prizes with translations
        $prizes = $wheel->prizes->map(function ($prize) {
            return [
                'id' => $prize->id,
                'name' => $prize->getTranslation('name', app()->getLocale()),
                'probability' => $prize->probability,
                'coupon_id' => $prize->coupon_id,
                'is_active' => $prize->is_active,
            ];
        })->toArray();

        // Select a random prize based on probability
        $selectedPrize = $this->selectRandomPrize($prizes);

        // Record the spin
        $user = Auth::guard('sanctum')->user();
        $spin = WheelSpin::create([
            'wheel_id' => $wheel->id,
            'user_id' => $user ? $user->id : null,
            'session_id' => $request->header('x-session-id'),
            'phone' => $request->phone,
            'prize_id' => $selectedPrize['id'],
            'ip_address' => $request->ip(),
        ]);

        // Prepare result
        $result = [
            'prize' => $selectedPrize['name'],
            'coupon' => null,
            'coupon_type' => null,
            'discount_value' => null
        ];

        // Add coupon if prize has one
        if ($selectedPrize['coupon_id']) {
            $coupon = Coupon::find($selectedPrize['coupon_id']);
            if ($coupon) {
                $result['coupon'] = $coupon->code;
                $result['coupon_type'] = $coupon->type;
                $result['discount_value'] = $coupon->value;
            }
        }

        return response()->json([
            'success' => true,
            'result' => $result,
            'selected_prize_index' => array_search($selectedPrize['id'], array_column($prizes, 'id')),
            'message' => 'Spin successful!'
        ], 200);
    }

    /**
     * Check if user can spin the wheel
     */
    public function checkEligibility(Request $request)
    {
        $wheel = Wheel::where('is_active', true)->first();

        if (!$wheel) {
            return response()->json([
                'can_spin' => false,
                'message' => 'No active wheel available.',
                'show_wheel' => false,
                'time_remaining' => ''
            ], 200);
        }

        $user = Auth::guard('sanctum')->user();
        $sessionId = request()->header('x-session-id') ?? session()->getId();
        $now = Carbon::now();

        // Check daily limit
        $todaySpins = WheelSpin::where(function($query) use ($user, $sessionId) {
            if ($user) {
                $query->where('user_id', $user->id);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
            ->where('wheel_id', $wheel->id)
            ->whereDate('created_at', $now->toDateString())
            ->count();

        if ($todaySpins >= $wheel->daily_spin_limit) {
            $nextDay = $now->copy()->addDay()->startOfDay();
            $diff = $now->diff($nextDay);
            $timeRemaining = $this->formatTimeRemaining($diff);

            return response()->json([
                'can_spin' => false,
                'show_wheel' => false,
                'time_remaining' => $timeRemaining,
                'message' => "Daily spin limit reached ({$wheel->daily_spin_limit} spins per day). Try again in {$timeRemaining}."
            ], 200);
        }

        // Check time between spins
        $lastSpin = WheelSpin::where(function($query) use ($user, $sessionId) {
            if ($user) {
                $query->where('user_id', $user->id);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
            ->where('wheel_id', $wheel->id)
            ->latest()
            ->first();

        if ($lastSpin) {
            $minutesBetween = $wheel->time_between_spins_minutes;
            $nextSpinTime = $lastSpin->created_at->copy()->addMinutes($minutesBetween);
            $minutesPassed = $lastSpin->created_at->diffInMinutes($now);

            if ($minutesPassed < $minutesBetween) {
                $diff = $now->diff($nextSpinTime);
                $timeRemaining = $this->formatTimeRemaining($diff);

                return response()->json([
                    'can_spin' => false,
                    'show_wheel' => false,
                    'time_remaining' => $timeRemaining,
                    'message' => "Please wait {$timeRemaining} before spinning again."
                ], 200);
            }
        }

        return response()->json([
            'can_spin' => true,
            'show_wheel' => true,
            'message' => '',
            'time_remaining' => ''
        ], 200);
    }

    /**
     * Get user's spin history
     */
    public function spinHistory(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $spins = WheelSpin::with(['wheel', 'prize'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($spin) {
                return [
                    'id' => $spin->id,
                    'wheel_name' => $spin->wheel->getTranslation('name', app()->getLocale()),
                    'prize_name' => $spin->prize->getTranslation('name', app()->getLocale()),
                    'coupon_code' => $spin->prize->coupon_id ? Coupon::find($spin->prize->coupon_id)->code : null,
                    'spin_date' => $spin->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'spins' => $spins
        ], 200);
    }


    /**
     * Set "don't show again" preference
     */
    public function hideWheel(Request $request)
    {
        $request->validate([
            'wheel_id' => 'required|exists:wheels,id',
            'days' => 'sometimes|integer|min:1|max:365'
        ]);

        $wheelId = $request->input('wheel_id');
        $days = $request->input('days', 7);
        $user = Auth::guard('sanctum')->user();
        $sessionId = request()->header('x-session-id') ?? session()->getId();

        $data = [
            'wheel_id' => $wheelId,
            'hide_until' => Carbon::now()->addDays($days)
        ];

        if ($user) {
            $data['user_id'] = $user->id;
            WheelPreference::updateOrCreate(
                ['user_id' => $user->id, 'wheel_id' => $wheelId],
                $data
            );
        } else {
            $data['session_id'] = $sessionId;
            WheelPreference::updateOrCreate(
                ['session_id' => $sessionId, 'wheel_id' => $wheelId],
                $data
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Wheel will be hidden for {$days} days.",
            'hide_until' => Carbon::now()->addDays($days)->toDateTimeString()
        ], 200);
    }

    /**
     * Select random prize based on probability
     */
    protected function selectRandomPrize($prizes)
    {
        $totalProbability = array_sum(array_column($prizes, 'probability'));
        $random = mt_rand(1, $totalProbability);
        $current = 0;

        foreach ($prizes as $prize) {
            $current += $prize['probability'];
            if ($random <= $current) {
                return $prize;
            }
        }

        return $prizes[0]; // Fallback
    }

    /**
     * Format time remaining in human readable format
     */
    protected function formatTimeRemaining($diff)
    {
        $parts = [];
        if ($diff->d > 0) {
            $parts[] = "{$diff->d} day" . ($diff->d > 1 ? 's' : '');
        }
        if ($diff->h > 0) {
            $parts[] = "{$diff->h} hour" . ($diff->h > 1 ? 's' : '');
        }
        if ($diff->i > 0) {
            $parts[] = "{$diff->i} minute" . ($diff->i > 1 ? 's' : '');
        }
        if ($diff->s > 0 || empty($parts)) {
            $parts[] = "{$diff->s} second" . ($diff->s != 1 ? 's' : '');
        }

        return implode(', ', $parts);
    }
}
