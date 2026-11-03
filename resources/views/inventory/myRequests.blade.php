@extends('layouts.default')

@section('title', 'My Restock Requests')

@section('content')

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-100 dark:bg-rose-950/85 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900 px-4 py-3 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm">

        <div class="px-6 py-5 border-b border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">My Restock Requests</h1>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Submit and track your stock replenishment requests.</p>
            </div>

            <!-- Request Stocks Button -->
            <button type="button" onclick="openRequestModal()"
                    class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer flex items-center gap-2 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Request Stocks
            </button>
        </div>

        <!-- Filter Dropdown Bar -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-[#1e2536] bg-gray-50/50 dark:bg-[#161B26]/30 flex items-center justify-between">
            <form method="GET" class="flex items-center gap-3">
                <select name="status" onchange="this.form.submit()" class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-300 dark:border-[#1e2536] px-4 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400 cursor-pointer">
                    <option value="">All Delivery Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="not_approved" {{ request('status') === 'not_approved' ? 'selected' : '' }}>Not Approved</option>
                </select>

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
                        <th class="px-6 py-3">Request Status</th>
                        <th class="px-6 py-3">Delivery Status</th>
                        <th class="px-6 py-3">Reviewed By</th>
                        <th class="px-6 py-3">Note / Reason</th>
                        <th class="px-6 py-3 text-right">Action</th>
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
                            <!-- Request Status -->
                            <td class="px-6 py-4">
                                @if ($req->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900">Pending</span>
                                @elseif ($req->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-emerald-950/80 text-green-700 dark:text-emerald-300 border border-green-200 dark:border-emerald-900">Approved</span>
                                @elseif ($req->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-rose-950/80 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900">Rejected</span>
                                @endif
                            </td>
                            <!-- Delivery Status -->
                            <td class="px-6 py-4">
                                @if ($req->delivery_status === 'received')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-emerald-950/80 text-green-700 dark:text-emerald-300 border border-green-200 dark:border-emerald-900">Received</span>
                                @elseif ($req->status === 'approved' || $req->delivery_status === 'in_transit')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900">In Transit</span>
                                @elseif ($req->delivery_status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900">Pending</span>
                                @elseif ($req->delivery_status === 'not_approved' || $req->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-rose-950/80 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900">Not Approved</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">{{ $req->delivery_status ?? '—' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">
                                {{ $req->approver->firstname ?? '—' }} {{ $req->approver->lastname ?? '' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-slate-400">
                                @if (($req->delivery_status === 'not_approved' || $req->status === 'rejected') && $req->rejection_reason)
                                    <span class="text-red-600 dark:text-rose-400">{{ $req->rejection_reason }}</span>
                                @else
                                    {{ $req->note ?? '—' }}
                                @endif
                            </td>
                            <!-- Action Column: Mark as Received Button (Appears only when Request Status is Approved) -->
                            <td class="px-6 py-4 text-right">
                                @if ($req->status === 'approved' && $req->delivery_status !== 'received')
                                    <form action="{{ route('inventory.fulfill', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-3.5 py-2 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            Mark as Received
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 dark:text-slate-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">
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

    <!-- Request Stocks Modal -->
    <div id="requestModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs hidden">
        <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl p-6 w-full max-w-lg shadow-xl mx-4">
            <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-[#1e2536]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">New Restock Request</h3>
                <button type="button" onclick="closeRequestModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('inventory.storeRequest') }}" method="POST" class="space-y-4 pt-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-400 mb-1.5">Product</label>
                    <select name="product_id" required class="w-full rounded-xl bg-gray-50 dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Select a product...</option>
                        @foreach (\App\Models\Product::orderBy('product_name')->get() as $p)
                            <option value="{{ $p->id }}">{{ $p->product_name }} (Store: {{ $p->store_quantity }}, WH: {{ $p->warehouse_quantity }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-400 mb-1.5">Location</label>
                    <select name="location" required class="w-full rounded-xl bg-gray-50 dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @if (auth()->user()->role === 'staff')
                            <option value="store">Store</option>
                        @elseif (auth()->user()->role === 'warehouse_staff')
                            <option value="warehouse">Warehouse</option>
                        @else
                            <option value="store">Store</option>
                            <option value="warehouse">Warehouse</option>
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-400 mb-1.5">Quantity</label>
                    <input type="number" name="quantity" min="1" required class="w-full rounded-xl bg-gray-50 dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Enter quantity">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-slate-400 mb-1.5">Note / Reason (Optional)</label>
                    <textarea name="note" rows="3" class="w-full rounded-xl bg-gray-50 dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Add any details or reason for request..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-[#1e2536]">
                    <button type="button" onclick="closeRequestModal()" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#161B26] rounded-xl transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRequestModal() {
            document.getElementById('requestModal').classList.remove('hidden');
        }
        function closeRequestModal() {
            document.getElementById('requestModal').classList.add('hidden');
        }
    </script>

@endsection