<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpinWheelRequest;
use App\Models\Contact;
use App\Models\Wheel;
use App\Models\WheelPrize;
use App\Models\WheelSpin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WheelController extends Controller
{
    /**
     * Get the active wheel and its prizes
     *
     * Retrieves the currently active wheel, including its prizes and display rules, for display on the frontend.
     * The wheel must be active, within its start and end dates (if set), and have available prizes.
     * The response includes translations for the wheel and prize names in the requested language (en or ar).
     *
     * @group Wheel of Fortune
     * @queryParam lang string The language for translations (en or ar). Defaults to en. Example: ar
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "name": "Summer Spin",
     *         "is_active": true,
     *         "start_date": "2025-05-01T00:00:00+00:00",
     *         "end_date": "2025-05-31T23:59:59+00:00",
     *         "spins_per_user": 1,
     *         "spins_duration": 24,
     *         "display_rules": "all_pages",
     *         "specific_pages": null,
     *         "prizes": [
     *             {
     *                 "id": 1,
     *                 "name": "10% Off",
     *                 "type": "discount",
     *                 "value": null,
     *                 "coupon_id": null,
     *                 "discount_id": 1,
     *                 "probability": 30,
     *                 "is_available": true
     *             },
     *             {
     *                 "id": 2,
     *                 "name": "100 Points",
     *                 "type": "points",
     *                 "value": 100,
     *                 "coupon_id": null,
     *                 "discount_id": null,
     *                 "probability": 20,
     *                 "is_available": true
     *             }
     *         ],
     *         "language": "en"
     *     },
     *     "message": "Active wheel retrieved successfully"
     * }
     * @response 404 {
     *     "error": "No active wheel found"
     * }
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Determine the requested language (from query param or Accept-Language header)
        $lang = request()->query('lang', request()->getPreferredLanguage(['en', 'ar']) ?? 'en');
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en'; // Fallback to English
        }

        // Set the locale for translations
        app()->setLocale($lang);

        $wheel = Wheel::with(['prizes' => function ($query) {
            $query->where('is_available', true);
        }])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->first();

        if (!$wheel) {
            return response()->json([
                'error' => 'No active wheel found',
            ], 404);
        }

        // Ensure translations are loaded for the wheel and prizes
        $wheel->setLocale($lang);
        $wheel->prizes->each->setLocale($lang);

        return response()->json([
            'data' => array_merge($wheel->toArray(), ['language' => $lang]),
            'message' => 'Active wheel retrieved successfully',
        ], 200);
    }

    /**
     * Spin the wheel
     *
     * Allows a user (authenticated or guest) to spin the wheel and potentially win a prize.
     * Validates spin eligibility based on spins_per_user and spins_duration.
     * Returns the spin result, including the prize (if any).
     *
     * @group Wheel of Fortune
     * @bodyParam wheel_id integer required The ID of the wheel to spin. Example: 1
     * @response 200 {
     *     "data": {
     *         "spin_id": 1,
     *         "wheel_id": 1,
     *         "prize": {
     *             "id": 1,
     *             "name": "10% Off",
     *             "type": "discount",
     *             "value": null,
     *             "coupon_id": null,
     *             "discount_id": 1,
     *             "probability": 30,
     *             "is_available": true
     *         },
     *         "is_winner": true
     *     },
     *     "message": "Spin successful"
     * }
     * @response 403 {
     *     "error": "You have reached the maximum number of spins or are in a cooldown period.",
     *     "cooldown_until": "2025-05-02T08:00:00+00:00"
     * }
     * @response 404 {
     *     "error": "Wheel not found or inactive"
     * }
     * @response 422 {
     *     "message": "The given data was invalid.",
     *     "errors": {
     *         "wheel_id": ["The selected wheel is not active or does not exist."]
     *     }
     * }
     * @param SpinWheelRequest $request
     * @return JsonResponse
     */
    public function spin(SpinWheelRequest $request): JsonResponse
    {
        try {
            // Determine the requested language for prize name
            $lang = request()->query('lang', request()->getPreferredLanguage(['en', 'ar']) ?? 'en');
            if (!in_array($lang, ['en', 'ar'])) {
                $lang = 'en';
            }
            app()->setLocale($lang);

            $wheel = Wheel::with(['prizes' => function ($query) {
                $query->where('is_available', true);
            }])->findOrFail($request->wheel_id);

            if (!$wheel->isActive()) {
                return response()->json([
                    'error' => 'Wheel not found or inactive',
                ], 404);
            }

            $sessionId = session()->getId();
            $userId = Auth::id();

            // Check spin eligibility
            $spinCount = WheelSpin::where('wheel_id', $wheel->id)
                ->where(function ($query) use ($userId, $sessionId) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->where('session_id', $sessionId);
                    }
                })
                ->count();

            if ($spinCount >= $wheel->spins_per_user) {
                return response()->json([
                    'error' => 'You have reached the maximum number of spins.',
                ], 403);
            }

            $lastSpin = WheelSpin::where('wheel_id', $wheel->id)
                ->where(function ($query) use ($userId, $sessionId) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->where('session_id', $sessionId);
                    }
                })
                ->latest()
                ->first();

            if ($lastSpin && now()->lt($lastSpin->created_at->addHours($wheel->spins_duration))) {
                return response()->json([
                    'error' => 'You are in a cooldown period.',
                    'cooldown_until' => $lastSpin->created_at->addHours($wheel->spins_duration)->toIso8601String(),
                ], 403);
            }

            // Select a prize (weighted random)
            $prize = $this->selectPrize($wheel->prizes);
            if ($prize) {
                $prize->setLocale($lang); // Set language for prize name
            }

            // Record the spin
            $spin = WheelSpin::create([
                'user_id' => $userId,
                'contact_id' => $userId ? null : Contact::where('session_id', $sessionId)->first()?->id,
                'session_id' => $userId ? null : $sessionId,
                'wheel_id' => $wheel->id,
                'wheel_prize_id' => $prize?->id,
                'is_winner' => $prize && $prize->type !== 'none',
            ]);

            Log::info('Wheel spin recorded', [
                'spin_id' => $spin->id,
                'user_id' => $userId,
                'session_id' => $sessionId,
                'wheel_id' => $wheel->id,
                'prize_id' => $prize?->id,
                'language' => $lang,
            ]);

            return response()->json([
                'data' => [
                    'spin_id' => $spin->id,
                    'wheel_id' => $wheel->id,
                    'prize' => $prize,
                    'is_winner' => $spin->is_winner,
                    'language' => $lang,
                ],
                'message' => 'Spin successful',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Wheel spin failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'An unexpected error occurred while spinning the wheel.',
            ], 500);
        }
    }

    /**
     * Select a prize based on probability weights
     *
     * @param \Illuminate\Database\Eloquent\Collection $prizes
     * @return WheelPrize|null
     */
    private function selectPrize($prizes): ?WheelPrize
    {
        if ($prizes->isEmpty()) {
            return null;
        }

        $totalProbability = $prizes->sum('probability');
        if ($totalProbability <= 0) {
            return null;
        }

        $random = mt_rand(1, $totalProbability);
        $current = 0;

        foreach ($prizes as $prize) {
            $current += $prize->probability;
            if ($random <= $current) {
                return $prize;
            }
        }

        return null; // Fallback if |no prize is selected
    }
}
