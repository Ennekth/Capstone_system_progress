@extends('layouts.default')

@section('title', 'Pending Approvals')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Pending Approvals</h1>

    @if (session('success'))
        <div class="mb-6 rounded-2xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-2 border-emerald-200 dark:border-emerald-900 px-6 py-4 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden mb-8">

        <div class="px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Pending Restock Approvals</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Staff-submitted restock requests awaiting review</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-8 py-4">Product</th>
                        <th class="px-8 py-4">Type</th>
                        <th class="px-8 py-4">Quantity</th>
                        <th class="px-8 py-4">Requested By</th>
                        <th class="px-8 py-4">Date</th>
                        <th class="px-8 py-4">Note</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($pending as $movement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-8 py-4 font-medium text-gray-900 dark:text-white">
                                {{ $movement->product->product_name ?? 'Deleted Product' }}
                            </td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400">
                                {{ ucwords(str_replace('_', ' ', $movement->type)) }}
                            </td>
                            <td class="px-8 py-4 text-gray-900 dark:text-slate-100 font-semibold">
                                +{{ $movement->quantity }}
                            </td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400">
                                {{ $movement->requester->firstname ?? 'N/A' }} {{ $movement->requester->lastname ?? '' }}
                            </td>
                            <td class="px-8 py-4 text-gray-500 dark:text-slate-400 text-xs">
                                {{ $movement->created_at->format('M d, Y - h:i A') }}
                            </td>
                            <td class="px-8 py-4 text-gray-500 dark:text-slate-400">
                                {{ $movement->note ?? '—' }}
                            </td>
                            <td class="px-8 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <form action="{{ route('inventory.approve', $movement) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Approve this restock? Stock will be updated immediately.');"
                                                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors shadow-xs">
                                            Approve
                                        </button>
                                    </form>

                                    <button type="button"
                                            onclick="document.getElementById('reject-{{ $movement->id }}').classList.toggle('hidden')"
                                            class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors shadow-xs">
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Reject reason form (hidden by default) -->
                        <tr id="reject-{{ $movement->id }}" class="hidden bg-red-50/50 dark:bg-red-950/20">
                            <td colspan="7" class="px-8 py-4 border-t border-red-100 dark:border-red-900/30">
                                <form action="{{ route('inventory.reject', $movement) }}" method="POST" class="flex items-end gap-3">
                                    @csrf
                                    <div class="flex-1">
                                        <label class="block text-xs font-bold text-gray-600 dark:text-slate-300 uppercase mb-1">
                                            Reason for rejection
                                        </label>
                                        <input type="text" name="rejection_reason" required
                                               placeholder="e.g. Quantity seems incorrect, please recheck"
                                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-slate-400">
                                    </div>
                                    <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-5 py-2.5 rounded-xl transition-colors shadow-xs whitespace-nowrap">
                                        Confirm Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">
                                No pending approvals right now.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pill Pagination Footer (Auto-hides if items <= 4) -->
        @if ($pending->hasPages())
            <div class="px-8 py-5 border-t-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-slate-400">
                <div>
                    Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $pending->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900 dark:text-white">{{ $pending->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $pending->total() }}</span> results
                </div>

                <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
                    @if ($pending->onFirstPage())
                        <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                            Previous
                        </span>
                    @else
                        <a href="{{ $pending->previousPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                            Previous
                        </a>
                    @endif

                    @foreach ($pending->getUrlRange(max(1, $pending->currentPage() - 2), min($pending->lastPage(), $pending->currentPage() + 2)) as $page => $url)
                        @if ($page == $pending->currentPage())
                            <span class="px-4 py-2 text-xs font-bold text-white bg-slate-900 dark:bg-[#1E293B] rounded-full shadow-xs border border-transparent">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if ($pending->hasMorePages())
                        <a href="{{ $pending->nextPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
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