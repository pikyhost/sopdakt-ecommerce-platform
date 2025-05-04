<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterSubscriberController extends Controller
{
    /**
     * Store a new newsletter subscriber.
     *
     * This endpoint allows users to subscribe to the newsletter by providing their email address.
     * The email is validated for format and uniqueness. The client's IP address is also recorded.
     * Returns a success message on successful subscription or an error message for invalid inputs
     * or duplicate emails.
     *
     * @param Request $request The HTTP request containing the email.
     * @return JsonResponse The response containing success or error details.
     *
     * @response 201 {
     *     "message": "Successfully subscribed to the newsletter."
     * }
     * @response 422 {
     *     "error": "Invalid input",
     *     "details": {
     *         "email": ["The email field is required.", "The email must be a valid email address."]
     *     }
     * }
     * @response 409 {
     *     "error": "Email already subscribed."
     * }
     */
    public function store(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns|max:255|unique:newsletter_subscribers,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid input',
                'details' => $validator->errors(),
            ], 422);
        }

        try {
            // Create new subscriber
            NewsletterSubscriber::create([
                'email' => $request->input('email'),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Successfully subscribed to the newsletter.',
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate email (race condition)
            if ($e->getCode() === '23000') {
                return response()->json([
                    'error' => 'Email already subscribed.',
                ], 409);
            }

            // Handle other database errors
            return response()->json([
                'error' => 'Failed to subscribe. Please try again later.',
            ], 500);
        }
    }
}
