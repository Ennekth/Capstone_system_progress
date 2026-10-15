@extends('layouts.default')

@section('title', 'Transaction History')

@section('content')

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <!-- Header + Filters -->
        <div class="px-6 py-5 border-b border-gray-200">
            <h1 class="text-xl font-bold mb-4">Transaction History</h1>

            <form method="GET" action="{{ route('transaction.history') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transaction ID..."
                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">

                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">

                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">

                <button type="submit"
                        class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800 whitespace-nowrap">
                    Filter
                </button>

                @if (request('search') || request('date_from') || request('date_to'))
                    <a href="{{ route('transaction.history') }}"
                       class="text-center text-sm font-medium text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
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
                <tbody class="divide-y divide-gray-100">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $transaction->transaction_id }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $transaction->created_at->format('M d, Y - h:i A') }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $transaction->user->firstname ?? 'N/A' }} {{ $transaction->user->lastname ?? '' }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium">₱{{ number_format($transaction->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-gray-600">₱{{ number_format($transaction->amount_paid, 2) }}</td>
                            <td class="px-6 py-4 text-gray-600">₱{{ number_format($transaction->amount_change, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('receipt', $transaction->id) }}"
                                   class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                                    View Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                No transactions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">
            {{ $transactions->links() }}
        </div>

    </div>

@endsection