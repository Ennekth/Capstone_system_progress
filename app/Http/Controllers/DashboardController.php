<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Services\EoqService;
use App\Models\User;
use App\Services\DemandForecastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request, DemandForecastService $forecastService, EoqService $eoqService)
    {
        $user = Auth::user();

        $data = $this->sharedMetrics();

        if (in_array($user->role, ['admin', 'manager'])) {
            $data = array_merge($data, $this->managementMetrics($forecastService, $eoqService));
        }

        $range = (int) $request->get('range', 14);
        if (!in_array($range, [7, 14, 30])) {
            $range = 14;
        }

        $data['forecast'] = $this->forecastSummary($forecastService);
        $data['salesChart'] = $this->salesChartData($range);
        $data['totalSales30d'] = Transaction::where('created_at', '>=', today()->subDays(30))->sum('total_amount');
        
        // Calculate sales growth percentage compared to previous period
        $currentPeriodSales = Transaction::where('created_at', '>=', today()->subDays($range))->sum('total_amount');
        $previousPeriodSales = Transaction::where('created_at', '>=', today()->subDays($range * 2))->where('created_at', '<', today()->subDays($range))->sum('total_amount');
        
        $salesGrowth = $previousPeriodSales > 0 
            ? round((($currentPeriodSales - $previousPeriodSales) / $previousPeriodSales) * 100, 1) 
            : ($currentPeriodSales > 0 ? 100 : 0);

        $data['salesGrowth'] = $salesGrowth;
        $data['eoqSummary'] = $this->eoqSummary($forecastService, $eoqService);
        $data['selectedRange'] = $range;

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

    private function managementMetrics(DemandForecastService $forecastService, EoqService $eoqService): array
    {
        $products = Product::whereHas('category', fn($q) => $q->where('type', 'product'))->get();
        
        $totalProjectedDemand = $products->sum(fn($p) => $forecastService->projectedAnnualDemand($p));
        $totalEoq = $products->sum(fn($p) => $eoqService->calculate($p, $forecastService->projectedAnnualDemand($p)));

        return [
            'lowStockProducts' => Product::with('category')->where(function($q) {
                $q->where('store_quantity', '<=', 0)
                  ->orWhere(fn($sub) => $sub->whereColumn('store_quantity', '<=', 'reorder_point')->whereNotNull('reorder_point'));
            })->get(),
            'pendingApprovals' => StockMovement::where('status', 'pending')->count(),
            'totalProjectedDemand' => $totalProjectedDemand,
            'totalEoq' => $totalEoq,
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

    private function salesChartData(int $daysCount): array
    {
        $days = collect(range($daysCount - 1, 0))->map(fn($i) => today()->subDays($i));

        $salesByDay = Transaction::whereDate('created_at', '>=', today()->subDays($daysCount - 1))
            ->get()
            ->groupBy(fn($t) => $t->created_at->format('Y-m-d'));

        $labels = $days->map(fn($d) => $d->format('M d'));
        $totals = $days->map(function ($d) use ($salesByDay) {
            $key = $d->format('Y-m-d');
            return isset($salesByDay[$key]) ? round($salesByDay[$key]->sum('total_amount'), 2) : 0;
        })->values()->all(); // <--- Convert Collection to plain PHP array here

        return ['labels' => $labels->all(), 'totals' => $totals];
    }

    private function eoqSummary(DemandForecastService $forecastService, EoqService $eoqService): \Illuminate\Support\Collection
    {
        $products = Product::whereHas('category', fn($q) => $q->where('type', 'product'))
            ->orderBy('product_name')
            ->get();

        return $products->map(function ($product) use ($forecastService, $eoqService) {
            $annualDemand = $forecastService->projectedAnnualDemand($product);
            $avgDailyDemand = $forecastService->averageDailyDemand($product);

            return [
                'product' => $product,
                'avg_daily_demand' => $avgDailyDemand,
                'annual_demand' => $annualDemand,
                'eoq' => $eoqService->calculate($product, $annualDemand),
            ];
        })->filter(fn($row) => $row['eoq'] > 0)->sortByDesc('eoq')->take(10)->values();
    }
}