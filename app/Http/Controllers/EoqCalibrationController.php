<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductOrderingCost;
use App\Services\DemandForecastService;
use App\Services\EoqService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EoqCalibrationController extends Controller
{
    public function index(Request $request, DemandForecastService $forecastService, EoqService $eoqService)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $searchTerms = array_filter(explode(' ', trim($request->search)));
            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $q->where('product_name', 'like', '%' . $term . '%');
                }
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->whereHas('category', fn($q) => $q->where('type', 'product'))
            ->orderBy('product_name')
            ->paginate(20)
            ->withQueryString();

        // Attach live EOQ figures for display
        $products->getCollection()->transform(function ($product) use ($forecastService, $eoqService) {
            $annualDemand = $forecastService->projectedAnnualDemand($product);
            $product->computed_annual_demand = $annualDemand;
            $product->computed_ordering_cost = $eoqService->orderingCost($product);
            $product->computed_holding_cost = $eoqService->holdingCost($product);
            $product->computed_eoq = $eoqService->calculate($product, $annualDemand);
            return $product;
        });

        $categories = Category::where('type', 'product')->orderBy('name')->get();

        return view('forecast.eoqCalibration', compact('products', 'categories'));
    }

    public function applyBatch(Request $request)
    {
        $data = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['exists:products,id'],
            'ordering_cost' => ['nullable', 'numeric', 'min:0'],
            'holding_cost_override' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (empty($data['ordering_cost']) && empty($data['holding_cost_override'])) {
            return back()->with('error', 'Enter at least one value (ordering cost or holding cost) to apply.');
        }

        DB::transaction(function () use ($data) {
            foreach ($data['product_ids'] as $productId) {
                ProductOrderingCost::create([
                    'product_id' => $productId,
                    'ordering_cost' => $data['ordering_cost'] ?? null,
                    'holding_cost_override' => $data['holding_cost_override'] ?? null,
                    'set_by' => Auth::id(),
                ]);
            }
        });

        $count = count($data['product_ids']);
        return back()->with('success', "Calibration applied to {$count} product(s).");
    }
}