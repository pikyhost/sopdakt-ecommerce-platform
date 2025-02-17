<?php

namespace App\Services;

use App\Models\ShippingCost;
use App\Models\ShippingZone;

class ShippingCostService
{
    /**
     * Get the nearest applicable shipping cost for a product based on the priority:
     * City → Governorate → Shipping Zone → Country → Country Group.
     *
     * @param int $productId The product to get the shipping cost for.
     * @param int|null $cityId The city ID of the destination.
     * @param int|null $governorateId The governorate ID of the destination.
     * @param int|null $countryId The country ID of the destination.
     * @param int|null $countryGroupId The country group ID of the destination.
     * @return ShippingCost|null The best available shipping cost or null if none found.
     */
    public function getBestShippingCost(int $productId, ?int $cityId, ?int $governorateId, ?int $countryId, ?int $countryGroupId): ?ShippingCost
    {
        return ShippingCost::where('product_id', $productId)
            ->where(function ($query) use ($cityId, $governorateId, $countryId, $countryGroupId) {
                $query->when($cityId, fn($q) => $q->orWhere('city_id', $cityId))
                    ->when($governorateId, fn($q) => $q->orWhere('governorate_id', $governorateId))
                    ->when($countryId, fn($q) => $q->orWhere('country_id', $countryId))
                    ->when($countryGroupId, fn($q) => $q->orWhere('country_group_id', $countryGroupId));

                // Check Shipping Zones linked to Governorates
                if ($governorateId) {
                    $query->orWhereHas('shippingZone', function ($q) use ($governorateId) {
                        $q->whereHas('governorates', fn($q2) => $q2->where('id', $governorateId));
                    });
                }
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
    }
}
