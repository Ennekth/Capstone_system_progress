@extends('layouts.default')

@section('title', 'Transaction History')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Transaction History</h1>

    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden mb-8">

        <!-- Header + Filters -->
        <div class="px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
            <!-- Instruction Subhead for Users -->
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">
                Type in the search bar or pick a date range to automatically filter transactions instantly.
            </p>

            <form id="filterForm" method="GET" action="{{ route('transaction.history') }}" class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                
                <div class="flex flex-col sm:flex-row flex-wrap gap-4 items-center w-full">
                    <!-- Search Input -->
                    <div class="flex items-center gap-2 flex-1 min-w-[260px] w-full sm:w-auto">
                        <label class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase whitespace-nowrap">Search:</label>
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search ID or Cashier..." autocomplete="off"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                                      focus:outline-none focus:border-slate-400 dark:focus:border-slate-500 transition-colors">
                    </div>

                    <!-- Date From Filter -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label for="date_from" class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase whitespace-nowrap cursor-pointer">From:</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                               class="w-full sm:w-auto rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 dark:focus:border-slate-500 cursor-pointer">
                    </div>

                    <!-- Date To Filter -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <label for="date_to" class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase whitespace-nowrap cursor-pointer">To:</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                               class="w-full sm:w-auto rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 dark:focus:border-slate-500 cursor-pointer">
                    </div>
                </div>

                <!-- Clear Button -->
                @if (request('search') || request('date_from') || request('date_to'))
                    <div class="w-full lg:w-auto flex justify-end">
                        <a href="{{ route('transaction.history') }}"
                           class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs whitespace-nowrap flex items-center justify-center">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-8 py-4">Transaction ID</th>
                        <th class="px-8 py-4">Date</th>
                        <th class="px-8 py-4">Cashier</th>
                        <th class="px-8 py-4">Total</th>
                        <th class="px-8 py-4">Amount Paid</th>
                        <th class="px-8 py-4">Change</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-8 py-4 font-medium text-gray-900 dark:text-white">{{ $transaction->transaction_id }}</td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400">{{ $transaction->created_at->format('M d, Y - h:i A') }}</td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400">
                                {{ $transaction->user->firstname ?? 'N/A' }} {{ $transaction->user->lastname ?? '' }}
                            </td>
                            <td class="px-8 py-4 text-gray-900 dark:text-slate-100 font-medium">₱{{ number_format($transaction->total_amount, 2) }}</td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400">₱{{ number_format($transaction->amount_paid, 2) }}</td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400">₱{{ number_format($transaction->amount_change, 2) }}</td>
                            <td class="px-8 py-4 text-right">
                                <a href="{{ route('receipt', $transaction->id) }}"
                                   class="text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white font-medium text-sm transition-colors">
                                    View Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pill Pagination Footer (Shows only when total items exceed per page) -->
        @if ($transactions->hasPages())
            <div class="px-8 py-5 border-t-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-slate-400">
                <div>
                    Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $transactions->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900 dark:text-white">{{ $transactions->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $transactions->total() }}</span> results
                </div>

                <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
                    @if ($transactions->onFirstPage())
                        <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                            Previous
                        </span>
                    @else
                        <a href="{{ $transactions->previousPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                            Previous
                        </a>
                    @endif

                    @foreach ($transactions->getUrlRange(max(1, $transactions->currentPage() - 2), min($transactions->lastPage(), $transactions->currentPage() + 2)) as $page => $url)
                        @if ($page == $transactions->currentPage())
                            <span class="px-4 py-2 text-xs font-bold text-white bg-slate-900 dark:bg-[#1E293B] rounded-full shadow-xs border border-transparent">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if ($transactions->hasMorePages())
                        <a href="{{ $transactions->nextPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
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
            const savedCursorPos = sessionStorage.getItem('searchCursorPos');
            if (savedCursorPos !== null) {
                searchInput.focus();
                searchInput.setSelectionRange(parseInt(savedCursorPos), parseInt(savedCursorPos));
                sessionStorage.removeItem('searchCursorPos');
            } else if (searchInput.value.length > 0) {
                searchInput.focus();
                searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
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