@extends('layouts.default')

@section('title', 'Sales Records')

@section('content')

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    @if (session('skipped') && count(session('skipped')) > 0)
        <div class="mb-4 rounded-lg bg-amber-100 text-amber-800 px-4 py-3 text-sm">
            <p class="font-medium mb-1">Some rows were skipped:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach (session('skipped') as $issue)
                    <li>{{ $issue }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-sm text-gray-500">Today</p>
                <p class="text-xl font-bold">₱{{ number_format($summary['today'], 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-sm text-gray-500">This Week</p>
                <p class="text-xl font-bold">₱{{ number_format($summary['week'], 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-sm text-gray-500">This Month</p>
                <p class="text-xl font-bold">₱{{ number_format($summary['month'], 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-sm text-gray-500">This Year</p>
                <p class="text-xl font-bold">₱{{ number_format($summary['year'], 2) }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-sm text-gray-500">Overall</p>
                <p class="text-xl font-bold">₱{{ number_format($summary['overall'], 2) }}</p>
            </div>
        </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-bold">Sales Records</h1>
                <p class="text-sm text-gray-500 mt-1">All recorded sales — from POS and imported data</p>
            </div>
            <a href="{{ route('sales.import') }}"
               class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-800">
                Import Sales
            </a>
        </div>

        <div class="px-6 py-4 border-b border-gray-200">
            <form method="GET" class="flex flex-wrap gap-3">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <select name="source" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">All Sources</option>
                    <option value="pos" {{ request('source') === 'pos' ? 'selected' : '' }}>POS</option>
                    <option value="imported" {{ request('source') === 'imported' ? 'selected' : '' }}>Imported</option>
                </select>
                <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                    Filter
                </button>
                @if (request()->anyFilled(['date_from', 'date_to', 'source']))
                    <a href="{{ route('sales.records') }}" class="text-sm font-medium text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Transaction ID</th>
                        <th class="px-6 py-3">Source</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Recorded By</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($records as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-600">{{ $record->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $record->transaction_id }}</td>
                            <td class="px-6 py-4">
                                @if ($record->transaction_type === 0)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">POS</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">Imported</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium">₱{{ number_format($record->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $record->user->firstname ?? 'N/A' }} {{ $record->user->lastname ?? '' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('receipt', $record->id) }}" class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                                    View
                                </a>
                                @if ($record->transaction_type === 1)
                                    <form action="{{ route('sales.destroy', $record) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Delete this imported record? This will also affect demand forecasting.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm ml-3">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">No sales records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $records->links() }}</div>

    </div>

@endsection