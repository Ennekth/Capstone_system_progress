@extends('layouts.default')

@section('title', 'Sales Records')

@section('content')

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 dark:bg-emerald-950/85 text-green-800 dark:text-emerald-300 border border-green-200 dark:border-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    @if (session('skipped') && count(session('skipped')) > 0)
        <div class="mb-4 rounded-lg bg-amber-100 dark:bg-amber-950/85 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900 px-4 py-3 text-sm">
            <p class="font-medium mb-1">Some rows were skipped:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach (session('skipped') as $issue)
                    <li>{{ $issue }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Massive Modern Summary Cards with Clean Icons & Real Dynamic Percentage Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-5 gap-6 mb-8">
        <!-- Today Card -->
        <a href="{{ route('sales.records', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString(), 'source' => request('source')]) }}"
           class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 shadow-sm hover:border-slate-400 dark:hover:border-slate-500 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">Today's Sales</span>
                <div class="p-3 rounded-2xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div>
                <p class="font-black text-gray-900 dark:text-white tracking-tight mb-2" style="font-size: 2rem;">₱{{ number_format($summary['today'], 2) }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold {{ $summary['today_change'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    <svg class="w-4 h-4 {{ $summary['today_change'] < 0 ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <span>{{ $summary['today_change'] >= 0 ? '+' : '' }}{{ $summary['today_change'] }}% <span class="text-gray-400 dark:text-slate-500 font-normal">vs yesterday</span></span>
                </div>
            </div>
        </a>

        <!-- This Week Card -->
        <a href="{{ route('sales.records', ['date_from' => now()->startOfWeek()->toDateString(), 'date_to' => now()->endOfWeek()->toDateString(), 'source' => request('source')]) }}"
           class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 shadow-sm hover:border-slate-400 dark:hover:border-slate-500 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">This Week's Sales</span>
                <div class="p-3 rounded-2xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div>
                <p class="font-black text-gray-900 dark:text-white tracking-tight mb-2" style="font-size: 2rem;">₱{{ number_format($summary['week'], 2) }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold {{ $summary['week_change'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    <svg class="w-4 h-4 {{ $summary['week_change'] < 0 ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <span>{{ $summary['week_change'] >= 0 ? '+' : '' }}{{ $summary['week_change'] }}% <span class="text-gray-400 dark:text-slate-500 font-normal">vs last week</span></span>
                </div>
            </div>
        </a>

        <!-- This Month Card -->
        <a href="{{ route('sales.records', ['date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->endOfMonth()->toDateString(), 'source' => request('source')]) }}"
           class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 shadow-sm hover:border-slate-400 dark:hover:border-slate-500 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">This Month's Sales</span>
                <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>
            <div>
                <p class="font-black text-gray-900 dark:text-white tracking-tight mb-2" style="font-size: 2rem;">₱{{ number_format($summary['month'], 2) }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold {{ $summary['month_change'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    <svg class="w-4 h-4 {{ $summary['month_change'] < 0 ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <span>{{ $summary['month_change'] >= 0 ? '+' : '' }}{{ $summary['month_change'] }}% <span class="text-gray-400 dark:text-slate-500 font-normal">vs last month</span></span>
                </div>
            </div>
        </a>

        <!-- This Year Card -->
        <a href="{{ route('sales.records', ['date_from' => now()->startOfYear()->toDateString(), 'date_to' => now()->endOfYear()->toDateString(), 'source' => request('source')]) }}"
           class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 shadow-sm hover:border-slate-400 dark:hover:border-slate-500 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">This Year's Sales</span>
                <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
            <div>
                <p class="font-black text-gray-900 dark:text-white tracking-tight mb-2" style="font-size: 2rem;">₱{{ number_format($summary['year'], 2) }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold {{ $summary['year_change'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    <svg class="w-4 h-4 {{ $summary['year_change'] < 0 ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <span>{{ $summary['year_change'] >= 0 ? '+' : '' }}{{ $summary['year_change'] }}% <span class="text-gray-400 dark:text-slate-500 font-normal">vs last year</span></span>
                </div>
            </div>
        </a>

        <!-- Overall Card -->
        <a href="{{ route('sales.records', ['source' => request('source')]) }}"
           class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 col-span-1 sm:col-span-1 shadow-sm hover:border-slate-400 dark:hover:border-slate-500 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 group-hover:text-gray-700 dark:group-hover:text-slate-300 transition-colors">Overall Sales</span>
                <div class="p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
            </div>
            <div>
                <p class="font-black text-gray-900 dark:text-white tracking-tight mb-2" style="font-size: 2rem;">₱{{ number_format($summary['overall'], 2) }}</p>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                    <span>All-time accumulated revenue</span>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Container Card Styled to Match Summary Cards -->
    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536] gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Sales Records</h1>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">All recorded sales — from POS and imported data</p>
            </div>
            <!-- Import Sales Button -->
            <button type="button" onclick="toggleImportSalesModal(true)"
               class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer self-start sm:self-auto">
                Import Sales
            </button>
        </div>

        <div class="px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536] bg-gray-50/50 dark:bg-[#161B26]/50">
            <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3">
                <input type="hidden" name="source" value="{{ request('source') }}">
                <div class="grid grid-cols-1 sm:flex sm:items-center gap-3 flex-1">
                    <!-- FROM Date Picker with inline label -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 whitespace-nowrap">FROM:</span>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()"
                               class="w-full sm:w-auto rounded-xl bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>

                    <!-- TO Date Picker with inline label -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 whitespace-nowrap">TO:</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()"
                               class="w-full sm:w-auto rounded-xl bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>

                    <!-- Source Dropdown & Clear Filters Side-by-Side -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <select name="source" onchange="this.form.submit()" class="w-full sm:w-auto rounded-xl bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                            <option value="">All Sources</option>
                            <option value="pos" {{ request('source') === 'pos' ? 'selected' : '' }}>POS</option>
                            <option value="imported" {{ request('source') === 'imported' ? 'selected' : '' }}>Imported</option>
                        </select>

                        @if (request()->anyFilled(['date_from', 'date_to', 'source']))
                            <a href="{{ route('sales.records') }}" class="text-xs font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] px-4 py-2.5 rounded-xl transition-colors whitespace-nowrap border border-gray-300 dark:border-[#1e2536] bg-white dark:bg-[#161B26]">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-8 py-4">Date</th>
                        <th class="px-8 py-4">Transaction ID</th>
                        <th class="px-8 py-4">Source</th>
                        <th class="px-8 py-4">Total</th>
                        <th class="px-8 py-4">Recorded By</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($records as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400">{{ $record->created_at->format('M d, Y') }}</td>
                            <td class="px-8 py-4 font-medium text-gray-900 dark:text-white">{{ $record->transaction_id }}</td>
                            <td class="px-8 py-4">
                                @if ($record->transaction_type === 0)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900">POS</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-900">Imported</span>
                                @endif
                            </td>
                            <td class="px-8 py-4 text-gray-900 dark:text-slate-100 font-medium">₱{{ number_format($record->total_amount, 2) }}</td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400">
                                {{ $record->user->firstname ?? 'N/A' }} {{ $record->user->lastname ?? '' }}
                            </td>
                            <td class="px-8 py-4 text-right">
                                <a href="{{ route('receipt', $record->id) }}" class="text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white font-medium text-sm transition-colors">
                                    View
                                </a>
                                @if ($record->transaction_type === 1)
                                    <form action="{{ route('sales.destroy', $record) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Delete this imported record? This will also affect demand forecasting.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-rose-400 hover:text-red-800 dark:hover:text-rose-300 font-medium text-sm ml-3 transition-colors cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500">No sales records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Fully Clickable Responsive Pagination Footer -->
        <div class="px-8 py-5 border-t-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-slate-400">
            <div>
                Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $records->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900 dark:text-white">{{ $records->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $records->total() }}</span> results
            </div>

            @if ($records->hasPages())
                <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
                    {{-- Previous Page Link --}}
                    @if ($records->onFirstPage())
                        <span class="px-3.5 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-xl cursor-not-allowed">
                            Previous
                        </span>
                    @else
                        <a href="{{ $records->previousPageUrl() }}" class="px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] rounded-xl hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                            Previous
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($records->getUrlRange(max(1, $records->currentPage() - 2), min($records->lastPage(), $records->currentPage() + 2)) as $page => $url)
                        @if ($page == $records->currentPage())
                            <span class="px-3.5 py-2 text-xs font-bold text-white bg-slate-800 dark:bg-slate-200 dark:text-slate-900 rounded-xl shadow-xs">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] rounded-xl hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($records->hasMorePages())
                        <a href="{{ $records->nextPageUrl() }}" class="px-3.5 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] rounded-xl hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                            Next
                        </a>
                    @else
                        <span class="px-3.5 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-xl cursor-not-allowed">
                            Next
                        </span>
                    @endif
                </nav>
            @endif
        </div>

    </div>

    <!-- Import Sales Modal Popup -->
    <div id="importSalesModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-xl bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-6 sm:p-8 transform transition-all text-left">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 mb-2">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Import Sales Records</h3>
                <button type="button" onclick="toggleImportSalesModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-[#10141D] transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">Upload a CSV file of historical sales to backfill demand data.</p>

            <!-- Import Form -->
            <form action="{{ route('sales.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Required Columns Box -->
                <div class="bg-gray-50 dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-4">
                    <p class="text-xs font-semibold text-gray-700 dark:text-slate-300 mb-3">Required CSV columns:</p>
                    <ul class="space-y-2 text-xs text-gray-600 dark:text-slate-400">
                        <li class="flex items-center gap-2">• <code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-slate-800 text-gray-800 dark:text-slate-200 font-mono text-[11px]">product_name</code></li>
                        <li class="flex items-center gap-2">• <code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-slate-800 text-gray-800 dark:text-slate-200 font-mono text-[11px]">quantity</code></li>
                        <li class="flex items-center gap-2">• <code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-slate-800 text-gray-800 dark:text-slate-200 font-mono text-[11px]">date</code> <span class="text-gray-400 dark:text-slate-500">— format: YYYY-MM-DD</span></li>
                        <li class="flex items-center gap-2">• <code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-slate-800 text-gray-800 dark:text-slate-200 font-mono text-[11px]">price</code> <span class="text-gray-400 dark:text-slate-500">— optional</span></li>
                    </ul>
                </div>

                <!-- CSV File Input Section -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-2">CSV File</label>
                    <input type="file" name="file" accept=".csv, .xlsx, .xls" required 
                           class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 dark:file:bg-[#10141D] file:text-gray-700 dark:file:text-slate-200 hover:file:bg-gray-200 dark:hover:file:bg-[#1e2536]">
                </div>

                <!-- Auto-create products checkbox card -->
                <div class="border border-gray-200 dark:border-[#1e2536] rounded-2xl p-4 bg-white dark:bg-[#10141D]">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="auto_create_products" value="1" class="mt-0.5 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                        <div>
                            <span class="text-xs font-semibold text-gray-900 dark:text-white block">Auto-create products not found in the system</span>
                            <span class="text-[11px] text-gray-500 dark:text-slate-400 leading-normal block mt-0.5">Unmatched product names will be created automatically with placeholder cost/price. You can edit them later in Product Management.</span>
                        </div>
                    </label>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-[#1e2536]">
                    <button type="button" onclick="toggleImportSalesModal(false)" class="text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#10141D] px-4 py-2.5 rounded-xl transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <!-- Upload & Import Button -->
                    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                        Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function toggleImportSalesModal(show) {
        const modal = document.getElementById('importSalesModal');
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }
</script>
@endpush