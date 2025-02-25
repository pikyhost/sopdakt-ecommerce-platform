<?php

namespace App\Helpers;

use App\Models\Bundle;
use App\Models\BundleSpecialPrice;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductSpecialPrice;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneralHelper
{
    private static array $currencyCache = [];
    private static array $specialPrices = [];
    private static array $bundlePrices = [];
    private static ?int $countryId = null;

    public static function getPriceForCountry(Product $product): string
    {
        $countryId = self::getCountryId();

        if (!$countryId) {
            return self::formatPrice($product->price, $product->currency_id);
        }

        if (isset(self::$specialPrices[$product->id])) {
            return self::$specialPrices[$product->id];
        }

        $specialPriceData = ProductSpecialPrice::where('product_id', $product->id)
            ->where(function ($query) use ($countryId) {
                $query->where('country_id', $countryId)
                    ->orWhereNull('country_id')
                    ->whereExists(fn ($existsQuery) => $existsQuery->select(DB::raw(1))
                        ->from('country_group_country')
                        ->whereRaw('product_special_prices.country_group_id = country_group_country.country_group_id')
                        ->where('country_group_country.country_id', $countryId)
                    );
            })
            ->orderByRaw('CASE WHEN country_id IS NOT NULL THEN 1 ELSE 2 END')
            ->first(['special_price', 'currency_id']);

        $finalPrice = $specialPriceData?->special_price ?? $product->price;
        $formattedPrice = self::formatPrice($finalPrice, $specialPriceData?->currency_id ?? $product->currency_id);

        self::$specialPrices[$product->id] = $formattedPrice;
        return $formattedPrice;
    }

    public static function getPriceForCountryWithDiscount(Product $product): string
    {
        $countryId = self::getCountryId();

        if (!$countryId) {
            return self::formatPrice($product->after_discount_price ?? $product->price, $product->currency_id);
        }

        if (isset(self::$specialPrices[$product->id])) {
            return self::$specialPrices[$product->id];
        }

        $specialPriceData = ProductSpecialPrice::where('product_id', $product->id)
            ->where(function ($query) use ($countryId) {
                $query->where('country_id', $countryId)
                    ->orWhereNull('country_id')
                    ->whereExists(fn ($existsQuery) => $existsQuery->select(DB::raw(1))
                        ->from('country_group_country')
                        ->whereRaw('product_special_prices.country_group_id = country_group_country.country_group_id')
                        ->where('country_group_country.country_id', $countryId)
                    );
            })
            ->orderByRaw('CASE WHEN country_id IS NOT NULL THEN 1 ELSE 2 END')
            ->first(['special_price', 'special_price_after_discount', 'currency_id']);

        $finalPrice = $specialPriceData?->special_price_after_discount
            ?? $specialPriceData?->special_price
            ?? $product->after_discount_price
            ?? $product->price;

        $formattedPrice = self::formatPrice($finalPrice, $specialPriceData?->currency_id ?? $product->currency_id);
        self::$specialPrices[$product->id] = $formattedPrice;
        return $formattedPrice;
    }

    public static function getBundlePriceForCountry(Bundle $bundle): float
    {
        $countryId = self::getCountryId();

        if (!$countryId) {
            return (float) $bundle->price;
        }

        if (isset(self::$bundlePrices[$bundle->id])) {
            return self::$bundlePrices[$bundle->id];
        }

        $specialPriceData = BundleSpecialPrice::where('bundle_id', $bundle->id)
            ->where(function ($query) use ($countryId) {
                $query->where('country_id', $countryId)
                    ->orWhereNull('country_id')
                    ->whereExists(fn ($existsQuery) => $existsQuery->select(DB::raw(1))
                        ->from('country_group_country')
                        ->whereRaw('bundle_special_prices.country_group_id = country_group_country.country_group_id')
                        ->where('country_group_country.country_id', $countryId)
                    );
            })
            ->orderByRaw('CASE WHEN country_id IS NOT NULL THEN 1 ELSE 2 END')
            ->first(['special_price']);

        $finalPrice = $specialPriceData?->special_price ?? $bundle->discount_price ?? $bundle->price;
        self::$bundlePrices[$bundle->id] = $finalPrice;
        return (float) $finalPrice;
    }

    public static function getBundlePriceForCountryWithDiscount(Bundle $bundle): float
    {
        $countryId = self::getCountryId();

        if (!$countryId) {
            return (float) ($bundle->after_discount_price ?? $bundle->price);
        }

        if (isset(self::$bundlePrices[$bundle->id])) {
            return self::$bundlePrices[$bundle->id];
        }

        $specialPriceData = BundleSpecialPrice::where('bundle_id', $bundle->id)
            ->where(function ($query) use ($countryId) {
                $query->where('country_id', $countryId)
                    ->orWhereNull('country_id')
                    ->whereExists(fn ($existsQuery) => $existsQuery->select(DB::raw(1))
                        ->from('country_group_country')
                        ->whereRaw('bundle_special_prices.country_group_id = country_group_country.country_group_id')
                        ->where('country_group_country.country_id', $countryId)
                    );
            })
            ->orderByRaw('CASE WHEN country_id IS NOT NULL THEN 1 ELSE 2 END')
            ->first(['special_price', 'special_price_after_discount']);

        $finalPrice = $specialPriceData?->special_price_after_discount
            ?? $specialPriceData?->special_price
            ?? $bundle->after_discount_price
            ?? $bundle->discount_price
            ?? $bundle->price;

        self::$bundlePrices[$bundle->id] = (float) $finalPrice;
        return (float) $finalPrice;
    }

    public static function getBundlePriceForCountryFormatted(Bundle $bundle): string
    {
        return self::formatPrice(self::getBundlePriceForCountry($bundle), $bundle->currency_id);
    }

    public static function getBundlePriceForCountryWithDiscountFormatted(Bundle $bundle): string
    {
        return self::formatPrice(self::getBundlePriceForCountryWithDiscount($bundle), $bundle->currency_id);
    }

    private static function getCountryId(): ?int
    {
        if (self::$countryId === null) {
            $ip = app()->isLocal() ? '156.221.68.3' : request()->ip();
            $countryCode = geoip($ip)['country_code2'] ?? 'US';
            self::$countryId = Country::where('code', $countryCode)->value('id') ?? null;
        }
        return self::$countryId;
    }

    private static function getCurrencyCode(?int $currencyId = null): string
    {
        // If currency ID is not provided, retrieve from cache or settings
        $currencyId ??= Cache::rememberForever('default_currency_id', fn () => Setting::getSetting('currency_id'));

        // If currency ID is still null, return default 'USD'
        if (!$currencyId) {
            return 'USD';
        }

        // Retrieve currency code from cache or database
        return Cache::rememberForever("currency_code_{$currencyId}", function () use ($currencyId) {
            return Currency::find($currencyId)?->code ?? 'USD';
        });
    }


    private static function formatPrice(float $amount, ?int $currencyId): string
    {
        return number_format($amount, 2) . ' ' . self::getCurrencyCode($currencyId);
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
