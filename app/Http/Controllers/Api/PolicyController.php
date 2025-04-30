<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class PolicyController extends Controller
{
    /**
     * Retrieve the Privacy Policy
     *
     * This endpoint fetches the Privacy Policy content in the current application locale (English or Arabic).
     * The response includes the policy content and the locale used. If no policy data is found, an error
     * message is returned indicating that the policy is null or empty.
     *
     * @group Policies
     * @response 200 {
     *     "data": {
     *         "content": "# Privacy Policy in English",
     *         "locale": "en"
     *     },
     *     "message": "Privacy Policy retrieved successfully"
     * }
     * @response 200 {
     *     "data": {
     *         "content": "# سياسة الخصوصية بالعربية",
     *         "locale": "ar"
     *     },
     *     "message": "Privacy Policy retrieved successfully"
     * }
     * @response 404 {
     *     "error": "Privacy Policy is null or empty",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @response 500 {
     *     "error": "An unexpected error occurred while retrieving the Privacy Policy. Please try again.",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @return JsonResponse
     */
    public function privacy(): JsonResponse
    {
        return $this->getPolicy('privacy_policy');
    }

    /**
     * Retrieve the Refund Policy
     *
     * This endpoint fetches the Refund Policy content in the current application locale (English or Arabic).
     * The response includes the policy content and the locale used. If no policy data is found, an error
     * message is returned indicating that the policy is null or empty.
     *
     * @group Policies
     * @response 200 {
     *     "data": {
     *         "content": "# Refund Policy in English",
     *         "locale": "en"
     *     },
     *     "message": "Refund Policy retrieved successfully"
     * }
     * @response 200 {
     *     "data": {
     *         "content": "# سياسة الاسترجاع بالعربية",
     *         "locale": "ar"
     *     },
     *     "message": "Refund Policy retrieved successfully"
     * }
     * @response 404 {
     *     "error": "Refund Policy is null or empty",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @response 500 {
     *     "error": "An unexpected error occurred while retrieving the Refund Policy. Please try again.",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @return JsonResponse
     */
    public function refund(): JsonResponse
    {
        return $this->getPolicy('refund_policy');
    }

    /**
     * Retrieve the Terms of Service
     *
     * This endpoint fetches the Terms of Service content in the current application locale (English or Arabic).
     * The response includes the policy content and the locale used. If no policy data is found, an error
     * message is returned indicating that the policy is null or empty.
     *
     * @group Policies
     * @response 200 {
     *     "data": {
     *         "content": "# Terms of Service in English",
     *         "locale": "en"
     *     },
     *     "message": "Terms of Service retrieved successfully"
     * }
     * @response 200 {
     *     "data": {
     *         "content": "# شروط الخدمة بالعربية",
     *         "locale": "ar"
     *     },
     *     "message": "Terms of Service retrieved successfully"
     * }
     * @response 404 {
     *     "error": "Terms of Service is null or empty",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @response 500 {
     *     "error": "An unexpected error occurred while retrieving the Terms of Service. Please try again.",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @return JsonResponse
     */
    public function terms(): JsonResponse
    {
        return $this->getPolicy('terms_of_service');
    }

    /**
     * Helper method to retrieve policy content
     *
     * Fetches the specified policy type in the current locale and returns a JSON response.
     *
     * @param string $policyType The type of policy (privacy_policy, refund_policy, terms_of_service)
     * @return JsonResponse
     */
    private function getPolicy(string $policyType): JsonResponse
    {
        try {
            // Get the current locale
            $locale = App::getLocale();

            // Fetch the policy content
            $content = Policy::getPolicy($policyType, $locale);

            // Check if content is null or empty
            if (is_null($content) || trim($content) === '') {
                $policyName = ucwords(str_replace('_', ' ', $policyType));
                return response()->json([
                    'error' => "{$policyName} is null or empty",
                    'support_link' => route('contact.us'),
                ], 404);
            }

            return response()->json([
                'data' => [
                    'content' => $content,
                ],
                'message' => ucwords(str_replace('_', ' ', $policyType)) . ' retrieved successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error(ucwords(str_replace('_', ' ', $policyType)) . ' API error: ' . $e->getMessage());
            $policyName = ucwords(str_replace('_', ' ', $policyType));
            return response()->json([
                'error' => "An unexpected error occurred while retrieving the {$policyName}. Please try again.",
                'support_link' => route('contact.us'),
            ], 500);
        }
    }
}
