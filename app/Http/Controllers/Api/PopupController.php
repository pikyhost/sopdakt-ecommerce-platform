<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\CouponEmail;

class PopupController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            // Fetch active popups, ordered by popup_order
            $popups = Popup::active()->orderBy('popup_order')->get();

            // Check if popups are empty
            if ($popups->isEmpty()) {
                return response()->json([
                    'error' => 'No active popups found',
                    'support_link' => route('contact.us'),
                ], 404);
            }

            // Load frontend pages mapping
            $pageMap = config('frontend-pages');

            // Format popups with image URLs and friendly specific pages
            $formattedPopups = $popups->map(function ($popup) use ($pageMap) {
                $specificPagesRaw = json_decode($popup->specific_pages ?? '[]', true);

                $specificPagesFriendly = collect($specificPagesRaw)
                    ->map(fn ($uri) => [
                        'uri' => $uri,
                        'label' => $pageMap[$uri] ?? $uri,
                    ])
                    ->values()
                    ->toArray();

                return [
                    'id' => $popup->id,
                    'title' => $popup->getTranslation('title', app()->getLocale()),
                    'description' => $popup->getTranslation('description', app()->getLocale()),
                    'image_path' => $popup->image_path ? asset('storage/' . $popup->image_path) : null,
                    'cta_text' => $popup->getTranslation('cta_text', app()->getLocale()),
                    'cta_link' => $popup->cta_link,
                    'is_active' => $popup->is_active,
                    'email_needed' => $popup->email_needed,
                    'display_rules' => $popup->display_rules,
                    'popup_order' => $popup->popup_order,
                    'show_interval_minutes' => $popup->show_interval_minutes,
                    'delay_seconds' => $popup->delay_seconds,
                    'duration_seconds' => $popup->duration_seconds,
                    'specific_pages' => $specificPagesFriendly,
                    'created_at' => $popup->created_at->toIso8601String(),
                    'updated_at' => $popup->updated_at->toIso8601String(),
                ];
            })->toArray();

            return response()->json([
                'data' => $formattedPopups,
                'message' => 'Active popups retrieved successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred while retrieving popups. Please try again.',
                'support_link' => route('contact.us'),
            ], 500);
        }
    }

    /**
     * Handle email submission for popups that require email and send coupon
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function submitEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'popup_id' => 'required|exists:popups,id',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $popup = Popup::find($request->popup_id);

            if (!$popup->email_needed) {
                return response()->json([
                    'error' => 'This popup does not require email submission',
                ], 400);
            }

            // Get an active coupon (you might want to customize this logic)
            $coupon = Coupon::where('is_active', true)
                ->where('expires_at', '>', now())
                ->inRandomOrder()
                ->first();

            if (!$coupon) {
                return response()->json([
                    'error' => 'No available coupons at this time',
                ], 404);
            }

            // Send email with coupon
            Mail::to($request->email)->send(new CouponEmail($coupon));

            return response()->json([
                'message' => 'Coupon code has been sent to your email',
                'coupon_code' => $coupon->code, // Optional: remove if you don't want to expose it
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while processing your request. Please try again.',
                'support_link' => route('contact.us'),
            ], 500);
        }
    }
}
