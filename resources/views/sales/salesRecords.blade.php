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

    <!-- Modern Summary Cards with Icons & Clickable Filters -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        <!-- Today Card -->
        <a href="{{ route('sales.records', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString(), 'source' => request('source')]) }}"
           class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-6 shadow-xs hover:border-slate-400 dark:hover:border-slate-700 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500 dark:text-slate-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Today</p>
                <div class="p-2 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['today'], 2) }}</p>
        </a>

        <!-- This Week Card -->
        <a href="{{ route('sales.records', ['date_from' => now()->startOfWeek()->toDateString(), 'date_to' => now()->endOfWeek()->toDateString(), 'source' => request('source')]) }}"
           class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-6 shadow-xs hover:border-slate-400 dark:hover:border-slate-700 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500 dark:text-slate-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">This Week</p>
                <div class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['week'], 2) }}</p>
        </a>

        <!-- This Month Card -->
        <a href="{{ route('sales.records', ['date_from' => now()->startOfMonth()->toDateString(), 'date_to' => now()->endOfMonth()->toDateString(), 'source' => request('source')]) }}"
           class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-6 shadow-xs hover:border-slate-400 dark:hover:border-slate-700 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500 dark:text-slate-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">This Month</p>
                <div class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['month'], 2) }}</p>
        </a>

        <!-- This Year Card -->
        <a href="{{ route('sales.records', ['date_from' => now()->startOfYear()->toDateString(), 'date_to' => now()->endOfYear()->toDateString(), 'source' => request('source')]) }}"
           class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-6 shadow-xs hover:border-slate-400 dark:hover:border-slate-700 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500 dark:text-slate-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">This Year</p>
                <div class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['year'], 2) }}</p>
        </a>

        <!-- Overall Card -->
        <a href="{{ route('sales.records', ['source' => request('source')]) }}"
           class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-6 col-span-2 sm:col-span-1 shadow-xs hover:border-slate-400 dark:hover:border-slate-700 transition-all cursor-pointer group flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-gray-500 dark:text-slate-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Overall</p>
                <div class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['overall'], 2) }}</p>
        </a>
    </div>

    <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-xs">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-gray-200 dark:border-[#1e2536] gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Sales Records</h1>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">All recorded sales — from POS and imported data</p>
            </div>
            <!-- Import Sales Button -->
            <button type="button" onclick="toggleImportSalesModal(true)"
               class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer self-start sm:self-auto">
                Import Sales
            </button>
        </div>

        <div class="px-6 py-4 border-b border-gray-200 dark:border-[#1e2536] bg-gray-50/50 dark:bg-[#161B26]/50">
            <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3">
                <input type="hidden" name="source" value="{{ request('source') }}">
                <div class="grid grid-cols-1 sm:flex sm:items-center gap-3 flex-1">
                    <!-- FROM Date Picker with inline label -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 whitespace-nowrap">FROM:</span>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" onchange="this.form.submit()"
                               class="w-full sm:w-auto rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>

                    <!-- TO Date Picker with inline label -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 whitespace-nowrap">TO:</span>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" onchange="this.form.submit()"
                               class="w-full sm:w-auto rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>

                    <!-- Source Dropdown & Clear Filters Side-by-Side -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <select name="source" onchange="this.form.submit()" class="w-full sm:w-auto rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                            <option value="">All Sources</option>
                            <option value="pos" {{ request('source') === 'pos' ? 'selected' : '' }}>POS</option>
                            <option value="imported" {{ request('source') === 'imported' ? 'selected' : '' }}>Imported</option>
                        </select>

                        @if (request()->anyFilled(['date_from', 'date_to', 'source']))
                            <a href="{{ route('sales.records') }}" class="text-xs font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] px-3 py-2 rounded-lg transition-colors whitespace-nowrap border border-gray-300 dark:border-[#1e2536] bg-white dark:bg-[#161B26]">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Transaction ID</th>
                        <th class="px-6 py-3">Source</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Recorded By</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($records as $record)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">{{ $record->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $record->transaction_id }}</td>
                            <td class="px-6 py-4">
                                @if ($record->transaction_type === 0)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900">POS</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-900">Imported</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-slate-100 font-medium">₱{{ number_format($record->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">
                                {{ $record->user->firstname ?? 'N/A' }} {{ $record->user->lastname ?? '' }}
                            </td>
                            <td class="px-6 py-4 text-right">
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
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">No sales records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-[#1e2536] text-gray-600 dark:text-slate-400">{{ $records->links() }}</div>

    </div>

    <!-- Import Sales Modal Popup -->
    <div id="importSalesModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-xl bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-2xl shadow-2xl p-6 sm:p-8 transform transition-all text-left">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 mb-2">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Import Sales Records</h3>
                <button type="button" onclick="toggleImportSalesModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-[#10141D] transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">Upload a CSV file of historical sales to backfill demand data.</p>

            <!-- Import Form -->
            <form action="{{ route('sales.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Required Columns Box -->
                <div class="bg-gray-50 dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl p-4">
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
                           class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 dark:file:bg-[#10141D] file:text-gray-700 dark:file:text-slate-200 hover:file:bg-gray-200 dark:hover:file:bg-[#1e2536]">
                </div>

                <!-- Auto-create products checkbox card -->
                <div class="border border-gray-200 dark:border-[#1e2536] rounded-xl p-4 bg-white dark:bg-[#10141D]">
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
                    <button type="button" onclick="toggleImportSalesModal(false)" class="text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#10141D] px-4 py-2 rounded-lg transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <!-- Upload & Import Button -->
                    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
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