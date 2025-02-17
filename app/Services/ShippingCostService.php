<?php

namespace App\Services;

use App\Models\Product;
use App\Models\City;
use App\Models\Governorate;
use App\Models\ShippingZone;
use App\Models\Country;
use App\Models\CountryGroup;

class ShippingCostService
{
    public function getShippingCost(Product $product, ?int $cityId, ?int $governorateId, ?int $shippingZoneId, ?int $countryId, ?int $countryGroupId): array
    {
        // 1 - Check shippingCosts table for the product based on priority
        $shippingCost = $product->shippingCosts()
            ->where(function ($query) use ($cityId, $governorateId, $shippingZoneId, $countryId, $countryGroupId) {
                $query->when($cityId, fn($q) => $q->where('city_id', $cityId))
                    ->when($governorateId, fn($q) => $q->where('governorate_id', $governorateId))
                    ->when($shippingZoneId, fn($q) => $q->where('shipping_zone_id', $shippingZoneId))
                    ->when($countryId, fn($q) => $q->where('country_id', $countryId))
                    ->when($countryGroupId, fn($q) => $q->where('country_group_id', $countryGroupId));
            })
            ->orderByRaw("
                CASE
                    WHEN city_id IS NOT NULL THEN 1
                    WHEN governorate_id IS NOT NULL THEN 2
                    WHEN shipping_zone_id IS NOT NULL THEN 3
                    WHEN country_id IS NOT NULL THEN 4
                    WHEN country_group_id IS NOT NULL THEN 5
                    ELSE 6
                END
            ")
            ->first();

        if ($shippingCost) {
            return [
                'cost' => $shippingCost->cost,
                'shipping_estimate_time' => $shippingCost->shipping_estimate_time,
            ];
        }

        // 2- Fallback: Use product's own cost fields
        if (!is_null($product->cost) && !is_null($product->shipping_estimate_time)) {
            return [
                'cost' => $product->cost,
                'shipping_estimate_time' => $product->shipping_estimate_time,
            ];
        }

        // 3- Fallback: Retrieve values from the place's table
        return $this->getFallbackLocationCost($cityId, $governorateId, $shippingZoneId, $countryId, $countryGroupId);
    }

    private function getFallbackLocationCost(?int $cityId, ?int $governorateId, ?int $shippingZoneId, ?int $countryId, ?int $countryGroupId): array
    {
        if ($cityId) {
            $city = City::find($cityId);
            if ($city && !is_null($city->cost) && !is_null($city->shipping_estimate_time)) {
                return ['cost' => $city->cost, 'shipping_estimate_time' => $city->shipping_estimate_time];
            }
        }

        if ($governorateId) {
            $governorate = Governorate::find($governorateId);
            if ($governorate && !is_null($governorate->cost) && !is_null($governorate->shipping_estimate_time)) {
                return ['cost' => $governorate->cost, 'shipping_estimate_time' => $governorate->shipping_estimate_time];
            }
        }

        if ($shippingZoneId) {
            $shippingZone = ShippingZone::find($shippingZoneId);
            if ($shippingZone && !is_null($shippingZone->cost) && !is_null($shippingZone->shipping_estimate_time)) {
                return ['cost' => $shippingZone->cost, 'shipping_estimate_time' => $shippingZone->shipping_estimate_time];
            }
        }

        if ($countryId) {
            $country = Country::find($countryId);
            if ($country && !is_null($country->cost) && !is_null($country->shipping_estimate_time)) {
                return ['cost' => $country->cost, 'shipping_estimate_time' => $country->shipping_estimate_time];
            }
        }

        if ($countryGroupId) {
            $countryGroup = CountryGroup::find($countryGroupId);
            if ($countryGroup && !is_null($countryGroup->cost) && !is_null($countryGroup->shipping_estimate_time)) {
                return ['cost' => $countryGroup->cost, 'shipping_estimate_time' => $countryGroup->shipping_estimate_time];
            }
        }

        // Default fallback if all else fails
        return ['cost' => 0, 'shipping_estimate_time' => '0-0'];
    }
}
