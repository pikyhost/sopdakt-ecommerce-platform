<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AboutUsController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $about = AboutUs::first();

            if (!$about || $this->isEmpty($about)) {
                return response()->json([
                    'error' => 'About Us data is null or empty',
                    'support_link' => route('contact.us'),
                ], 404);
            }

            $locale = App::getLocale();
            $direction = $locale === 'ar' ? 'rtl' : 'ltr';

            $teamMembers = $locale === 'ar' ? ($about->team_members_ar ?? []) : ($about->team_members ?? []);

            $formattedTeamMembers = collect($teamMembers)->map(function ($member) {
                $imagePath = $member['image'] ?? null;
                $imageUrl = $imagePath ? asset('storage/' . $imagePath) : null;

                return [
                    'name' => $member['name'] ?? null,
                    'image' => $imageUrl,
                ];
            })->filter(function ($member) {
                return !is_null($member['name']) && !is_null($member['image']);
            })->values()->toArray();

            $data = [
                'direction' => $direction,
                'header' => [
                    'label' => $about->background_text ?? null,
                    'title' => $about->header_title ?? null,
                    'subtitle' => $about->header_subtitle ?? null,
                    'background_image' => $about->background_image ? asset('storage/' . $about->background_image) : asset('assets/images/demoes/demo12/page-header-bg.jpg'),
                ],
                'breadcrumbs' => [
                    'home' => [
                        'title' => $about->breadcrumb_home ?? null,
                        'url' => url('/'),
                    ],
                    'current' => [
                        'title' => $about->breadcrumb_current ?? null,
                    ],
                ],
                'founder' => [
                    'label' => $about->founder_label ?? null,
                    'name' => $about->founder_name ?? null,
                    'title' => $about->founder_title ?? null,
                    'image' => $about->founder_image ? asset('storage/' . $about->founder_image) : null,
                ],
                'features' => [
                    [
                        'title' => $about->feature_title_one ?? null,
                        'subtitle' => $about->feature_subtitle_one ?? null,
                        'image' => $about->feature_image_one ? asset('storage/' . $about->feature_image_one) : null,
                    ],
                    [
                        'title' => $about->feature_title_two ?? null,
                        'subtitle' => $about->feature_subtitle_two ?? null,
                        'image' => $about->feature_image_two ? asset('storage/' . $about->feature_image_two) : null,
                    ],
                    [
                        'title' => $about->feature_title_three ?? null,
                        'subtitle' => $about->feature_subtitle_three ?? null,
                        'image' => $about->feature_image_three ? asset('storage/' . $about->feature_image_three) : null,
                    ],
                    [
                        'title' => $about->feature_title_four ?? null,
                        'subtitle' => $about->feature_subtitle_four ?? null,
                        'image' => $about->feature_image_four ? asset('storage/' . $about->feature_image_four) : null,
                    ]
                ],
                'about' => [
                    'title' => $about->about_title ?? null,
                    'description_1' => $about->about_description_1 ?? null,
                    'description_2' => $about->about_description_2 ?? null,
                    'image' => $about->about_image ? asset('storage/' . $about->about_image) : null,
                ],
                'vision_mission' => [
                    'vision_title' => $about->vision_title ?? null,
                    'vision_content' => $about->vision_content ?? null,
                    'mission_title' => $about->mission_title ?? null,
                    'mission_content' => $about->mission_content ?? null,
                    'values_title' => $about->values_title ?? null,
                    'values_content' => $about->values_content ?? null,
                ],
                'accordion' => [
                    [
                        'title' => $about->accordion_title_1 ?? null,
                        'content' => $about->accordion_content_1 ?? null,
                        'is_open' => true,
                    ],
                    [
                        'title' => $about->accordion_title_2 ?? null,
                        'content' => $about->accordion_content_2 ?? null,
                        'is_open' => false,
                    ],
                    [
                        'title' => $about->accordion_title_3 ?? null,
                        'content' => $about->accordion_content_3 ?? null,
                        'is_open' => false,
                    ],
                    [
                        'title' => $about->accordion_title_4 ?? null,
                        'content' => $about->accordion_content_4 ?? null,
                        'is_open' => false,
                    ],
                ],
//                'team' => [
//                    'title' => $about->team_title ?? null,
//                    'members' => $formattedTeamMembers,
//                    'cta' => $about->cta_text && $about->cta_url ? [
//                        'text' => $about->cta_text,
//                        'url' => $about->cta_url,
//                    ] : null,
//                ],
                'seo' => [
                    'meta_title' => $about->meta_title ?? null,
                    'meta_description' => $about->meta_description ?? null,
                ],
            ];

            return response()->json([
                'data' => $data,
                'message' => 'About Us data retrieved successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error('About Us API error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An unexpected error occurred while retrieving About Us data. Please try again.',
                'support_link' => route('contact.us'),
            ], 500);
        }
    }

    /**
     * Check if the AboutUs record is empty or lacks meaningful data
     *
     * @param AboutUs $about
     * @return bool
     */
    private function isEmpty(AboutUs $about): bool
    {
        $translatableFields = [
            'about_title',
            'team_title',
            'testimonial_title',
            'header_title',
            'header_subtitle',
            'breadcrumb_home',
            'breadcrumb_current',
            'about_description_1',
            'about_description_2',
            'accordion_title_1',
            'accordion_content_1',
            'accordion_title_2',
            'accordion_content_2',
            'accordion_title_3',
            'accordion_content_3',
            'accordion_title_4',
            'accordion_content_4',
            'testimonial_content',
            'testimonial_name',
            'testimonial_role',
            'meta_title',
            'meta_description',
            'cta_text',
        ];

        // Check if all translatable fields are null or empty
        foreach ($translatableFields as $field) {
            if (!is_null($about->$field) && trim($about->$field) !== '') {
                return false;
            }
        }

        // Check if non-translatable fields (images, team members, etc.) have data
        if (
            !is_null($about->about_image) ||
            !is_null($about->testimonial_image) ||
            !is_null($about->cta_url) ||
            !empty($about->team_members) ||
            !empty($about->team_members_ar) ||
            !is_null($about->testimonial_rating)
        ) {
            return false;
        }

        return true;
    }
}
