@extends('layouts.default')

@section('title', 'Dashboard')

@section('content')

    <h1 class="text-2xl font-bold mb-6">Welcome, {{ auth()->user()->firstname }}</h1>

    {{-- Shared metric cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('sales.records') }}"
        class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-400 hover:shadow-sm transition">
            <p class="text-sm text-gray-500">Today's Sales</p>
            <p class="text-2xl font-bold">₱{{ number_format($todaySales, 2) }}</p>
        </a>

        <a href="{{ route('sales.records') }}"
        class="bg-white border border-gray-200 rounded-xl p-5 hover:border-gray-400 hover:shadow-sm transition">
            <p class="text-sm text-gray-500">Overall Sales</p>
            <p class="text-2xl font-bold">₱{{ number_format($overallSales, 2) }}</p>
        </a>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-sm text-gray-500">Transactions Today</p>
            <p class="text-2xl font-bold">{{ $todayTransactions }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-sm text-gray-500">Total Products</p>
            <p class="text-2xl font-bold">{{ $totalProducts }}</p>
        </div>
    </div>


    {{-- Admin + Manager only --}}
    @if (in_array(auth()->user()->role, ['admin', 'manager']))
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-sm text-gray-500">Low Stock Items</p>
                <p class="text-2xl font-bold text-red-600">{{ $lowStockProducts->count() }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-sm text-gray-500">Pending Restock Approvals</p>
                <p class="text-2xl font-bold text-amber-600">{{ $pendingApprovals }}</p>
            </div>
        </div>

        @if ($lowStockProducts->count() > 0)
            <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6">
                <h2 class="font-bold mb-3">Low Stock Alerts</h2>
                <ul class="divide-y divide-gray-100">
                    @foreach ($lowStockProducts as $product)
                        <li class="py-2 flex justify-between text-sm">
                            <span>{{ $product->product_name }}</span>
                            <span class="text-red-600 font-medium">{{ $product->store_quantity }} left (ROP: {{ $product->reorder_point }})</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

            {{-- Sales graph --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold">Sales — Last 14 Days</h2>
                    <p class="text-sm text-gray-500">30-day total: ₱{{ number_format($totalSales30d, 2) }}</p>
                </div>
                <canvas id="salesChart" height="80"></canvas>
            </div>

            @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
            <script>
                function chartColors() {
                    const isDark = document.documentElement.classList.contains('dark');
                    return {
                        line: isDark ? '#e2e8f0' : '#111827',
                        fill: isDark ? 'rgba(226, 232, 240, 0.12)' : 'rgba(17, 24, 39, 0.08)',
                        grid: isDark ? 'rgba(148, 163, 184, 0.15)' : 'rgba(0, 0, 0, 0.08)',
                        text: isDark ? '#94a3b8' : '#6b7280',
                    };
                }

                const colors = chartColors();

                const salesChart = new Chart(document.getElementById('salesChart'), {
                    type: 'line',
                    data: {
                        labels: @json($salesChart['labels']),
                        datasets: [{
                            label: 'Sales (₱)',
                            data: @json($salesChart['totals']),
                            borderColor: colors.line,
                            backgroundColor: colors.fill,
                            tension: 0.3,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: colors.grid }, ticks: { color: colors.text } },
                            x: { grid: { color: colors.grid }, ticks: { color: colors.text } }
                        }
                    }
                });

                // Re-color the chart instantly when the theme is toggled
                window.addEventListener('themechange', function () {
                    const c = chartColors();
                    salesChart.data.datasets[0].borderColor = c.line;
                    salesChart.data.datasets[0].backgroundColor = c.fill;
                    salesChart.options.scales.y.grid.color = c.grid;
                    salesChart.options.scales.x.grid.color = c.grid;
                    salesChart.options.scales.y.ticks.color = c.text;
                    salesChart.options.scales.x.ticks.color = c.text;
                    salesChart.update();
                });
            </script>
            @endpush

    @endif

    {{-- Demand Forecast — Admin, Manager, Staff --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h2 class="font-bold">Demand Forecast</h2>
                <p class="text-xs text-gray-500">Top 10 products by average daily demand (SMA)</p>
            </div>

            @if (auth()->user()->role === 'admin')
                <a href="{{ route('forecast.settings') }}"
                   class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-800">
                    Calibrate Forecast Settings
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Avg. Daily Demand</th>
                        <th class="px-6 py-3">Projected Annual Demand</th>
                        <th class="px-6 py-3">Current Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($forecast as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $row['product']->product_name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $row['avg_daily_demand'] }} / day</td>
                            <td class="px-6 py-4 text-gray-600">{{ number_format($row['projected_annual_demand']) }} / year</td>
                            <td class="px-6 py-4 text-gray-600">{{ $row['product']->store_quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                No sales data yet. Forecast will populate once sales are recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- eoq --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm mt-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h2 class="font-bold">Economic Order Quantity (EOQ)</h2>
                <p class="text-xs text-gray-500">Recommended order quantity per product</p>
            </div>

            @if (in_array(auth()->user()->role, ['admin', 'manager']))
                <a href="{{ route('eoq.calibration') }}"
                class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-800">
                    Calibrate EOQ Parameters
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Annual Demand</th>
                        <th class="px-6 py-3">Recommended Order Qty (EOQ)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($eoqSummary as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $row['product']->product_name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ number_format($row['annual_demand']) }} / year</td>
                            <td class="px-6 py-4 text-gray-600 font-semibold">{{ number_format($row['eoq'], 0) }} units</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400">
                            Not enough data to calculate EOQ yet.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection