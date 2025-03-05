<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\{LandingPage, Region, ShippingType};
use App\Http\Requests\Shipping\CalculateShippingRequest;

class ShippingController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        Log::info('JT Express Webhook', $data);
        return response()->json(['message' => 'Webhook received successfully.']);
    }

    function calculateShipping(CalculateShippingRequest $request)
    {
        try {
            $region = Region::find($request->region_id);
            $landingPage = LandingPage::find($request->landing_page_id);
            $shippingType = ShippingType::find($request->shipping_type_id);

            $shippingCost = $landingPage->shippingCost($region, $shippingType);

            return response()->json([
                'shipping_cost' => $shippingCost,
                'message'       => 'Shipping cost calculated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while calculating shipping cost.',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}
