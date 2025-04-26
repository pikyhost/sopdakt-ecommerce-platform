<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShippingController;
use Illuminate\Support\Facades\Log;


Route::get('/categories/art', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('jt-express-webhook', [ShippingController::class, 'handleWebhook']);

Route::post('/payment/process', [PaymentController::class, 'paymentProcess']);
Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);

Route::prefix('whatsapp')->group(function () {
    Route::post('/send-text', [WhatsAppController::class, 'sendTextMessage']);
    Route::post('/send-template', [WhatsAppController::class, 'sendTemplateMessage']);

    // Webhook routes
    Route::get('/webhook', [WhatsAppController::class, 'verifyWebhook']);
    Route::post('/webhook', [WhatsAppController::class, 'handleWebhook']);
});


Route::post('/test-wapilot', function (Request $request) {
    $apiToken = config('services.wapilot.api_token');
    $phone = $request->input('phone', '+201025263865'); // Default phone, override via request
    $message = $request->input('message', 'Test message from Wapilot API');
    $url = config('services.wapilot.api_url');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiToken,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'phone' => $phone,
        'message' => $message,
    ]));
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output
    $verboseFile = storage_path('logs/curl_verbose.log');
    $verbose = fopen($verboseFile, 'a');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    fclose($verbose);

    // Read verbose log
    $verboseLog = file_get_contents($verboseFile);

    curl_close($ch);

    // Prepare response
    $result = [
        'http_code' => $httpCode,
        'response' => $response ? json_decode($response, true) : null,
        'curl_error' => $curlError ?: null,
        'verbose_log' => $verboseLog,
    ];

    // Log for debugging
    Log::info('Wapilot API test', $result);

    return response()->json($result);
});
