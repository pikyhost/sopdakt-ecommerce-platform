<?php
// app/Http/Controllers/WhatsAppController.php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function sendTextMessage(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $response = $this->whatsAppService->sendTextMessage(
            $validated['phone'],
            $validated['message']
        );

        return response()->json($response);
    }

    public function sendTemplateMessage(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'template_name' => 'required|string',
        ]);

        $response = $this->whatsAppService->sendTemplateMessage(
            $validated['phone'],
            $validated['template_name'],
            $request->input('components', [])
        );

        return response()->json($response);
    }

    // Webhook verification
    public function verifyWebhook(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $verifyToken = env('WHATSAPP_VERIFY_TOKEN', 'your_verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        }

        return response()->json(['error' => 'Invalid verification'], 403);
    }

    // Handle incoming messages
    public function handleWebhook(Request $request)
    {
        $data = $request->all();

        // Process the incoming message
        Log::info('WhatsApp Webhook Data:', $data);

        // Add your business logic here
        // For example, reply to messages, update database, etc.

        return response()->json(['success' => true]);
    }
}
