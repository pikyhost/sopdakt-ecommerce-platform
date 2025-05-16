<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ContactSettingController extends Controller
{
    /**
     * Retrieve all contact settings
     *
     * This endpoint fetches all contact settings stored in the system, such as phone numbers, email addresses,
     * Skype handles, and social media links. The settings are returned as an associative array where the key is
     * the setting identifier (e.g., phone1, email1), and the value is the corresponding setting value. Social media
     * links are returned in a nested `social_media` object. If no settings are found, an error message is returned.
     *
     * @group Contact Settings
     *
     * @response 200 {
     *     "data": {
     *         "phone1": "0201 203 2032",
     *         "phone2": "0201 203 2032",
     *         "mobile1": "201-123-39223",
     *         "mobile2": "02-123-3928",
     *         "email1": "porto@gmail.com",
     *         "email2": "porto@portotemplate.com",
     *         "skype1": "porto_skype",
     *         "skype2": "porto_templete"
     *     },
     *     "social_media": {
     *         "facebook": "https://facebook.com/company",
     *         "instagram": "https://instagram.com/company.ae",
     *         "linkedin": null,
     *         "twitter": null,
     *         "youtube": null,
     *         "tiktok": null
     *     },
     *     "message": "Contact settings retrieved successfully"
     * }
     *
     * @response 404 {
     *     "error": "Contact settings are null or empty",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     *
     * @response 500 {
     *     "error": "An unexpected error occurred while retrieving contact settings. Please try again.",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Fetch all contact settings
            $settings = ContactSetting::getAllSettings();

            // Check if settings are empty
            if (empty($settings)) {
                return response()->json([
                    'error' => 'Contact settings are null or empty',
                    'support_link' => route('contact.us'),
                ], 404);
            }

            // Fetch social media links
            $socials = Setting::getSocialMediaLinks();

            return response()->json([
                'data' => $settings,
                'social_media' => $socials,
                'message' => 'Contact settings retrieved successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Contact Settings API error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An unexpected error occurred while retrieving contact settings. Please try again.',
                'support_link' => route('contact.us'),
            ], 500);
        }
    }
}
