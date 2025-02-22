<?php

namespace App\Http\Controllers\Api;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegionsController extends Controller
{
    function index(Request $request)
    {
        $governorateId = $request->query('governorate_id');

        if ($governorateId) {
            $regions= Region::where('governorate_id', $governorateId)->get();
            return response()->json($regions);
        }

        return response()->json(null);
    }
}
