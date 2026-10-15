<?php

namespace App\Http\Controllers;

use App\Models\ForecastSetting;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForecastSettingController extends Controller
{
    public function index()
    {
        $global = ForecastSetting::where('scope', 'global')->first();
        $categorySettings = ForecastSetting::with('category')->where('scope', 'category')->get();
        $productSettings = ForecastSetting::with('product')->where('scope', 'product')->get();

        $categories = Category::where('type', 'product')->orderBy('name')->get();
        $products = Product::orderBy('product_name')->get();

        return view('forecast.forecastSettings', compact(
            'global', 'categorySettings', 'productSettings', 'categories', 'products'
        ));
    }

    public function updateGlobal(Request $request)
    {
        $data = $request->validate([
            'sma_window_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        ForecastSetting::updateOrCreate(
            ['scope' => 'global'],
            ['sma_window_days' => $data['sma_window_days'], 'updated_by' => Auth::id()]
        );

        return back()->with('success', 'Global SMA window updated.');
    }

    public function storeOverride(Request $request)
    {
        $data = $request->validate([
            'scope' => ['required', 'in:category,product'],
            'category_id' => ['required_if:scope,category', 'nullable', 'exists:categories,id'],
            'product_id' => ['required_if:scope,product', 'nullable', 'exists:products,id'],
            'sma_window_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        ForecastSetting::updateOrCreate(
            [
                'scope' => $data['scope'],
                'category_id' => $data['scope'] === 'category' ? $data['category_id'] : null,
                'product_id' => $data['scope'] === 'product' ? $data['product_id'] : null,
            ],
            [
                'sma_window_days' => $data['sma_window_days'],
                'updated_by' => Auth::id(),
            ]
        );

        return back()->with('success', 'Forecast override saved.');
    }

    public function destroyOverride(ForecastSetting $forecastSetting)
    {
        if ($forecastSetting->scope === 'global') {
            return back()->with('error', 'The global default cannot be deleted.');
        }

        $forecastSetting->delete();

        return back()->with('success', 'Override removed.');
    }
}