<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class NewsletterSubscriberController extends Controller
{
    /**
     * Store a new newsletter subscriber.
     *
     * This endpoint allows users to subscribe to the newsletter by providing their email address.
     * The email is validated for format and uniqueness, and the client's IP address is recorded.
     * A verification email is sent to the provided email address. The subscription is not active
     * until the email is verified. Returns a success message or an error for invalid inputs
     * or duplicate emails.
     *
     * @param Request $request The HTTP request containing the email.
     * @return JsonResponse The response with success or error details.
     *
     * @response 201 {
     *     "message": "Subscription request received. Please check your email to verify."
     * }
     * @response 422 {
     *     "error": "Invalid input",
     *     "details": {
     *         "email": [
     *             "The email field is required.",
     *             "The email must be a valid email address."
     *         ]
     *     }
     * }
     * @response 409 {
     *     "error": "Email already subscribed."
     * }
     * @response 500 {
     *     "error": "Failed to process subscription. Please try again later."
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
            $subscriber = NewsletterSubscriber::create([
                'email' => $request->input('email'),
                'ip_address' => $request->ip(),
            ]);

            // Send verification email
            Notification::send($subscriber, new \App\Notifications\VerifyNewsletterSubscription($subscriber));

            $adminUsers = User::role(['admin', 'super_admin'])->get();

            foreach ($adminUsers as $admin) {
                App::setLocale($admin->locale ?? config('app.locale'));

                \Filament\Notifications\Notification::make()
                    ->title(__('New Newsletter Subscription'))
                    ->warning()
                    ->body(__('A new user has subscribed to the newsletter with the email: :email', [
                        'email' => $subscriber->email,
                    ]))
                    ->actions([
                        Action::make('view')
                            ->label(__('View Subscribers'))
                            ->url(route('filament.admin.resources.newsletter-subscribers.index'))
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);
            }

            return response()->json([
                'message' => 'Subscription request received. Please check your email to verify.',
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
                'error' => 'Failed to process subscription. Please try again later.',
            ], 500);
        }
    }

    /**
     * Verify a newsletter subscriber's email address.
     *
     * This endpoint verifies a subscriber's email using a signed URL from the verification email.
     * The URL includes the subscriber's ID and a hash of their email. If valid, the `verified_at`
     * timestamp is set, and a success message is returned. Invalid or expired signatures return
     * an error.
     *
     * @param Request $request The HTTP request containing the ID and hash.
     * @param int $id The subscriber's ID.
     * @param string $hash The hash of the subscriber's email.
     * @return JsonResponse The response with success or error details.
     *
     * @response 200 {
     *     "message": "Email successfully verified."
     * }
     * @response 403 {
     *     "error": "Invalid or expired verification link."
     * }
     * @response 404 {
     *     "error": "Subscriber not found."
     * }
     */
    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        $subscriber = NewsletterSubscriber::find($id);

        if (!$subscriber) {
            return response()->json([
                'error' => 'Subscriber not found.',
            ], 404);
        }

        if (!hash_equals($hash, sha1($subscriber->email))) {
            return response()->json([
                'error' => 'Invalid or expired verification link.',
            ], 403);
        }

        if (!$request->hasValidSignature()) {
            return response()->json([
                'error' => 'Invalid or expired verification link.',
            ], 403);
        }

        if (!$subscriber->verified_at) {
            $subscriber->update(['verified_at' => now()]);
        }

        return response()->json([
            'message' => 'Email successfully verified.',
        ], 200);
    }
}
