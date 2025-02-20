<?php

namespace App\Helpers;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductSpecialPrice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GeneralHelper
{
    protected static ?int $countryId = null;
    protected static array $specialPrices = [];
    protected static array $specialPricesWithDiscount = [];

    protected static function getCountryId(): ?int
    {
        if (is_null(self::$countryId)) {
            $ip = app()->isLocal() ? '82.205.219.30' : request()->ip(); // EGYPT IP Example
            $countryCode = geoip($ip)['country_code2'] ?? 'US'; // Default to 'US' if geoip fails
            self::$countryId = Country::where('code', $countryCode)->value('id') ?? null;
        }

        return self::$countryId;
    }

    public static function getPriceForCountry(Product $product): string
    {
        $countryId = self::getCountryId();

        if (!$countryId) {
            return number_format($product->price, 2) . ' ' . ($product->currency->code ?? 'USD');
        }

        if (isset(self::$specialPrices[$product->id])) {
            return self::$specialPrices[$product->id];
        }

        $specialPriceData = ProductSpecialPrice::where('product_id', $product->id)
            ->where(function ($query) use ($countryId) {
                $query->where('country_id', $countryId)
                    ->orWhere(function ($subQuery) use ($countryId) {
                        $subQuery->whereNull('country_id')
                            ->whereExists(function ($existsQuery) use ($countryId) {
                                $existsQuery->select(DB::raw(1))
                                    ->from('country_group_country')
                                    ->whereRaw('product_special_prices.country_group_id = country_group_country.country_group_id')
                                    ->where('country_group_country.country_id', $countryId);
                            });
                    });
            })
            ->orderByRaw('CASE WHEN country_id IS NOT NULL THEN 1 ELSE 2 END')
            ->first(['special_price', 'currency_id']);

        $currencyCode = $specialPriceData?->currency_id
            ? Currency::where('id', $specialPriceData->currency_id)->value('code')
            : ($product->currency->code ?? 'USD');

        $formattedPrice = number_format($specialPriceData->special_price ?? $product->price, 2) . ' ' . $currencyCode;

        self::$specialPrices[$product->id] = $formattedPrice;

        return $formattedPrice;
    }

    public static function getPriceForCountryWithDiscount(Product $product): string
    {
        $countryId = self::getCountryId();

        if (!$countryId) {
            return number_format($product->after_discount_price ?? $product->price, 2) . ' ' . ($product->currency->code ?? 'USD');
        }

        if (isset(self::$specialPricesWithDiscount[$product->id])) {
            return self::$specialPricesWithDiscount[$product->id];
        }

        $specialPriceData = ProductSpecialPrice::where('product_id', $product->id)
            ->where(function ($query) use ($countryId) {
                $query->where('country_id', $countryId)
                    ->orWhere(function ($subQuery) use ($countryId) {
                        $subQuery->whereNull('country_id')
                            ->whereExists(function ($existsQuery) use ($countryId) {
                                $existsQuery->select(DB::raw(1))
                                    ->from('country_group_country')
                                    ->whereRaw('product_special_prices.country_group_id = country_group_country.country_group_id')
                                    ->where('country_group_country.country_id', $countryId);
                            });
                    });
            })
            ->orderByRaw('CASE WHEN country_id IS NOT NULL THEN 1 ELSE 2 END')
            ->first(['special_price', 'special_price_after_discount', 'currency_id']);

        $finalPrice = $specialPriceData?->special_price_after_discount ?? $specialPriceData?->special_price ?? $product->after_discount_price ?? $product->price;

        $currencyCode = $specialPriceData?->currency_id
            ? Currency::where('id', $specialPriceData->currency_id)->value('code')
            : ($product->currency->code ?? 'USD');

        $formattedPrice = number_format($finalPrice, 2) . ' ' . $currencyCode;

        self::$specialPricesWithDiscount[$product->id] = $formattedPrice;

        return $formattedPrice;
    }


    /**
     * @throws \Exception
     */
    public function getClientCountryCode(): string
    {
        return geoip()->getLocation(request()->ip())['iso_code'] ?? 'US';
    }

    /**
     * @throws \Exception
     */
    public function getClientCountryName(): string
    {
        return geoip()->getLocation(request()->ip())['country'] ?? 'United States';
    }

    public static function getDefaultUserImageUrl(User $record): string
    {
        // Common titles to ignore
        $titles = ['Mr.', 'Mrs.', 'Ms.', 'Dr.', 'Prof.'];

        // Split the name into parts by space
        $nameParts = explode(' ', trim($record->name));

        // Remove any title that matches the list of common titles
        $filteredParts = array_filter($nameParts, function ($part) use ($titles) {
            return ! in_array($part, $titles);
        });

        // Reset the array keys after filtering
        $filteredParts = array_values($filteredParts);

        // Determine the initials based on the filtered name parts
        if (count($filteredParts) === 1) {
            // Single part name, take the first two characters
            $initials = mb_strtoupper(mb_substr($filteredParts[0], 0, 2));
        } elseif (count($filteredParts) > 1) {
            // Multi-part name, take the first character of the first and second words
            $initials = mb_strtoupper(mb_substr($filteredParts[0], 0, 1).mb_substr($filteredParts[1], 0, 1));
        } else {
            // Default fallback (if the name is empty or only contains titles)
            $initials = 'NN';
        }

        // Generate the avatar URL with the initials
        return 'https://ui-avatars.com/api/?background=000000&color=fff&name='.urlencode($initials);
    }

    public static function formatCategoryName($record): string
    {
        $parentName = $record?->product->category?->parent?->name;
        $categoryName = $record?->product->category?->name;

        return $parentName ? "{$parentName} -> {$categoryName}" : $categoryName;
    }

    public static function shouldShowTooltip($record): ?string
    {
        $formattedName = self::formatCategoryName($record);

        return strlen($formattedName) > 20 ? $formattedName : null;
    }
}



/*
 *

Here are four example IP addresses for the requested countries:

Egypt:    156.221.68.3
England (UK): 51.140.123.45
Saudi Arabia: 212.102.4.15
United Arab Emirates (UAE): 94.200.123.77
England (UK): 51.145.89.32
Norway (NO): 84.208.20.110


 * */
