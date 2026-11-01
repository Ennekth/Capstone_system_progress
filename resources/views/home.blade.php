@extends('layouts.default')

@section('title', 'Dashboard')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Welcome, {{ auth()->user()->firstname }}</h1>

    {{-- Shared metric cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('sales.records', ['date_from' => today()->toDateString(), 'date_to' => today()->toDateString()]) }}"
           class="group bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-5 hover:border-blue-500/40 dark:hover:border-blue-500/40 hover:shadow-lg transition-all duration-300 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-1">Today's Sales</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">₱{{ number_format($todaySales, 2) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </a>

        <a href="{{ route('sales.records') }}"
           class="group bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-5 hover:border-emerald-500/40 dark:hover:border-emerald-500/40 hover:shadow-lg transition-all duration-300 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-1">Overall Sales</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">₱{{ number_format($overallSales, 2) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </a>

        <a href="{{ route('transaction.history', ['date_from' => today()->toDateString(), 'date_to' => today()->toDateString()]) }}"
           class="group bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-5 hover:border-violet-500/40 dark:hover:border-violet-500/40 hover:shadow-lg transition-all duration-300 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-1">Today's Transactions</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">{{ $todayTransactions }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 dark:bg-violet-950/50 text-violet-600 dark:text-violet-400 flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </div>
        </a>

        <a href="{{ route('admin.products.index') }}"
           class="group bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-5 hover:border-sky-500/40 dark:hover:border-sky-500/40 hover:shadow-lg transition-all duration-300 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-1">Total Products</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">{{ $totalProducts }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
        </a>
    </div>


    {{-- Admin + Manager only --}}
    @if (in_array(auth()->user()->role, ['admin', 'manager']))
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <a href="{{ route('inventory.index', ['status' => 'restock']) }}"
               class="group bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-5 hover:border-red-500/40 dark:hover:border-rose-500/40 hover:shadow-lg transition-all duration-300 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-1">Restock Needed</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-rose-400">{{ $lowStockProducts->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </a>
            
            <a href="{{ route('inventory.approvals') }}"
               class="group bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-5 hover:border-amber-500/40 dark:hover:border-amber-500/40 hover:shadow-lg transition-all duration-300 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-1">Pending Approvals</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $pendingApprovals }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </a>
        </div>

            {{-- Sales Performance Card (Styled like reference design) --}}
            <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Total Sales</h2>
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-extrabold text-gray-900 dark:text-white">₱{{ number_format(array_sum($salesChart['totals']), 2) }}</span>
                            <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full {{ $salesGrowth >= 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400' }}">
                                {{ $salesGrowth >= 0 ? '↑ +' . $salesGrowth . '%' : '↓ ' . $salesGrowth . '%' }} from last period
                            </span>
                        </div>
                    </div>

                    {{-- Right Header Controls (7D/14D/30D buttons + Projected Demand & EOQ badges) --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="hidden xl:flex items-center gap-3 bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] px-3 py-1.5 rounded-xl text-xs">
                            <div>
                                <span class="text-gray-400">Projected Demand:</span> <span class="font-bold text-gray-800 dark:text-white">{{ number_format($totalProjectedDemand) }}/yr</span>
                            </div>
                            <div class="h-3 w-px bg-gray-300 dark:bg-gray-700"></div>
                            <div>
                                <span class="text-gray-400">EOQ:</span> <span class="font-bold text-gray-800 dark:text-white">{{ number_format($totalEoq) }} units</span>
                            </div>
                        </div>

                        <div class="inline-flex rounded-xl border border-gray-200 dark:border-[#1e2536] p-1 bg-gray-50 dark:bg-[#161B26]">
                            <a href="{{ route('home', ['range' => 7]) }}" class="px-3 py-1 text-xs font-semibold rounded-lg transition-colors {{ $selectedRange == 7 ? 'bg-white dark:bg-[#10141D] text-blue-600 dark:text-blue-400 shadow-xs' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">7D</a>
                            <a href="{{ route('home', ['range' => 14]) }}" class="px-3 py-1 text-xs font-semibold rounded-lg transition-colors {{ $selectedRange == 14 ? 'bg-white dark:bg-[#10141D] text-blue-600 dark:text-blue-400 shadow-xs' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">14D</a>
                            <a href="{{ route('home', ['range' => 30]) }}" class="px-3 py-1 text-xs font-semibold rounded-lg transition-colors {{ $selectedRange == 30 ? 'bg-white dark:bg-[#10141D] text-blue-600 dark:text-blue-400 shadow-xs' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">30D</a>
                        </div>
                    </div>
                </div>
                <canvas id="salesChart" height="85"></canvas>
            </div>

            @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
            <script>
                function chartColors() {
                    const isDark = document.documentElement.classList.contains('dark');
                    return {
                        line: isDark ? '#3b82f6' : '#2563eb',
                        fill: isDark ? 'rgba(59, 130, 246, 0.15)' : 'rgba(37, 99, 235, 0.08)',
                        grid: isDark ? 'rgba(148, 163, 184, 0.08)' : 'rgba(0, 0, 0, 0.05)',
                        text: isDark ? '#94a3b8' : '#6b7280',
                    };
                }

                const colors = chartColors();
                const ctx = document.getElementById('salesChart').getContext('2d');

                const salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($salesChart['labels']),
                        datasets: [{
                            label: 'Sales (₱)',
                            data: @json($salesChart['totals']),
                            borderColor: colors.line,
                            backgroundColor: colors.fill,
                            borderWidth: 2.5,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: colors.line,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { color: colors.grid, drawBorder: false }, 
                                ticks: { color: colors.text, font: { size: 11 } } 
                            },
                            x: { 
                                grid: { color: colors.grid, drawBorder: false }, 
                                ticks: { color: colors.text, font: { size: 11 } } 
                            }
                        }
                    }
                });

                window.addEventListener('themechange', function () {
                    const c = chartColors();
                    salesChart.data.datasets[0].borderColor = c.line;
                    salesChart.data.datasets[0].backgroundColor = c.fill;
                    salesChart.data.datasets[0].pointBackgroundColor = c.line;
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
    <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-[#1e2536]">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">Demand Forecast</h2>
                <p class="text-xs text-gray-500 dark:text-slate-400">Top 10 products by average daily demand (SMA)</p>
            </div>

            @if (in_array(auth()->user()->role, ['admin', 'manager']))
                <a href="{{ route('forecast.settings') }}"
                   class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs">
                    Calibrate Forecast Settings
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left table-fixed">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-6 py-3 text-left w-1/4">Product Name</th>
                        <th class="px-6 py-3 text-left w-1/4">Avg. Daily Demand</th>
                        <th class="px-6 py-3 text-left w-1/4">Projected Annual Demand</th>
                        <th class="px-6 py-3 text-right w-1/4">Current Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($forecast as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white text-left truncate w-1/4">{{ $row['product']->product_name }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400 text-left w-1/4">{{ $row['avg_daily_demand'] }} / day</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400 text-left w-1/4">{{ number_format($row['projected_annual_demand']) }} / year</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400 text-right w-1/4">{{ $row['product']->store_quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">
                                No sales data yet. Forecast will populate once sales are recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Economic Order Quantity (EOQ) --}}
    <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm mb-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-[#1e2536]">
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">Economic Order Quantity (EOQ)</h2>
                <p class="text-xs text-gray-500 dark:text-slate-400">Recommended order quantity per product</p>
            </div>

            @if (in_array(auth()->user()->role, ['admin', 'manager']))
                <a href="{{ route('eoq.calibration') }}"
                class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs">
                    Calibrate EOQ Parameters
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left table-fixed">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-6 py-3 text-left w-1/4">Product Name</th>
                        <th class="px-6 py-3 text-left w-1/4">Avg. Daily Demand</th>
                        <th class="px-6 py-3 text-left w-1/4">Annual Demand</th>
                        <th class="px-6 py-3 text-right w-1/4">Recommended Order Qty (EOQ)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($eoqSummary as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white text-left truncate w-1/4">{{ $row['product']->product_name }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400 text-left w-1/4">{{ $row['avg_daily_demand'] }} / day</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400 text-left w-1/4">{{ number_format($row['annual_demand']) }} / year</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-slate-100 font-semibold text-right w-1/4">{{ number_format($row['eoq'], 0) }} units</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">
                            Not enough data to calculate EOQ yet.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Admin + Manager: Inventory Alerts --}}
    @if (in_array(auth()->user()->role, ['admin', 'manager']) && $lowStockProducts->count() > 0)
        <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm mb-6">
            {{-- Card Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-[#1e2536]">
                <div>
                    <h2 class="font-bold text-gray-900 dark:text-white">Inventory Alerts</h2>
                    <p class="text-xs text-gray-500 dark:text-slate-400">Combined out-of-stock and low stock items requiring attention</p>
                </div>
                <span class="bg-slate-100 text-slate-700 dark:bg-[#161B26] dark:text-slate-300 text-xs font-bold px-3.5 py-1.5 rounded-full">
                    {{ $lowStockProducts->count() }} Products
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left table-fixed">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-6 py-3 text-left w-1/4">Product Name</th>
                            <th class="px-6 py-3 text-left w-1/4">Category</th>
                            <th class="px-6 py-3 text-left w-1/4">Stock Status</th>
                            <th class="px-6 py-3 text-right w-1/4">Current Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @foreach ($lowStockProducts as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white text-left truncate w-1/4">
                                    {{ $product->product_name }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-400 text-left truncate w-1/4">
                                    {{ $product->category->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-left w-1/4">
                                    @if ($product->store_quantity <= 0)
                                        <span class="inline-block bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">
                                            Out of Stock
                                        </span>
                                    @else
                                        <span class="inline-block bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">
                                            Low Stock
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-slate-400 w-1/4">
                                    {{ $product->store_quantity }} left <span class="text-gray-400 font-normal hidden sm:inline">(ROP: {{ $product->reorder_point ?? 'N/A' }})</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection