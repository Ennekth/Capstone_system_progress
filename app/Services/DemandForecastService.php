<?php

namespace App\Services;

use App\Models\Product;
use App\Models\DemandRecord;
use App\Models\ForecastSetting;
use Illuminate\Support\Carbon;

class DemandForecastService
{
    /**
     * Resolve the SMA window (in days) for a given product,
     * checking product-level override, then category-level, then global default.
     */
    public function resolveWindow(Product $product): int
    {
        $productSetting = ForecastSetting::where('scope', 'product')
            ->where('product_id', $product->id)
            ->first();

        if ($productSetting) {
            return $productSetting->sma_window_days;
        }

        $categorySetting = ForecastSetting::where('scope', 'category')
            ->where('category_id', $product->category_id)
            ->first();

        if ($categorySetting) {
            return $categorySetting->sma_window_days;
        }

        $globalSetting = ForecastSetting::where('scope', 'global')->first();

        return $globalSetting->sma_window_days ?? 30;
    }

    /**
     * Simple Moving Average: total demand over the window / number of days in the window.
     * This gives average DAILY demand, which is the base unit EOQ needs.
     */
    public function averageDailyDemand(Product $product): float
    {
        $windowDays = $this->resolveWindow($product);

        $totalDemand = DemandRecord::where('product_id', $product->id)
            ->where('record_date', '>=', Carbon::today()->subDays($windowDays))
            ->sum('quantity');

        return $windowDays > 0 ? round($totalDemand / $windowDays, 2) : 0;
    }

    /**
     * Projected annual demand, extrapolated from the SMA daily average.
     * This is the 'D' EOQ needs.
     */
    public function projectedAnnualDemand(Product $product): float
    {
        return round($this->averageDailyDemand($product) * 365, 2);
    }
}