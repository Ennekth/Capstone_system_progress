<?php

namespace App\Services;

use App\Models\Product;
use App\Models\EoqSetting;

class EoqService
{
    public function holdingCost(Product $product): float
    {
        $latest = $product->latestOrderingCost;

        if ($latest && $latest->holding_cost_override !== null) {
            return (float) $latest->holding_cost_override;
        }

        $settings = EoqSetting::first();

        if (!$settings) {
            // Fallback even with no settings row at all
            return round(($product->purchase_cost ?? 0) * 0.20, 2);
        }

        if ($product->storage_volume_cbm !== null && $settings->holding_cost_per_cbm > 0) {
            return round($product->storage_volume_cbm * $settings->holding_cost_per_cbm, 2);
        }

        $percentage = $settings->holding_cost_percentage / 100;
        return round(($product->purchase_cost ?? 0) * $percentage, 2);
    }

    public function orderingCost(Product $product): float
    {
        $latest = $product->latestOrderingCost;

        if ($latest && $latest->ordering_cost !== null) {
            return (float) $latest->ordering_cost;
        }

        $settings = EoqSetting::first();

        if (!$settings) {
            return round(($product->purchase_cost ?? 0) * 0.10, 2);
        }

        if ($settings->default_ordering_cost !== null) {
            return (float) $settings->default_ordering_cost;
        }

        $percentage = $settings->ordering_cost_percentage / 100;
        return round(($product->purchase_cost ?? 0) * $percentage, 2);
    }

    public function calculate(Product $product, float $annualDemand): float
    {
        $holdingCost = $this->holdingCost($product);
        $orderingCost = $this->orderingCost($product);

        if ($holdingCost <= 0 || $annualDemand <= 0) {
            return 0;
        }

        return round(sqrt((2 * $annualDemand * $orderingCost) / $holdingCost), 2);
    }
}