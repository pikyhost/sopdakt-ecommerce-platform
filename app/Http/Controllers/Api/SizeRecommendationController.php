<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RecommendSizeRequest;
use App\Models\SizeGuide;
use App\Models\SizeRecommendation;
use App\Services\SizeRecommendationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SizeRecommendationController extends Controller
{
    public function __construct(protected SizeRecommendationService $service) {}

    public function recommend(RecommendSizeRequest $request)
    {
        $data = $request->validated();

        // Get detailed recommendation
        $recommendation = $this->service->getDetailedRecommendation(
            $data['height'],
            $data['weight'],
            $data['age'] ?? null,
            $data['shoulder_width']
        );

        $sessionId = Session::getId();

        // Save to DB
        SizeRecommendation::create([
            'user_id' => Auth::id() ?? null,
            'session_id' => $sessionId,
            'height' => $data['height'],
            'weight' => $data['weight'],
            'age' => $data['age'] ?? null,
            'shoulder_width' => $data['shoulder_width'],
            'recommended_size' => $recommendation['size'],
        ]);

        // Get the size guide image for the recommended size
        $sizeGuide = SizeGuide::whereHas('size', function($query) use ($recommendation) {
            $query->where('name', $recommendation['size']);
        })->first();

        $imageUrl = $sizeGuide ? asset('storage/' . $sizeGuide->image_path) : null;

        return response()->json([
            'recommended_size' => $recommendation['size'],
            'image_url' => $imageUrl,
//            'fit_score' => round($recommendation['fit_score'] * 100),
//            'explanation' => $recommendation['explanation'],
            'message' => "Recommended size: {$recommendation['size']}",
        ]);
    }
}
