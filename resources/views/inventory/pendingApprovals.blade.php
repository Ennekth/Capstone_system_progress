@extends('layouts.default')

@section('title', 'Pending Approvals')

@section('content')

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <div class="px-6 py-5 border-b border-gray-200">
            <h1 class="text-xl font-bold">Pending Restock Approvals</h1>
            <p class="text-sm text-gray-500 mt-1">Staff-submitted restock requests awaiting review</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Quantity</th>
                        <th class="px-6 py-3">Requested By</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Note</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pending as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $movement->product->product_name ?? 'Deleted Product' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ ucwords(str_replace('_', ' ', $movement->type)) }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium">
                                +{{ $movement->quantity }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $movement->requester->firstname ?? 'N/A' }} {{ $movement->requester->lastname ?? '' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $movement->created_at->format('M d, Y - h:i A') }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $movement->note ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <form action="{{ route('inventory.approve', $movement) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Approve this restock? Stock will be updated immediately.');"
                                                class="bg-green-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-green-700">
                                            Approve
                                        </button>
                                    </form>

                                    <button type="button"
                                            onclick="document.getElementById('reject-{{ $movement->id }}').classList.toggle('hidden')"
                                            class="bg-red-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-red-700">
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Reject reason form (hidden by default) -->
                        <tr id="reject-{{ $movement->id }}" class="hidden bg-red-50">
                            <td colspan="7" class="px-6 py-4">
                                <form action="{{ route('inventory.reject', $movement) }}" method="POST" class="flex items-end gap-3">
                                    @csrf
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">
                                            Reason for rejection
                                        </label>
                                        <input type="text" name="rejection_reason" required
                                               placeholder="e.g. Quantity seems incorrect, please recheck"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    </div>
                                    <button type="submit"
                                            class="bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700">
                                        Confirm Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                No pending approvals right now.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">
            {{ $pending->links() }}
        </div>

    </div>

@endsection