<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpinWheelRequest;
use App\Models\Contact;
use App\Models\User;
use App\Models\Wheel;
use App\Models\WheelPrize;
use App\Models\WheelSpin;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WheelController extends Controller
{
    /**
     * Get the active wheel and its prizes
     *
     * Retrieves the currently active wheel of fortune, including its configuration and available prizes.
     * The wheel must be active (is_active=true), within its valid date range, and have at least one available prize.
     * All text fields are returned in the requested language (English or Arabic).
     *
     * @group Wheel of Fortune
     * @queryParam lang string The language for translations (en or ar). Defaults to application's default locale. Example: ar
     *
     * @response 200 {
     *     "data": {
     *         "id": integer,
     *         "name": string,
     *         "is_active": boolean,
     *         "start_date": string|null,
     *         "end_date": string|null,
     *         "spins_per_user": integer,
     *         "spins_duration": integer,
     *         "display_rules": string,
     *         "specific_pages": array|null,
     *         "prizes": array [
     *             {
     *                 "id": integer,
     *                 "name": string,
     *                 "type": string,
     *                 "value": integer|null,
     *                 "coupon_id": integer|null,
     *                 "discount_id": integer|null,
     *                 "probability": integer,
     *                 "is_available": boolean
     *             }
     *         ],
     *         "language": string
     *     },
     *     "message": string
     * }
     * @response 404 {
     *     "error": string
     * }
     *
     * @example response Example Success Response
     * {
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
     *             }
     *         ],
     *         "language": "en"
     *     },
     *     "message": "Active wheel retrieved successfully"
     * }
     *
     * @example response Example Error Response
     * {
     *     "error": "No active wheel found"
     * }
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Determine the requested language (from query param or Accept-Language header)
        $lang = request()->query('lang', request()->getPreferredLanguage(['en', 'ar']) ?? 'en');
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'en';
        }
        App::setLocale($lang);

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
                'error' => __('No active wheel found', [], $lang),
            ], 404);
        }

        // Ensure translations are loaded for the wheel and prizes
        $wheel->setLocale($lang);
        $wheel->prizes->each->setLocale($lang);

        // Get the wheel data and ensure only the current language's name is returned
        $wheelData = $wheel->toArray();
        $wheelData['name'] = $wheel->getTranslation('name', $lang);

        // Modify prize names to show only the current language
        if (isset($wheelData['prizes'])) {
            foreach ($wheelData['prizes'] as &$prize) {
                $prize['name'] = $wheel->prizes->find($prize['id'])->getTranslation('name', $lang);
            }
        }

        return response()->json([
            'data' => array_merge($wheelData, ['language' => $lang]),
            'message' => __('Active wheel retrieved successfully', [], $lang),
        ], 200);
    }

    /**
     * Spin the wheel of fortune
     *
     * Processes a wheel spin attempt for either authenticated users or guests. Validates:
     * - Wheel is active and available
     * - User hasn't exceeded spin limits (spins_per_user)
     * - Cooldown period has expired (spins_duration)
     *
     * Every spin guarantees a prize (is_winner = true). If the spin limit is reached, returns the latest spin record.
     * If in cooldown, informs the user to wait with the next allowed spin time.
     *
     * @group Wheel of Fortune
     * @authenticated
     * @bodyParam wheel_id integer required The ID of an active wheel. Example: 1
     *
     * @response 200 {
     *     "data": {
     *         "spin_id": integer,
     *         "wheel_id": integer,
     *         "is_winner": true,
     *         "prize": {
     *             "id": integer,
     *             "name": string,
     *             "type": string,
     *             "value": integer|null,
     *             "coupon_id": integer|null,
     *             "discount_id": integer|null,
     *             "probability": integer,
     *             "is_available": boolean
     *         },
     *         "language": string
     *     },
     *     "message": string
     * }
     * @response 403 {
     *     "error": string,
     *     "remaining_spins": integer,
     *     "next_spin_at": string|null,
     *     "latest_spin": {
     *         "spin_id": integer,
     *         "wheel_id": integer,
     *         "is_winner": true,
     *         "prize": {
     *             "id": integer,
     *             "name": string,
     *             "type": string,
     *             "value": integer|null,
     *             "coupon_id": integer|null,
     *             "discount_id": integer|null,
     *             "probability": integer,
     *             "is_available": boolean
     *         }
     *     }
     * }
     * @response 404 {
     *     "error": string
     * }
     * @response 422 {
     *     "message": string,
     *     "errors": {
     *         "wheel_id": array
     *     }
     * }
     * @response 500 {
     *     "error": string
     * }
     *
     * @param SpinWheelRequest $request
     * @return JsonResponse
     */
    public function spin(SpinWheelRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Determine the requested language
            $lang = request()->query('lang', request()->getPreferredLanguage(['en', 'ar']) ?? 'en');
            if (!in_array($lang, ['en', 'ar'])) {
                $lang = 'en';
            }
            App::setLocale($lang);

            $user = Auth::user();
            $sessionId = $user ? null : $this->ensureValidSessionId();

            $wheel = Wheel::with(['prizes' => fn($q) => $q->where('is_available', true)])
                ->findOrFail($request->wheel_id);

            if (!$wheel->isActive()) {
                return response()->json([
                    'error' => __('Wheel is currently unavailable', [], $lang),
                ], 404);
            }

            // Check spin eligibility
            $eligibility = $this->checkSpinEligibility($wheel, $user, $sessionId);
            if (!$eligibility['can_spin']) {
                $latestSpin = $eligibility['latest_spin'];
                if ($latestSpin) {
                    $latestSpin->load('wheelPrize');
                    $prize = $latestSpin->wheelPrize;
                    if ($prize) {
                        $prize->setLocale($lang);
                    }
                }

                return response()->json([
                    'error' => $eligibility['message'],
                    'remaining_spins' => $eligibility['remaining_spins'],
                    'next_spin_at' => $eligibility['next_spin_at'],
                    'latest_spin' => $latestSpin ? [
                        'spin_id' => $latestSpin->id,
                        'wheel_id' => $latestSpin->wheel_id,
                        'is_winner' => $latestSpin->is_winner,
                        'prize' => $prize ? $prize->only([
                            'id', 'name', 'type', 'value', 'coupon_id', 'discount_id', 'probability', 'is_available'
                        ]) : null,
                    ] : null,
                ], 403);
            }

            // Select a guaranteed prize
            $prize = $this->selectGuaranteedPrize($wheel->prizes);
            $prize->setLocale($lang);

            // Record the spin
            $spin = WheelSpin::create([
                'user_id' => $user?->id,
                'contact_id' => $user ? null : Contact::where('session_id', $sessionId)->first()?->id,
                'session_id' => $sessionId,
                'wheel_id' => $wheel->id,
                'wheel_prize_id' => $prize->id,
                'is_winner' => true, // Always true for guaranteed prize
            ]);

            // Apply the prize to the user
            $this->applyPrizeToUser($prize, $user);

            DB::commit();

            Log::info('Wheel spin recorded', [
                'spin_id' => $spin->id,
                'user_id' => $user?->id,
                'session_id' => $sessionId,
                'wheel_id' => $wheel->id,
                'prize_id' => $prize->id,
                'language' => $lang,
            ]);

            return response()->json([
                'data' => [
                    'spin_id' => $spin->id,
                    'wheel_id' => $wheel->id,
                    'prize' => $prize->only([
                        'id', 'name', 'type', 'value', 'coupon_id', 'discount_id', 'probability', 'is_available'
                    ]),
                    'is_winner' => $spin->is_winner,
                    'language' => $lang,
                ],
                'message' => __('You won a :prize!', ['prize' => $prize->name], $lang),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Spin failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'error' => __('Spin failed. Please try again.', [], $lang),
            ], 500);
        }
    }

    /**
     * Check if user can spin with proper limits and cooldown
     *
     * @param Wheel $wheel
     * @param User|null $user
     * @param string|null $sessionId
     * @return array
     */
    private function checkSpinEligibility(Wheel $wheel, ?User $user, ?string $sessionId): array
    {
        $lang = App::getLocale();
        $query = WheelSpin::where('wheel_id', $wheel->id)
            ->lockForUpdate() // Prevent concurrent spins
            ->when($user, fn($q) => $q->where('user_id', $user->id), fn($q) => $q->where('session_id', $sessionId));

        $spinCount = $query->count();
        $lastSpin = $query->latest()->first();

        // Check max spins (spins_per_user)
        if ($spinCount >= $wheel->spins_per_user) {
            return [
                'can_spin' => false,
                'message' => __('Maximum spins reached', [], $lang),
                'remaining_spins' => 0,
                'next_spin_at' => null,
                'latest_spin' => $lastSpin,
            ];
        }

        // Check cooldown (spins_duration)
        $cooldownEnd = $lastSpin?->created_at->addHours($wheel->spins_duration);
        if ($lastSpin && now()->lt($cooldownEnd)) {
            return [
                'can_spin' => false,
                'message' => __('Please wait before spinning again', [], $lang),
                'remaining_spins' => $wheel->spins_per_user - $spinCount,
                'next_spin_at' => $cooldownEnd?->toIso8601String(),
                'latest_spin' => $lastSpin,
            ];
        }

        return [
            'can_spin' => true,
            'message' => null,
            'remaining_spins' => $wheel->spins_per_user - $spinCount,
            'next_spin_at' => null,
            'latest_spin' => null,
        ];
    }

    /**
     * Select a guaranteed prize
     *
     * @param Collection $prizes
     * @return WheelPrize
     * @throws \Exception
     */
    private function selectGuaranteedPrize(Collection $prizes): WheelPrize
    {
        $lang = App::getLocale();
        $availablePrizes = $prizes->where('type', '!=', 'none'); // Exclude 'none' type for guaranteed wins
        if ($availablePrizes->isEmpty()) {
            throw new \Exception(__('No available prizes for this wheel', [], $lang));
        }

        $totalWeight = $availablePrizes->sum('probability');
        if ($totalWeight <= 0) {
            // Fallback to random prize if probabilities are invalid
            return $availablePrizes->random();
        }

        $random = mt_rand(1, $totalWeight);
        $cumulative = 0;

        foreach ($availablePrizes as $prize) {
            $cumulative += $prize->probability;
            if ($random <= $cumulative) {
                return $prize;
            }
        }

        // Fallback to first prize if algorithm fails
        return $availablePrizes->first();
    }

    /**
     * Ensure valid session ID for guests
     *
     * @return string
     */
    private function ensureValidSessionId(): string
    {
        $sessionId = session()->getId();
        if (!Str::isUuid($sessionId)) {
            $sessionId = Str::uuid()->toString();
            session()->setId($sessionId);
            session()->save();
        }
        return $sessionId;
    }

    /**
     * Apply the prize to the user
     *
     * @param WheelPrize $prize
     * @param User|null $user
     * @return void
     */
    private function applyPrizeToUser(WheelPrize $prize, ?User $user): void
    {
        // Placeholder: Implement prize application logic based on prize type
        Log::info('Prize applied', [
            'prize_id' => $prize->id,
            'prize_type' => $prize->type,
            'user_id' => $user?->id,
        ]);

        // Example implementation (customize based on your system):
        if ($prize->type === 'points' && $user) {
            $user->increment('points', $prize->value);
        } elseif ($prize->type === 'coupon' && $user && $prize->coupon_id) {
            DB::table('user_coupon')->insert([
                'user_id' => $user->id,
                'coupon_id' => $prize->coupon_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($prize->type === 'discount' && $user && $prize->discount_id) {
            DB::table('user_discount')->insert([
                'user_id' => $user->id,
                'discount_id' => $prize->discount_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($prize->type === 'product') {
            // Requires product_id in wheel_prizes schema
        }
    }
}
