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
