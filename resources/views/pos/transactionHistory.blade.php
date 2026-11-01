@extends('layouts.default')

@section('title', 'Transaction History')

@section('content')

    <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm">

        <!-- Header + Filters -->
        <div class="px-6 py-5 border-b border-gray-200 dark:border-[#1e2536]">
            <h1 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Transaction History</h1>

            <form method="GET" action="{{ route('transaction.history') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transaction ID..."
                       class="flex-1 rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                              focus:outline-none focus:ring-1 focus:ring-slate-400">

                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100
                              focus:outline-none focus:ring-1 focus:ring-slate-400">

                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100
                              focus:outline-none focus:ring-1 focus:ring-slate-400">

                <!-- Filter Button (Option B Style) -->
                <button type="submit"
                        class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-300 ease-in-out whitespace-nowrap shadow-xs cursor-pointer">
                    Filter
                </button>

                @if (request('search') || request('date_from') || request('date_to'))
                    <a href="{{ route('transaction.history') }}"
                       class="text-center text-sm font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] px-4 py-2 rounded-lg transition-colors">
                        Clear
                    </a>
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
                                No transactions found.
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