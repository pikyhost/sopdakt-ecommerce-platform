<?php

// app/Http/Controllers/AramexWebhookController.php
namespace App\Http\Controllers;

use App\Jobs\ProcessAramexWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AramexWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $data = $request->all();

            // Verify webhook (implement your verification logic based on Aramex documentation)
            if (!$this->verifyWebhook($data)) {
                return response()->json(['error' => 'Invalid webhook'], 403);
            }

            ProcessAramexWebhook::dispatch($data);

            return response()->json(['status' => 'Webhook received']);
        } catch (\Exception $e) {
            Log::error('Aramex webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    protected function verifyWebhook(array $data): bool
    {
        // Implement Aramex webhook verification logic
        // Check for signatures or tokens as per Aramex documentation
        return true; // Replace with actual verification
    }
}
