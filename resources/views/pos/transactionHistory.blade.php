@extends('layouts.default')

@section('title', 'Transaction History')

@section('content')

    <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm">

        <!-- Header + Filters -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-[#1e2536]">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Transaction History</h1>
            <!-- Instruction Subhead for Users -->
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1 mb-4">
                Type in the search bar or pick a date range to automatically filter transactions instantly.
            </p>

            <form id="filterForm" method="GET" action="{{ route('transaction.history') }}" class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                
                <div class="flex flex-col sm:flex-row flex-wrap gap-4 items-center w-full">
                    <!-- Search Input (Label Beside) -->
                    <div class="flex items-center gap-2 flex-1 min-w-[260px] w-full sm:w-auto">
                        <label class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase whitespace-nowrap">Search:</label>
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search ID or Cashier..." autocomplete="off"
                               class="w-full rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                                      focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>

                    <!-- Date From Filter (Label Beside) -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label for="date_from" class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase whitespace-nowrap cursor-pointer">From:</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                               class="w-full sm:w-auto rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400 cursor-pointer">
                    </div>

                    <!-- Date To Filter (Label Beside) -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label for="date_to" class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase whitespace-nowrap cursor-pointer">To:</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                               class="w-full sm:w-auto rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400 cursor-pointer">
                    </div>
                </div>

                <!-- Clear Button (Appears only when filtering) -->
                @if (request('search') || request('date_from') || request('date_to'))
                    <div class="w-full lg:w-auto flex justify-end">
                        <a href="{{ route('transaction.history') }}"
                           class="text-center text-sm font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] px-4 py-2 rounded-lg transition-colors flex items-center justify-center whitespace-nowrap border border-gray-300 dark:border-[#1e2536]">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-6 py-3">Transaction ID</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Cashier</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Amount Paid</th>
                        <th class="px-6 py-3">Change</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $transaction->transaction_id }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">{{ $transaction->created_at->format('M d, Y - h:i A') }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">
                                {{ $transaction->user->firstname ?? 'N/A' }} {{ $transaction->user->lastname ?? '' }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-slate-100 font-medium">₱{{ number_format($transaction->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">₱{{ number_format($transaction->amount_paid, 2) }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">₱{{ number_format($transaction->amount_change, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('receipt', $transaction->id) }}"
                                   class="text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white font-medium text-sm transition-colors">
                                    View Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">
                                No transactions found[cite: 1, 2].
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-[#1e2536] text-gray-600 dark:text-slate-400">
            {{ $transactions->links() }}
        </div>

    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('filterForm');
        const dateFrom = document.getElementById('date_from');
        const dateTo = document.getElementById('date_to');
        let debounceTimer;

        if (searchInput) {
            // Restore cursor position from session storage if it was saved prior to submit
            const savedCursorPos = sessionStorage.getItem('searchCursorPos');
            if (savedCursorPos !== null) {
                searchInput.focus();
                searchInput.setSelectionRange(parseInt(savedCursorPos), parseInt(savedCursorPos));
                sessionStorage.removeItem('searchCursorPos');
            } else if (searchInput.value.length > 0) {
                // Default fallback: focus at the end of text
                searchInput.focus();
                searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
            }

            // Live Search with 300ms debounce
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                
                // Save current cursor position right before submitting
                sessionStorage.setItem('searchCursorPos', searchInput.selectionStart);

                debounceTimer = setTimeout(function () {
                    filterForm.submit();
                }, 300);
            });
        }

        if (dateFrom && filterForm) {
            dateFrom.addEventListener('change', function () {
                filterForm.submit();
            });
        }

        if (dateTo && filterForm) {
            dateTo.addEventListener('change', function () {
                filterForm.submit();
            });
        }
    });
</script>
@endpush