<?php

namespace App\Helpers;

use App\Models\BlockedPhoneNumber;
use App\Models\Bundle;
use App\Models\BundleSpecialPrice;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductSpecialPrice;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GeneralHelper
{
    private static array $specialPrices = [];
    private static array $bundlePrices = [];
    private static ?int $countryId = null;

    public static function getCountryId(): ?int
    {
        if (self::$countryId === null) {
            $ip = app()->isLocal() ? '188.48.75.12' : request()->ip();
            $countryCode = geoip($ip)['country_code2'] ?? 'US';
            self::$countryId = Country::where('code', $countryCode)->value('id') ?? null;
        }
        return self::$countryId;
    }

    public static function getSenderCountryText(): string
    {
        $countryId = self::getCountryId();
        $countryName = \App\Models\Country::find($countryId)?->name;

        return $countryName
            ? __('messages.sender_from') . ' ' . $countryName
            : __('messages.country_unknown');
    }


    private static function getCurrencyCode(?int $currencyId = null): string
    {
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

    private static function getProductPriceForCountry(Product $product, ?int $countryId): float
    {
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
            ->first(['special_price', 'special_price_after_discount']);

        return (float) ($specialPriceData?->special_price_after_discount
            ?? $specialPriceData?->special_price
            ?? $product->after_discount_price
            ?? $product->price);
    }

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

        // Use a separate cache for discount prices
        if (isset(self::$specialPrices["discount_{$product->id}"])) {
            return self::$specialPrices["discount_{$product->id}"];
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

        // Prioritize discount prices correctly
        $finalPrice = $specialPriceData?->special_price_after_discount
            ?? $specialPriceData?->special_price
            ?? $product->after_discount_price
            ?? $product->price;

        $formattedPrice = self::formatPrice($finalPrice, $specialPriceData?->currency_id ?? $product->currency_id);

        // Store discount price separately
        self::$specialPrices["discount_{$product->id}"] = $formattedPrice;

        return $formattedPrice;
    }



    public static function getBundlePriceForCountry(Bundle $bundle): string
    {
        $countryId = self::getCountryId();

        if (!$countryId) {
            return self::formatBundlePrice($bundle->price, $bundle->currency);
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
            ->first(['special_price', 'currency_id']);

        $finalPrice = $specialPriceData?->special_price ?? $bundle->discount_price ?? $bundle->price;
        $currency = $specialPriceData?->currency_id ?? $bundle->currency;

        self::$bundlePrices[$bundle->id] = self::formatBundlePrice($finalPrice, $currency);
        return self::$bundlePrices[$bundle->id];
    }

    public static function getBundlePriceForCountryWithDiscount(Bundle $bundle): string
    {
        $countryId = self::getCountryId();

        if (!$countryId) {
            return self::formatBundlePrice($bundle->after_discount_price ?? $bundle->price, $bundle->currency);
        }

        if (isset(self::$bundlePrices[$bundle->id])) {
            return self::$bundlePrices[$bundle->id];
        }

        $defaultCurrency = Setting::getCurrency()?->code ?? 'USD';

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
            ->first(['special_price', 'special_price_after_discount', 'currency_id']);

        $finalPrice = $specialPriceData?->special_price_after_discount
            ?? $specialPriceData?->special_price
            ?? $bundle->after_discount_price
            ?? $bundle->discount_price
            ?? $bundle->price;

        $currency = $specialPriceData?->currency_id
            ? Currency::find($specialPriceData->currency_id)?->code
            : $defaultCurrency;

        self::$bundlePrices[$bundle->id] = self::formatBundlePrice($finalPrice, $currency);
        return self::$bundlePrices[$bundle->id];
    }



    public static function getBundlePrice(Bundle $bundle): string
    {
        $countryId = self::getCountryId();

        // Check if already cached
        if (isset(self::$bundlePrices[$bundle->id])) {
            return self::$bundlePrices[$bundle->id];
        }

        // 1️⃣ Check for a special bundle price
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
            ->first(['special_price', 'currency_id']);

        if ($specialPriceData) {
            return self::$bundlePrices[$bundle->id] = self::formatBundlePrice($specialPriceData->special_price, $specialPriceData->currency_id);
        }

        // 2️⃣ If no special bundle price, check bundle discount price
        if ($bundle->discount_price) {
            return self::$bundlePrices[$bundle->id] = self::formatBundlePrice($bundle->discount_price, $bundle->currency);
        }

        // 3️⃣ If no special price or discount, sum the product prices
        $totalPrice = $bundle->products->sum(fn ($product) => self::getProductPriceForCountry($product, $countryId));

        return self::$bundlePrices[$bundle->id] = self::formatBundlePrice($totalPrice, $bundle->currency);
    }

    public static function formatBundlePrice(?float $price, ?string $currency): string
    {
        $price = $price ?? 0.0; // Default to 0 if null
        return number_format($price, 2) . ' ' . ($currency ?? 'USD');
    }

    public static function getCountryCode(): string
    {
        $ip = request()->ip();
        return geoip($ip)['country_code2'] ?? 'US';
    }

    public static function isPhoneBlocked(string $phone): bool
    {
        return BlockedPhoneNumber::where('phone_number', $phone)->exists();
    }

    public static function normalizePhone(string $phone): string
    {
        // Remove leading "+" or "00"
        $phone = ltrim($phone, '+');
        $phone = preg_replace('/^00/', '', $phone);

        // Convert local Egyptian number to international (assuming Egypt example here)
        if (preg_match('/^01[0-9]{8}$/', $phone)) {
            return '20' . substr($phone, 1);
        }

        return $phone;
    }


    // at production
//    public static function getCountryId(): ?int
//    {
//        if (self::$countryId === null) {
//            $ip = request()->ip();
//            $countryCode = geoip($ip)['country_code2'] ?? 'US';
//            self::$countryId = Country::where('code', $countryCode)->value('id') ?? null;
//        }
//        return self::$countryId;
//    }

}
