<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    /**
     * Retrieve banners of type 'product'
     *
     * This endpoint fetches all banners with the type 'product' in the current application locale (English or Arabic).
     * The response includes an array of banners with their title, subtitle, discount, button text, button URL, image URL,
     * and type. Translatable fields (title, subtitle, discount, button_text) are returned in the current locale.
     * If no product banners are found, an error message is returned indicating that the banners are null or empty.
     *
     * @group Banners
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "title": "50% OFF",
     *             "subtitle": "UP TO",
     *             "discount": "50%",
     *             "button_text": "SHOP NOW",
     *             "button_url": "/shop",
     *             "image": "https://your-domain.com/assets/images/menu-banner.jpg",
     *             "type": "product",
     *             "created_at": "2025-04-30T12:00:00.000000Z",
     *             "updated_at": "2025-04-30T12:00:00.000000Z"
     *         }
     *     ],
     *     "message": "Product banners retrieved successfully"
     * }
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 1,
     *             "title": "خصم 50%",
     *             "subtitle": "حتى",
     *             "discount": "٥٠٪",
     *             "button_text": "تسوق الآن",
     *             "button_url": "/shop",
     *             "image": "https://your-domain.com/assets/images/menu-banner.jpg",
     *             "type": "product",
     *             "created_at": "2025-04-30T12:00:00.000000Z",
     *             "updated_at": "2025-04-30T12:00:00.000000Z"
     *         }
     *     ],
     *     "message": "Product banners retrieved successfully"
     * }
     * @response 404 {
     *     "error": "No product banners found",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @response 500 {
     *     "error": "An unexpected error occurred while retrieving product banners. Please try again.",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @return JsonResponse
     */
    public function product(): JsonResponse
    {
        return $this->getBanners('product');
    }

    /**
     * Retrieve banners of type 'category'
     *
     * This endpoint fetches all banners with the type 'category' in the current application locale (English or Arabic).
     * The response includes an array of banners with their title, subtitle, discount, button text, button URL, image URL,
     * and type. Translatable fields (title, subtitle, discount, button_text) are returned in the current locale.
     * If no category banners are found, an error message is returned indicating that the banners are null or empty.
     *
     * @group Banners
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 2,
     *             "title": "New Arrivals",
     *             "subtitle": "EXPLORE",
     *             "discount": null,
     *             "button_text": "VIEW CATEGORIES",
     *             "button_url": "/categories",
     *             "image": "https://your-domain.com/assets/images/category-banner.jpg",
     *             "type": "category",
     *             "created_at": "2025-04-30T12:00:00.000000Z",
     *             "updated_at": "2025-04-30T12:00:00.000000Z"
     *         }
     *     ],
     *     "message": "Category banners retrieved successfully"
     * }
     * @response 200 {
     *     "data": [
     *         {
     *             "id": 2,
     *             "title": "وصل حديثاً",
     *             "subtitle": "استكشاف",
     *             "discount": null,
     *             "button_text": "عرض الفئات",
     *             "button_url": "/categories",
     *             "image": "https://your-domain.com/assets/images/category-banner.jpg",
     *             "type": "category",
     *             "created_at": "2025-04-30T12:00:00.000000Z",
     *             "updated_at": "2025-04-30T12:00:00.000000Z"
     *         }
     *     ],
     *     "message": "Category banners retrieved successfully"
     * }
     * @response 404 {
     *     "error": "No category banners found",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @response 500 {
     *     "error": "An unexpected error occurred while retrieving category banners. Please try again.",
     *     "support_link": "https://your-domain.com/contact-us"
     * }
     * @return JsonResponse
     */
    public function category(): JsonResponse
    {
        return $this->getBanners('category');
    }

    /**
     * Helper method to retrieve banners by type
     *
     * Fetches banners of the specified type and returns a JSON response with the banner data.
     *
     * @param string $type The type of banners to retrieve (product or category)
     * @return JsonResponse
     */
    private function getBanners(string $type): JsonResponse
    {
        try {
            // Fetch banners by type
            $banners = Banner::where('type', $type)->get();

            // Check if banners are empty
            if ($banners->isEmpty()) {
                $typeName = ucfirst($type);
                return response()->json([
                    'error' => "No {$type} banners found",
                    'support_link' => route('contact.us'),
                ], 404);
            }

            // Format banners with image URLs
            $formattedBanners = $banners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle,
                    'discount' => $banner->discount,
                    'button_text' => $banner->button_text,
                    'button_url' => $banner->button_url,
                    'image' => asset($banner->image),
                    'type' => $banner->type,
                    'created_at' => $banner->created_at->toIso8601String(),
                    'updated_at' => $banner->updated_at->toIso8601String(),
                ];
            })->toArray();

            return response()->json([
                'data' => $formattedBanners,
                'message' => ucfirst($type) . ' banners retrieved successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error(ucfirst($type) . ' Banners API error: ' . $e->getMessage());
            $typeName = ucfirst($type);
            return response()->json([
                'error' => "An unexpected error occurred while retrieving {$type} banners. Please try again.",
                'support_link' => route('contact.us'),
            ], 500);
        }
    }
}
