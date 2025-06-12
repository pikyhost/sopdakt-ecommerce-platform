<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Support\Facades\App;

class FaqController extends Controller
{
    public function index()
    {
        $locale = App::getLocale();

        $faq = Faq::where('locale', $locale)->first();

        if (!$faq) {
            return response()->json([
                'message' => 'FAQ not found for locale: ' . $locale,
            ], 404);
        }

        return response()->json([
            'locale' => $faq->locale,
            'items' => $faq->items,
        ]);
    }
}
