<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TopNotice;
use Illuminate\Http\Request;

class TopNoticeController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale(); // 'en' or 'ar'
        $suffix = $locale === 'ar' ? '_ar' : '_en';

        $notices = TopNotice::where('is_active', true)
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($notice) use ($suffix) {
                return [
                    'content' => $notice->{'content' . $suffix},
                    'cta_text' => $notice->{'cta_text' . $suffix},
                    'cta_url' => $notice->cta_url,
                    'cta_text_2' => $notice->{'cta_text_2' . $suffix},
                    'cta_url_2' => $notice->cta_url_2,
                    'limited_time_text' => $notice->{'limited_time_text' . $suffix},
                    'header_message' => $notice->{'header_message' . $suffix},
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $notices,
        ]);
    }
}
