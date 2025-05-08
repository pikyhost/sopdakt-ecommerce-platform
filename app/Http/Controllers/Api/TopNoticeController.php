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
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $notices,
        ]);
    }
}
