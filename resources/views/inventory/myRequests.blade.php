@extends('layouts.default')

@section('title', 'My Restock Requests')

@section('content')

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <div class="px-6 py-5 border-b border-gray-200">
            <h1 class="text-xl font-bold mb-4">My Restock Requests</h1>

            <form method="GET" class="flex gap-3">
                <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                    Filter
                </button>
                @if (request('status'))
                    <a href="{{ route('inventory.myRequests') }}" class="text-sm font-medium text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg">
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
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Quantity</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Reviewed By</th>
                        <th class="px-6 py-3">Note / Reason</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                {{ $req->created_at->format('M d, Y - h:i A') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $req->product->product_name ?? 'Deleted Product' }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium">
                                {{ $req->quantity }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($req->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                                @elseif ($req->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Approved</span>
                                @elseif ($req->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $req->approver->firstname ?? '—' }} {{ $req->approver->lastname ?? '' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                @if ($req->status === 'rejected' && $req->rejection_reason)
                                    <span class="text-red-600">{{ $req->rejection_reason }}</span>
                                @else
                                    {{ $req->note ?? '—' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($req->status === 'pending')
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('inventory.editRequest', $req) }}" class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('inventory.cancelRequest', $req) }}" method="POST"
                                              onsubmit="return confirm('Withdraw this request?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                Withdraw
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                You haven't submitted any restock requests yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">
            {{ $requests->links() }}
        </div>

    </div>

@endsection