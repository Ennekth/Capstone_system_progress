<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Services\EoqService;
use App\Models\User;
use App\Services\DemandForecastService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(DemandForecastService $forecastService, EoqService $eoqService)
    {
        $user = Auth::user();

        $data = $this->sharedMetrics();

        if (in_array($user->role, ['admin', 'manager'])) {
            $data = array_merge($data, $this->managementMetrics());
        }

        $data['forecast'] = $this->forecastSummary($forecastService);
        $data['salesChart'] = $this->salesChartData();
        $data['totalSales30d'] = Transaction::where('created_at', '>=', today()->subDays(30))->sum('total_amount');
        $data['eoqSummary'] = $this->eoqSummary($forecastService, $eoqService);

        if ($user->role === 'admin') {
            $data['totalUsers'] = User::count();
        }

        return view('home', $data);
    }

    private function sharedMetrics(): array
    {
        return [
            'todaySales' => Transaction::whereDate('created_at', today())->sum('total_amount'),
            'overallSales' => Transaction::sum('total_amount'),
            'todayTransactions' => Transaction::whereDate('created_at', today())->count(),
            'totalProducts' => Product::count(),
        ];
    }

    private function managementMetrics(): array
    {
        return [
            'lowStockProducts' => Product::whereColumn('store_quantity', '<=', 'reorder_point')
                ->whereNotNull('reorder_point')
                ->get(),
            'pendingApprovals' => StockMovement::where('status', 'pending')->count(),
        ];
    }

    private function forecastSummary(DemandForecastService $forecastService): \Illuminate\Support\Collection
    {
        $products = Product::whereHas('category', fn($q) => $q->where('type', 'product'))
            ->orderBy('product_name')
            ->get();

        return $products->map(function ($product) use ($forecastService) {
            return [
                'product' => $product,
                'avg_daily_demand' => $forecastService->averageDailyDemand($product),
                'projected_annual_demand' => $forecastService->projectedAnnualDemand($product),
            ];
        })->sortByDesc('avg_daily_demand')->take(10)->values();
    }

    private function salesChartData(): array
    {
        $days = collect(range(13, 0))->map(fn($i) => today()->subDays($i));

        $salesByDay = Transaction::whereDate('created_at', '>=', today()->subDays(13))
            ->get()
            ->groupBy(fn($t) => $t->created_at->format('Y-m-d'));

        $labels = $days->map(fn($d) => $d->format('M d'));
        $totals = $days->map(function ($d) use ($salesByDay) {
            $key = $d->format('Y-m-d');
            return isset($salesByDay[$key]) ? round($salesByDay[$key]->sum('total_amount'), 2) : 0;
        });

        return ['labels' => $labels, 'totals' => $totals];
    }

    private function eoqSummary(DemandForecastService $forecastService, EoqService $eoqService): \Illuminate\Support\Collection
    {
        $products = Product::whereHas('category', fn($q) => $q->where('type', 'product'))
            ->orderBy('product_name')
            ->get();

        return $products->map(function ($product) use ($forecastService, $eoqService) {
            $annualDemand = $forecastService->projectedAnnualDemand($product);

            return [
                'product' => $product,
                'annual_demand' => $annualDemand,
                'eoq' => $eoqService->calculate($product, $annualDemand),
            ];
        })->filter(fn($row) => $row['eoq'] > 0)->sortByDesc('eoq')->take(10)->values();
    }

}