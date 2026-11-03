@extends('layouts.default')

@section('title', 'Stock Logs')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Stock Logs</h1>

    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden mb-8">

        <!-- Header / Category Filter Bar -->
        <div class="px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Movement Batches</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Review historical stock adjustments and transfer logs</p>
            </div>

            <form method="GET" action="{{ route('inventory.logs') }}" class="flex items-center gap-3 w-full sm:w-auto">
                <select name="category_id" onchange="this.form.submit()"
                        class="w-full sm:w-64 rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 dark:focus:border-slate-500 cursor-pointer">
                    <option value="">All Categories (Default)</option>
                    
                    {{-- Render grouped categories if available, else flat --}}
                    @if(isset($categoryGroups))
                        @foreach ($categoryGroups as $groupName => $groupCategories)
                            <optgroup label="{{ strtoupper($groupName) }}">
                                @foreach ($groupCategories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&mdash; {{ $cat->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    @else
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @if (request('category_id'))
                    <a href="{{ route('inventory.logs') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white whitespace-nowrap">Reset</a>
                @endif
            </form>
        </div>

        <!-- Batches Container -->
        <div class="p-8 space-y-6">
            @forelse ($grouped as $batchId => $movements)
                @php
                    $first = $movements->first();
                    $statusColors = [
                        'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900',
                        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-900',
                        'rejected' => 'bg-red-100 text-red-700 dark:bg-rose-950/80 dark:text-rose-300 border border-red-200 dark:border-rose-900',
                    ];
                @endphp

                <div class="bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] rounded-2xl shadow-xs overflow-hidden">
                    <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b-2 border-gray-200 dark:border-[#1e2536] bg-gray-50 dark:bg-[#10141D]/50">
                        <div class="flex items-center gap-3">
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ ucwords(str_replace('_', ' ', $first->type)) }} Batch
                                </p>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ $first->created_at->format('M d, Y - h:i A') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$first->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($first->status) }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-600 dark:text-slate-400 text-right">
                            <p>Requested by: <span class="font-semibold text-gray-900 dark:text-white">{{ $first->requester->firstname ?? 'N/A' }} {{ $first->requester->lastname ?? '' }}</span></p>
                            <p>Approved by: <span class="font-semibold text-gray-900 dark:text-white">{{ $first->approver->firstname ?? '—' }} {{ $first->approver->lastname ?? '' }}</span></p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-white dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536]">
                                <tr>
                                    <th class="px-6 py-3">Product</th>
                                    <th class="px-6 py-3">Category</th>
                                    <th class="px-6 py-3">Previous Qty</th>
                                    <th class="px-6 py-3">Added / Changed</th>
                                    <th class="px-6 py-3">New Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                                @foreach ($movements as $movement)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/30 transition-colors">
                                        <td class="px-6 py-3.5 font-medium text-gray-900 dark:text-white">
                                            {{ $movement->product->product_name ?? 'Deleted Product' }}
                                        </td>
                                        <td class="px-6 py-3.5 text-gray-600 dark:text-slate-400">
                                            {{ $movement->product->category->name ?? '—' }}
                                        </td>
                                        <td class="px-6 py-3.5 text-gray-600 dark:text-slate-400">{{ $movement->quantity_before ?? '—' }}</td>
                                        <td class="px-6 py-3.5 text-emerald-600 dark:text-emerald-400 font-semibold">+{{ $movement->quantity }}</td>
                                        <td class="px-6 py-3.5 text-gray-900 dark:text-white font-bold">{{ $movement->quantity_after ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($first->note)
                        <div class="px-6 py-3.5 text-xs text-gray-500 dark:text-slate-400 border-t border-gray-200 dark:border-[#1e2536] bg-gray-50/50 dark:bg-[#10141D]/30">
                            <span class="font-bold uppercase tracking-wider text-gray-400">Note:</span> {{ $first->note }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="py-12 text-center text-gray-400 dark:text-slate-500 font-medium">
                    No stock movement logs found.
                </div>
            @endforelse
        </div>

        <!-- Custom Pill Pagination Footer -->
        @if ($grouped->hasPages())
            <div class="px-8 py-5 border-t-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-slate-400">
                <div>
                    Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $grouped->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900 dark:text-white">{{ $grouped->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $grouped->total() }}</span> results
                </div>

                <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
                    @if ($grouped->onFirstPage())
                        <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                            Previous
                        </span>
                    @else
                        <a href="{{ $grouped->previousPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                            Previous
                        </a>
                    @endif

                    @foreach ($grouped->getUrlRange(max(1, $grouped->currentPage() - 2), min($grouped->lastPage(), $grouped->currentPage() + 2)) as $page => $url)
                        @if ($page == $grouped->currentPage())
                            <span class="px-4 py-2 text-xs font-bold text-white bg-slate-900 dark:bg-[#1E293B] rounded-full shadow-xs border border-transparent">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if ($grouped->hasMorePages())
                        <a href="{{ $grouped->nextPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
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