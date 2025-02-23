<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipping\CalculateShippingRequest;
use App\Models\{LandingPage, Region, ShippingType};

class ShippingController extends Controller
{
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
