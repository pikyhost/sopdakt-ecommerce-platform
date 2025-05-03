<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

class DiscountController extends Controller
{
    /**
     * Display a paginated list of active discounts with filtering, sorting, and search.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Validate request parameters
        $validator = Validator::make($request->all(), [
            'locale' => 'string|regex:/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/',
            'search' => 'string|max:255',
            'discount_type' => 'in:percentage,fixed,free_shipping',
            'applies_to' => 'in:product,category,cart,collection',
            'sort' => 'in:created_at,starts_at,ends_at,value,price',
            'direction' => 'in:asc,desc',
            'per_page' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid parameters',
                'details' => $validator->errors(),
            ], 422);
        }

        $locale = $request->input('locale', app()->getLocale());
        $query = $this->buildQuery($request, $locale);
        $discounts = $this->paginateResults($query, $request);

        return response()->json([
            'data' => $this->transformDiscounts($discounts, $locale),
            'meta' => [
                'current_page' => $discounts->currentPage(),
                'last_page' => $discounts->lastPage(),
                'per_page' => $discounts->perPage(),
                'total' => $discounts->total(),
            ],
        ]);
    }

    /**
     * Build the query for fetching discounts with filters and search.
     *
     * @param Request $request
     * @param string $locale
     * @return Builder
     */
    private function buildQuery(Request $request, string $locale): Builder
    {
        $query = Discount::query()
            ->select([
                'id', 'name', 'description', 'discount_type', 'applies_to',
                'value', 'price', 'after_discount_price', 'min_order_value',
                'starts_at', 'ends_at', 'usage_limit', 'requires_coupon',
                'is_active', 'created_at', 'updated_at'
            ])
            ->with([
                'products' => fn($q) => $q->select('products.id', 'name', 'slug'),
                'categories' => fn($q) => $q->select('categories.id', 'name', 'slug'),
            ])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });

        // Search by name or description
        if ($search = $request->input('search')) {
            $searchTerm = '%' . strtolower(trim($search)) . '%';
            $query->where(function ($q) use ($searchTerm, $locale) {
                $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(name, '$.\"{$locale}\"'))) LIKE ?", [$searchTerm])
                    ->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(description, '$.\"{$locale}\"'))) LIKE ?", [$searchTerm]);
            });
        }

        // Filtering
        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->input('discount_type'));
        }

        if ($request->filled('applies_to')) {
            $query->where('applies_to', $request->input('applies_to'));
        }

        // Sorting
        $sort = in_array($request->input('sort'), ['created_at', 'starts_at', 'ends_at', 'value', 'price'])
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $direction);

        return $query;
    }

    /**
     * Paginate the query results.
     *
     * @param Builder $query
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function paginateResults(Builder $query, Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $perPage = $request->integer('per_page', 15);
        return $query->paginate(min($perPage, 50)); // Cap at 50 for performance
    }

    /**
     * Transform discounts into API response format.
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $discounts
     * @param string $locale
     * @return array
     */
    private function transformDiscounts($discounts, string $locale): array
    {
        return $discounts->map(function ($discount) use ($locale) {
            return [
                'id' => $discount->id,
                'name' => $discount->getTranslation('name', $locale) ?? '',
                'description' => $discount->getTranslation('description', $locale) ?? null,
                'discount_type' => $discount->discount_type,
                'applies_to' => $discount->applies_to,
                'value' => $discount->value !== null ? (float) $discount->value : null,
                'price' => (int) $discount->price,
                'after_discount_price' => $discount->after_discount_price !== null ? (int) $discount->after_discount_price : null,
                'min_order_value' => $discount->min_order_value !== null ? (float) $discount->min_order_value : null,
                'starts_at' => optional($discount->starts_at)->toIso8601String(),
                'ends_at' => optional($discount->ends_at)->toIso8601String(),
                'usage_limit' => $discount->usage_limit !== null ? (int) $discount->usage_limit : null,
                'requires_coupon' => $discount->requires_coupon,
                'is_active' => $discount->is_active,
                'products' => $discount->products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                ])->toArray(),
                'categories' => $discount->categories->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                ])->toArray(),
                'created_at' => $discount->created_at->toIso8601String(),
                'updated_at' => $discount->updated_at->toIso8601String(),
            ];
        })->toArray();
    }
}
