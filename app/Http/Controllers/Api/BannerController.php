<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product; // Assuming you have a Product model
use App\Models\Category; // Assuming you have a Category model
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    // ... (keep all the existing docblocks and methods)

    /**
     * Helper method to retrieve banners by type with related items
     *
     * Fetches banners of the specified type with related products/categories and returns a JSON response.
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

            // Format banners with image URLs and related items
            $formattedBanners = $banners->map(function ($banner) use ($type) {
                $data = [
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

                // Add related items based on banner type
                if ($type === 'product') {
                    $data['products'] = Product::latest()
                        ->take(6)
                        ->get()
                        ->map(function ($product) {
                            return [
                                'id' => $product->id,
                                'name' => $product->getTranslation('name', app()->getLocale()),
                                'slug' => $product->slug,
                            ];
                        });
                } elseif ($type === 'category') {
                    $data['categories'] = Category::latest()
                        ->take(6)
                        ->get()
                        ->map(function ($category) {
                            return [
                                'id' => $category->id,
                                'name' => $category->getTranslation('name', app()->getLocale()),
                                'slug' => $category->slug,
                            ];
                        });
                }

                return $data;
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
