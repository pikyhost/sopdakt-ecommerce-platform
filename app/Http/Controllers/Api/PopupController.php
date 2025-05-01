<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PopupController extends Controller
{
    /**
     * Retrieve all active popups
     *
     * This endpoint fetches all active popups configured in the system, intended for display on the frontend.
     * Each popup includes translatable fields (title, description, cta_text) in the current application locale
     * (English or Arabic), along with configuration details such as image, call-to-action link, display rules,
     * timing settings, and specific pages. The response is designed to provide React developers with all necessary
     * data to implement popup rendering, timing, and user interaction logic (e.g., delay, duration, "don't show again").
     * If no active popups are found, an error message is returned.
     *
     * @group Popups
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "title": "Welcome Offer",
     *             "description": "Get 20% off your first purchase!",
     *             "image_path": "https://your-domain.com/storage/popups/welcome.jpg",
     *             "cta_text": "Shop Now",
     *             "cta_link": "/shop",
     *             "is_active": true,
     *             "email_needed": false,
     *             "display_rules": "all_pages",
     *             "popup_order": 0,
     *             "show_interval_minutes": 60,
     *             "delay_seconds": 60,
     *             "duration_seconds": 60,
     *             "dont_show_again_days": 7,
     *             "specific_pages": null,
     *             "created_at": "2025-04-30T12:00:00.000000Z",
     *             "updated_at": "2025-04-30T12:00:00.000000Z"
     *         },
     *         {
     *             "id": 2,
     *             "title": "Newsletter Signup",
     *             "description": "Subscribe to our newsletter for exclusive updates.",
     *             "image_path": null,
     *             "cta_text": "Subscribe",
     *             "cta_link": "/subscribe",
     *             "is_active": true,
     *             "email_needed": true,
     *             "display_rules": "specific_pages",
     *             "popup_order": 1,
     *             "show_interval_minutes": 120,
     *             "delay_seconds": 30,
     *             "duration_seconds": 120,
     *             "dont_show_again_days": 14,
     *             "specific_pages": ["/home", "/about"],
     *             "created_at": "2025-04-30T12:00:00.000000Z",
     *             "updated_at": "2025-04-30T12:00:00.000000Z"
     *         }
     *     ],
     *     "message": "Active popups retrieved successfully"
     * }
     * @response 404 {
     *     "error": "No active popups found",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @response 500 {
     *     "error": "An unexpected error occurred while retrieving popups. Please try again.",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @return JsonResponse
     */
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

            // Format popups with image URLs
            $formattedPopups = $popups->map(function ($popup) {
                return [
                    'id' => $popup->id,
                    'title' => $popup->title,
                    'description' => $popup->description,
                    'image_path' => $popup->image_path ? Storage::url($popup->image_path) : null,
                    'cta_text' => $popup->cta_text,
                    'cta_link' => $popup->cta_link,
                    'is_active' => $popup->is_active,
                    'email_needed' => $popup->email_needed,
                    'display_rules' => $popup->display_rules,
                    'popup_order' => $popup->popup_order,
                    'show_interval_minutes' => $popup->show_interval_minutes,
                    'delay_seconds' => $popup->delay_seconds,
                    'duration_seconds' => $popup->duration_seconds,
                    'dont_show_again_days' => $popup->dont_show_again_days,
                    'specific_pages' => $popup->specific_pages,
                    'created_at' => $popup->created_at->toIso8601String(),
                    'updated_at' => $popup->updated_at->toIso8601String(),
                ];
            })->toArray();

            return response()->json([
                'data' => $formattedPopups,
                'message' => 'Active popups retrieved successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Popups API error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An unexpected error occurred while retrieving popups. Please try again.',
                'support_link' => route('contact.us'),
            ], 500);
        }
    }
}
