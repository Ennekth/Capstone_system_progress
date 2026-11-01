@extends('layouts.default')

@section('title', 'Stock Logs')

@section('content')

    <div class="space-y-4">

        @forelse ($grouped as $batchId => $movements)
            @php
                $first = $movements->first();
            @endphp

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 px-6 py-4 border-b border-gray-200">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ ucwords(str_replace('_', ' ', $first->type)) }} Batch
                        </p>
                        <p class="text-xs text-gray-500">{{ $first->created_at->format('M d, Y - h:i A') }}</p>
                    </div>
                    <div class="text-xs text-gray-600 text-right">
                        <p>Requested by: <span class="font-medium">{{ $first->requester->firstname ?? 'N/A' }} {{ $first->requester->lastname ?? '' }}</span></p>
                        <p>Approved by: <span class="font-medium">{{ $first->approver->firstname ?? '—' }} {{ $first->approver->lastname ?? '' }}</span></p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-2">Product</th>
                                <th class="px-6 py-2">Previous Qty</th>
                                <th class="px-6 py-2">Added</th>
                                <th class="px-6 py-2">New Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($movements as $movement)
                                <tr>
                                    <td class="px-6 py-2 font-medium text-gray-900">
                                        {{ $movement->product->product_name ?? 'Deleted Product' }}
                                    </td>
                                    <td class="px-6 py-2 text-gray-600">{{ $movement->quantity_before }}</td>
                                    <td class="px-6 py-2 text-green-600 font-medium">+{{ $movement->quantity }}</td>
                                    <td class="px-6 py-2 text-gray-900 font-semibold">{{ $movement->quantity_after }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($first->note)
                    <div class="px-6 py-3 text-sm text-gray-500 border-t border-gray-100">
                        Note: {{ $first->note }}
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-10 text-center text-gray-400">
                No stock movement logs found.
            </div>
        @endforelse

    </div>

@endsection