@extends('layouts.default')

@section('title', 'Dashboard')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Welcome, {{ auth()->user()->firstname }}</h1>

    {{-- Top Summary Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-5 gap-6 mb-8">
        <!-- Today's Sales Card -->
        <a href="{{ route('sales.records', ['date_from' => today()->toDateString(), 'date_to' => today()->toDateString()]) }}"
           class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 shadow-sm hover:border-slate-400 dark:hover:border-slate-500 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">Today's Sales</span>
                <div class="p-3 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div>
                <p class="font-black text-gray-900 dark:text-white tracking-tight mb-2" style="font-size: 2rem;">₱{{ number_format($todaySales, 2) }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400">
                    <span>Daily revenue total</span>
                </div>
            </div>
        </a>

        <!-- Overall Sales Card -->
        <a href="{{ route('sales.records') }}"
           class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 shadow-sm hover:border-slate-400 dark:hover:border-slate-500 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">Overall Sales</span>
                <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
            <div>
                <p class="font-black text-gray-900 dark:text-white tracking-tight mb-2" style="font-size: 2rem;">₱{{ number_format($overallSales, 2) }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                    <span>All-time accumulated revenue</span>
                </div>
            </div>
        </a>

        <!-- Today's Transactions Card -->
        <a href="{{ route('transaction.history', ['date_from' => today()->toDateString(), 'date_to' => today()->toDateString()]) }}"
           class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 shadow-sm hover:border-slate-400 dark:hover:border-slate-500 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">Transactions</span>
                <div class="p-3 rounded-2xl bg-violet-50 dark:bg-violet-950/50 text-violet-600 dark:text-violet-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
            </div>
            <div>
                <p class="font-black text-gray-900 dark:text-white tracking-tight mb-2" style="font-size: 2rem;">{{ $todayTransactions }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-violet-600 dark:text-violet-400">
                    <span>Completed today</span>
                </div>
            </div>
        </a>

        <!-- Total Products Card -->
        <a href="{{ route('admin.products.index') }}"
           class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 shadow-sm hover:border-slate-400 dark:hover:border-slate-500 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">Products</span>
                <div class="p-3 rounded-2xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
            <div>
                <p class="font-black text-gray-900 dark:text-white tracking-tight mb-2" style="font-size: 2rem;">{{ $totalProducts }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-sky-600 dark:text-sky-400">
                    <span>Active catalog items</span>
                </div>
            </div>
        </a>

        <!-- Restock Needed Card -->
        <a href="{{ route('admin.products.index', ['status' => 'restock_needed']) }}"
           class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 shadow-sm hover:border-slate-400 dark:hover:border-slate-500 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">Restock Needed</span>
                <div class="p-3 rounded-2xl bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-rose-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <div>
                <p class="font-black text-red-600 dark:text-rose-400 tracking-tight mb-2" style="font-size: 2rem;">{{ $restockNeededCount }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-red-600 dark:text-rose-400">
                    <span>Items require attention</span>
                </div>
            </div>
        </a>
    </div>

    {{-- Admin + Manager only: Sales Performance Card --}}
    @if (in_array(auth()->user()->role, ['admin', 'manager']))
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8 mb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Total Sales</h2>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-extrabold text-gray-900 dark:text-white">₱{{ number_format(array_sum($salesChart['totals']), 2) }}</span>
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full {{ $salesGrowth >= 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400' }}">
                            {{ $salesGrowth >= 0 ? '↑ +' . $salesGrowth . '%' : '↓ ' . $salesGrowth . '%' }} from last period
                        </span>
                    </div>
                </div>

                {{-- Right Header Controls --}}
                <div class="flex flex-wrap items-center gap-3">
                    <div class="hidden xl:flex items-center gap-3 bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] px-4 py-2 rounded-xl text-xs">
                        <div>
                            <span class="text-gray-400">Projected Demand:</span> <span class="font-bold text-gray-800 dark:text-white">{{ number_format($totalProjectedDemand) }}/yr</span>
                        </div>
                        <div class="h-3 w-px bg-gray-300 dark:bg-gray-700"></div>
                        <div>
                            <span class="text-gray-400">EOQ:</span> <span class="font-bold text-gray-800 dark:text-white">{{ number_format($totalEoq) }} units</span>
                        </div>
                    </div>

                    <div class="inline-flex rounded-xl border border-gray-200 dark:border-[#1e2536] p-1 bg-gray-50 dark:bg-[#161B26]">
                        <a href="{{ route('home', ['range' => 7]) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $selectedRange == 7 ? 'bg-white dark:bg-[#10141D] text-blue-600 dark:text-blue-400 shadow-xs' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">7D</a>
                        <a href="{{ route('home', ['range' => 14]) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $selectedRange == 14 ? 'bg-white dark:bg-[#10141D] text-blue-600 dark:text-blue-400 shadow-xs' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">14D</a>
                        <a href="{{ route('home', ['range' => 30]) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $selectedRange == 30 ? 'bg-white dark:bg-[#10141D] text-blue-600 dark:text-blue-400 shadow-xs' : 'text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white' }}">30D</a>
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

    {{-- Demand Forecast --}}
    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm mb-8 overflow-hidden">
        <div class="flex items-center justify-between px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Demand Forecast</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Top products by average daily demand (SMA) — showing 4 per page</p>
            </div>

            @if (in_array(auth()->user()->role, ['admin', 'manager']))
                <a href="{{ route('forecast.settings') }}"
                   class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs">
                    Calibrate Forecast Settings
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left table-fixed">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-8 py-4 text-left w-1/4">Product Name</th>
                        <th class="px-8 py-4 text-left w-1/4">Avg. Daily Demand</th>
                        <th class="px-8 py-4 text-left w-1/4">Projected Annual Demand</th>
                        <th class="px-8 py-4 text-right w-1/4">Current Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($forecast as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-8 py-4 font-medium text-gray-900 dark:text-white text-left truncate w-1/4">{{ $row['product']->product_name }}</td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400 text-left w-1/4">{{ $row['avg_daily_demand'] }} / day</td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400 text-left w-1/4">{{ number_format($row['projected_annual_demand']) }} / year</td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400 text-right w-1/4">{{ $row['product']->store_quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500">
                                No sales data yet. Forecast will populate once sales are recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Demand Forecast Pagination --}}
        @if ($forecast->hasPages())
            <div class="px-8 py-5 border-t-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-slate-400">
                <div>
                    Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $forecast->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900 dark:text-white">{{ $forecast->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $forecast->total() }}</span> results
                </div>

                <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
                    @if ($forecast->onFirstPage())
                        <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                            Previous
                        </span>
                    @else
                        <a href="{{ $forecast->previousPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                            Previous
                        </a>
                    @endif

                    @foreach ($forecast->getUrlRange(max(1, $forecast->currentPage() - 2), min($forecast->lastPage(), $forecast->currentPage() + 2)) as $page => $url)
                        @if ($page == $forecast->currentPage())
                            <span class="px-4 py-2 text-xs font-bold text-white bg-slate-900 dark:bg-[#1E293B] rounded-full shadow-xs border border-transparent">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if ($forecast->hasMorePages())
                        <a href="{{ $forecast->nextPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                            Next
                        </a>
                    @else
                        <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                            Next
                        </span>
                    @endif
                </nav>
            </div>
        @endif
    </div>

    {{-- Economic Order Quantity (EOQ) --}}
    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm mb-8 overflow-hidden">
        <div class="flex items-center justify-between px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Economic Order Quantity (EOQ)</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Recommended order quantity per product — showing 4 per page</p>
            </div>

            @if (in_array(auth()->user()->role, ['admin', 'manager']))
                <a href="{{ route('eoq.calibration') }}"
                class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs">
                    Calibrate EOQ Parameters
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left table-fixed">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-8 py-4 text-left w-1/4">Product Name</th>
                        <th class="px-8 py-4 text-left w-1/4">Avg. Daily Demand</th>
                        <th class="px-8 py-4 text-left w-1/4">Annual Demand</th>
                        <th class="px-8 py-4 text-right w-1/4">Recommended Order Qty (EOQ)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($eoqSummary as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-8 py-4 font-medium text-gray-900 dark:text-white text-left truncate w-1/4">{{ $row['product']->product_name }}</td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400 text-left w-1/4">{{ $row['avg_daily_demand'] }} / day</td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400 text-left w-1/4">{{ number_format($row['annual_demand']) }} / year</td>
                            <td class="px-8 py-4 text-gray-900 dark:text-slate-100 font-semibold text-right w-1/4">{{ number_format($row['eoq'], 0) }} units</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500">
                                Not enough data to calculate EOQ yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- EOQ Pagination --}}
        @if ($eoqSummary->hasPages())
            <div class="px-8 py-5 border-t-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-slate-400">
                <div>
                    Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $eoqSummary->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900 dark:text-white">{{ $eoqSummary->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $eoqSummary->total() }}</span> results
                </div>

                <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
                    @if ($eoqSummary->onFirstPage())
                        <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                            Previous
                        </span>
                    @else
                        <a href="{{ $eoqSummary->previousPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                            Previous
                        </a>
                    @endif

                    @foreach ($eoqSummary->getUrlRange(max(1, $eoqSummary->currentPage() - 2), min($eoqSummary->lastPage(), $eoqSummary->currentPage() + 2)) as $page => $url)
                        @if ($page == $eoqSummary->currentPage())
                            <span class="px-4 py-2 text-xs font-bold text-white bg-slate-900 dark:bg-[#1E293B] rounded-full shadow-xs border border-transparent">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if ($eoqSummary->hasMorePages())
                        <a href="{{ $eoqSummary->nextPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                            Next
                        </a>
                    @else
                        <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                            Next
                        </span>
                    @endif
                </nav>
            </div>
        @endif
    </div>

    {{-- Admin + Manager: Inventory Alerts (Store Status / Warehouse Status / Total Status with Stock levels & ROP) --}}
    @if (in_array(auth()->user()->role, ['admin', 'manager']))
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm mb-8 overflow-hidden">
            <div class="flex items-center justify-between px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Inventory Alerts</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Combined store, warehouse, and total stock tracking — showing 4 per page</p>
                </div>
                <span class="bg-slate-100 text-slate-700 dark:bg-[#161B26] dark:text-slate-300 text-xs font-bold px-4 py-2 rounded-xl">
                    {{ $lowStockProducts->total() }} Products
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-6 py-4">Product Name</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4">Store Status</th>
                            <th class="px-6 py-4">Warehouse Status</th>
                            <th class="px-6 py-4">Total Status</th>
                            <th class="px-6 py-4 text-right">Current Stock / ROP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($lowStockProducts as $product)
                            @php
                                $storeQty = $product->store_quantity ?? 0;
                                $whQty = $product->warehouse_quantity ?? 0;
                                $totalQty = $storeQty + $whQty;
                                $reorder = $product->reorder_point ?? 10;

                                $storeStatus = $storeQty == 0 ? 'Out of Stock' : ($storeQty <= $reorder ? 'Low Stock' : 'In Stock');
                                $whStatus = $whQty == 0 ? 'Out of Stock' : ($whQty <= $reorder ? 'Low Stock' : 'In Stock');
                                $totalStatus = $totalQty == 0 ? 'Out of Stock' : ($totalQty <= $reorder ? 'Low Stock' : 'In Stock');
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                    {{ $product->product_name }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-400">
                                    {{ $product->category->name ?? '—' }}
                                </td>

                                <!-- Store Status Badge -->
                                <td class="px-6 py-4">
                                    @if ($storeStatus === 'Out of Stock')
                                        <span class="inline-block bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-red-200 dark:border-rose-900">Out of Stock</span>
                                    @elseif ($storeStatus === 'Low Stock')
                                        <span class="inline-block bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-amber-200 dark:border-amber-900">Low Stock</span>
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-900">In Stock</span>
                                    @endif
                                </td>

                                <!-- Warehouse Status Badge -->
                                <td class="px-6 py-4">
                                    @if ($whStatus === 'Out of Stock')
                                        <span class="inline-block bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-red-200 dark:border-rose-900">Out of Stock</span>
                                    @elseif ($whStatus === 'Low Stock')
                                        <span class="inline-block bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-amber-200 dark:border-amber-900">Low Stock</span>
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-900">In Stock</span>
                                    @endif
                                </td>

                                <!-- Total Status Badge -->
                                <td class="px-6 py-4">
                                    @if ($totalStatus === 'Out of Stock')
                                        <span class="inline-block bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-red-200 dark:border-rose-900">Out of Stock</span>
                                    @elseif ($totalStatus === 'Low Stock')
                                        <span class="inline-block bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-amber-200 dark:border-amber-900">Low Stock</span>
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-900">In Stock</span>
                                    @endif
                                </td>

                                <!-- Current Level & ROP -->
                                <td class="px-6 py-4 text-right text-gray-600 dark:text-slate-300 font-medium">
                                    Store {{ $storeQty }} · WH {{ $whQty }} <span class="text-gray-400 text-xs block">(ROP: {{ $reorder }})</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500">
                                    No inventory alerts at this time. All products are well-stocked!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Inventory Alerts Pagination --}}
            @if ($lowStockProducts->hasPages())
                <div class="px-8 py-5 border-t-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-slate-400">
                    <div>
                        Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $lowStockProducts->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900 dark:text-white">{{ $lowStockProducts->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $lowStockProducts->total() }}</span> results
                    </div>

                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
                        @if ($lowStockProducts->onFirstPage())
                            <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                                Previous
                            </span>
                        @else
                            <a href="{{ $lowStockProducts->previousPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                Previous
                            </a>
                        @endif

                        @foreach ($lowStockProducts->getUrlRange(max(1, $lowStockProducts->currentPage() - 2), min($lowStockProducts->lastPage(), $lowStockProducts->currentPage() + 2)) as $page => $url)
                            @if ($page == $lowStockProducts->currentPage())
                                <span class="px-4 py-2 text-xs font-bold text-white bg-slate-900 dark:bg-[#1E293B] rounded-full shadow-xs border border-transparent">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($lowStockProducts->hasMorePages())
                            <a href="{{ $lowStockProducts->nextPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                Next
                            </a>
                        @else
                            <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                                Next
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        </div>
    @endif

@endsection