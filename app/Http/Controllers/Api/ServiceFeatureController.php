<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceFeature;
use Illuminate\Http\JsonResponse;

class ServiceFeatureController extends Controller
{
    public function index(): JsonResponse
    {
        $locale = app()->getLocale();

        $features = ServiceFeature::limit(4)->get()->map(function ($feature) use ($locale) {
            return [
                'id' => $feature->id,
                'title' => $feature->getTranslation('title', $locale),
                'subtitle' => $feature->getTranslation('subtitle', $locale),
                'image_url' => asset('storage/' . $feature->icon),
            ];
        });

        return response()->json($features);
    }
}
