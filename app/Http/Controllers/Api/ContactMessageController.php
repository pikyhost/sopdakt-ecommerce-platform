<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ContactMessageController extends Controller
{
    /**
     * Store a new contact message
     *
     * This endpoint allows users to submit a contact message, which is validated and stored in the system.
     * The message includes the user's name, email, phone number, optional subject, and message content.
     * Upon successful submission, a notification is sent via the ContactMessageNotifier, and the stored
     * message is returned in the response. The user's IP address and authenticated user ID (if logged in)
     * are also recorded.
     *
     * @group Contact Messages
     * @bodyParam name string required The name of the sender. Example: John Doe
     * @bodyParam email string required The email address of the sender. Must be a valid email. Example: john.doe@example.com
     * @bodyParam phone string required The phone number of the sender. Example: +1234567890
     * @bodyParam subject string nullable The subject of the message. Example: Inquiry about services
     * @bodyParam message string required The content of the message. Example: I have a question about your products.
     * @response 201 {
     *     "message": "Your message has been sent successfully.",
     *     "data": {
     *         "id": 1,
     *         "name": "John Doe",
     *         "email": "john.doe@example.com",
     *         "phone": "+1234567890",
     *         "subject": "Inquiry about services",
     *         "message": "I have a question about your products.",
     *         "ip_address": "192.168.1.1",
     *         "user_id": null,
     *         "created_at": "2025-04-30T12:00:00.000000Z",
     *         "updated_at": "2025-04-30T12:00:00.000000Z"
     *     }
     * }
     * @response 422 {
     *     "message": "The given data was invalid.",
     *     "errors": {
     *         "email": ["The email field is required."],
     *         "name": ["The name field is required."],
     *         "phone": ["The phone field is required."],
     *         "message": ["The message field is required."]
     *     }
     * }
     * @response 500 {
     *     "error": "An unexpected error occurred while processing your message. Please try again.",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @param StoreContactMessageRequest $request
     * @return JsonResponse
     */
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $message = ContactMessage::create([
                'name' => auth()->check() ? auth()->user()->name : $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'subject' => $data['subject'],
                'message' => $data['message'],
                'ip_address' => $request->ip(),
                'user_id' => auth()->id(),
            ]);


            ContactMessageNotifier::notify($message);

            return response()->json([
                'message' => 'Your message has been sent successfully.',
                'data' => $message,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Contact Message API error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An unexpected error occurred while processing your message. Please try again.',
                'support_link' => route('contact.us'),
            ], 500);
        }
    }
}
