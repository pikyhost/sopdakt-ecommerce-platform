<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceFeature;
use Illuminate\Http\JsonResponse;

class ServiceFeatureController extends Controller
{
    public function index(): JsonResponse
    {
        $features = ServiceFeature::limit(4)->get();

        return response()->json($features);
    }
}
