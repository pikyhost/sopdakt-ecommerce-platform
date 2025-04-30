<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageNotifier;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    public function store(StoreContactMessageRequest $request): JsonResponse
    {
        $data = $request->validated();

        $message = ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'subject' => $data['subject'] ?? null,
            'message' => $data['message'],
            'ip_address' => $request->ip(),
            'user_id' => auth()->id() ?? null,
        ]);

        ContactMessageNotifier::notify($message);

        return response()->json([
            'message' => __('contact.success_message'),
            'data' => $message,
        ], 201);
    }
}
