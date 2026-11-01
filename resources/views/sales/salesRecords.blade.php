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

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl p-5">
                <p class="text-sm text-gray-500 dark:text-slate-400">Today</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['today'], 2) }}</p>
            </div>
            <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl p-5">
                <p class="text-sm text-gray-500 dark:text-slate-400">This Week</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['week'], 2) }}</p>
            </div>
            <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl p-5">
                <p class="text-sm text-gray-500 dark:text-slate-400">This Month</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['month'], 2) }}</p>
            </div>
            <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl p-5">
                <p class="text-sm text-gray-500 dark:text-slate-400">This Year</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['year'], 2) }}</p>
            </div>
            <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl p-5 col-span-2 sm:col-span-1">
                <p class="text-sm text-gray-500 dark:text-slate-400">Overall</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">₱{{ number_format($summary['overall'], 2) }}</p>
            </div>
        </div>

    <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm">

        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 dark:border-[#1e2536]">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Sales Records</h1>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">All recorded sales — from POS and imported data</p>
            </div>
            <!-- Import Sales Button (Option B Style) -->
            <button type="button" onclick="toggleImportSalesModal(true)"
               class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                Import Sales
            </button>
        </div>

        <div class="px-6 py-4 border-b border-gray-200 dark:border-[#1e2536] bg-gray-50/50 dark:bg-[#161B26]/50">
            <form method="GET" class="flex flex-wrap gap-3">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                <select name="source" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="">All Sources</option>
                    <option value="pos" {{ request('source') === 'pos' ? 'selected' : '' }}>POS</option>
                    <option value="imported" {{ request('source') === 'imported' ? 'selected' : '' }}>Imported</option>
                </select>
                <!-- Filter Button (Option B Style) -->
                <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    Filter
                </button>
                @if (request()->anyFilled(['date_from', 'date_to', 'source']))
                    <a href="{{ route('sales.records') }}" class="text-sm font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] px-4 py-2 rounded-lg transition-colors">
                        Clear
                    </a>
                @endif
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
                    <!-- Upload & Import Button (Option B Style) -->
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