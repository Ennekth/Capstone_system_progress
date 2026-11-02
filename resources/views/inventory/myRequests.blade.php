@extends('layouts.default')

@section('title', 'My Restock Requests')

@section('content')

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 dark:bg-emerald-950/85 text-green-800 dark:text-emerald-300 border border-green-200 dark:border-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 dark:bg-rose-950/85 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm">

        <div class="px-6 py-5 border-b border-gray-200 dark:border-[#1e2536]">
            <h1 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">My Restock Requests</h1>

            <form method="GET" class="flex items-center gap-3">
                <select name="status" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                <!-- Filter Button (Option B Style) -->
                <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    Filter
                </button>

                @if (request('status'))
                    <a href="{{ route('inventory.myRequests') }}" class="text-sm font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] px-4 py-2 rounded-lg transition-colors">
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
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Quantity</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Reviewed By</th>
                        <th class="px-6 py-3">Note / Reason</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($requests as $req)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-6 py-4 text-gray-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $req->created_at->format('M d, Y - h:i A') }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $req->product->product_name ?? 'Deleted Product' }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 dark:text-slate-200 font-medium">
                                {{ $req->quantity }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($req->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900">Pending</span>
                                @elseif ($req->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-emerald-950/80 text-green-700 dark:text-emerald-300 border border-green-200 dark:border-emerald-900">Approved</span>
                                @elseif ($req->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-rose-950/80 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">
                                {{ $req->approver->firstname ?? '—' }} {{ $req->approver->lastname ?? '' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-slate-400">
                                @if ($req->status === 'rejected' && $req->rejection_reason)
                                    <span class="text-red-600 dark:text-rose-400">{{ $req->rejection_reason }}</span>
                                @else
                                    {{ $req->note ?? '—' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($req->status === 'pending')
                                    <div class="flex justify-end gap-2 items-center">
                                        <!-- Edit Button (Option B Style) -->
                                        <a href="{{ route('inventory.editRequest', $req) }}" 
                                           class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                            Edit
                                        </a>

                                        <!-- Withdraw Button (Destructive Rose Style) -->
                                        <form action="{{ route('inventory.cancelRequest', $req) }}" method="POST"
                                              onsubmit="return confirm('Withdraw this request?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                                Withdraw
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-300 dark:text-slate-600 text-sm">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">
                                You haven't submitted any restock requests yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 text-gray-600 dark:text-slate-400">
            {{ $requests->links() }}
        </div>

    </div>

@endsection