@extends('layouts.default')

@section('title', 'Restock Mode')

@section('content')

    @if (session('error'))
        <div class="mb-6 rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Restock Mode</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">
                Type a quantity, press <kbd class="px-2 py-0.5 bg-gray-100 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] text-gray-800 dark:text-slate-200 rounded-lg text-xs">Enter</kbd> or
                <kbd class="px-2 py-0.5 bg-gray-100 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] text-gray-800 dark:text-slate-200 rounded-lg text-xs">↓</kbd> to move to the next item.
            </p>
        </div>
        <a href="{{ route('inventory.index') }}" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer inline-flex items-center">
            Exit Restock Mode
        </a>
    </div>

    <!-- Live Search & Category Filter Bar -->
    <form id="filterForm" method="GET" class="flex flex-col sm:flex-row gap-4 mb-8">
        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search products..." autocomplete="off"
               class="flex-1 rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-slate-400">
        
        <select name="category_id" onchange="this.form.submit()" class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer min-w-[200px]">
            <option value="">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        @if (request()->anyFilled(['search', 'category_id']))
            <a href="{{ request()->url() }}" class="text-sm font-semibold text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] px-4 py-3 rounded-xl transition-colors flex items-center justify-center">
                Clear
            </a>
        @endif
    </form>

    <form method="POST" action="{{ route('inventory.restockBatch.store') }}" id="restockForm">
        @csrf

        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden">

            <!-- Batch Configuration Header -->
            <div class="flex flex-wrap items-end gap-4 px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536] bg-gray-50/50 dark:bg-[#161B26]/50">
                <div class="flex-1 min-w-[250px]">
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Batch Note (optional)</label>
                    <input type="text" name="note" placeholder="e.g. Weekly delivery from supplier"
                           class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-slate-400">
                </div>

                <!-- Done — Submit Batch Button -->
                <button type="button" onclick="toggleRestockConfirmModal(true)"
                        class="bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white text-sm font-semibold px-6 py-3 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    Done — Submit Batch
                </button>
            </div>

            <!-- Products Table with Per-Item Location & Status Columns -->
            <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536] sticky top-0">
                        <tr>
                            <th class="px-8 py-4">Product</th>
                            <th class="px-8 py-4 w-60">Add Quantity & Destination</th>
                            <th class="px-8 py-4">Store Qty</th>
                            <th class="px-8 py-4">Warehouse Qty</th>
                            <th class="px-8 py-4">Store Status</th>
                            <th class="px-8 py-4">Warehouse Status</th>
                            <th class="px-8 py-4">Total Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($products as $index => $product)
                            @php
                                $storeQty = $product->store_quantity ?? 0;
                                $whQty = $product->warehouse_quantity ?? 0;
                                $totalQty = $storeQty + $whQty;
                                $reorder = $product->reorder_point ?? 10;

                                $storeStatus = $storeQty == 0 ? 'Out of Stock' : ($storeQty <= $reorder ? 'Low Stock' : 'In Stock');
                                $whStatus = $whQty == 0 ? 'Out of Stock' : ($whQty <= $reorder ? 'Low Stock' : 'In Stock');
                                $totalStatus = $totalQty == 0 ? 'Out of Stock' : ($totalQty <= $reorder ? 'Low Stock' : 'In Stock');
                            @endphp

                            <tr id="row-{{ $index }}" class="restock-row transition-colors">
                                <td class="px-8 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $product->product_name }}</div>
                                    @if ($product->category)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $product->category->name }}</div>
                                    @endif
                                </td>
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-2">
                                        <input type="number" min="0"
                                            name="quantities[{{ $product->id }}]"
                                            data-index="{{ $index }}"
                                            placeholder="0"
                                            class="restlink restock-input w-24 rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100
                                                    focus:outline-none focus:border-slate-400">
                                        
                                        <select name="locations[{{ $product->id }}]"
                                            class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-3 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 focus:outline-none focus:border-slate-400 cursor-pointer">
                                            <option value="store">Store</option>
                                            <option value="warehouse">Warehouse</option>
                                        </select>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-semibold">{{ $storeQty }}</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-semibold">{{ $whQty }}</td>
                                
                                <!-- Store Status Pill -->
                                <td class="px-8 py-4">
                                    @if ($storeStatus === 'Out of Stock')
                                        <span class="inline-block bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-red-200 dark:border-rose-900">Out of Stock</span>
                                    @elseif ($storeStatus === 'Low Stock')
                                        <span class="inline-block bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-amber-200 dark:border-amber-900">Low Stock</span>
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-900">In Stock</span>
                                    @endif
                                </td>

                                <!-- Warehouse Status Pill -->
                                <td class="px-8 py-4">
                                    @if ($whStatus === 'Out of Stock')
                                        <span class="inline-block bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-red-200 dark:border-rose-900">Out of Stock</span>
                                    @elseif ($whStatus === 'Low Stock')
                                        <span class="inline-block bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-amber-200 dark:border-amber-900">Low Stock</span>
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-900">In Stock</span>
                                    @endif
                                </td>

                                <!-- Total Status Pill -->
                                <td class="px-8 py-4">
                                    @if ($totalStatus === 'Out of Stock')
                                        <span class="inline-block bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-red-200 dark:border-rose-900">Out of Stock</span>
                                    @elseif ($totalStatus === 'Low Stock')
                                        <span class="inline-block bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-amber-200 dark:border-amber-900">Low Stock</span>
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-900">In Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">
                                    No products found for restock.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Custom Confirmation Modal Popup -->
        <div id="restockConfirmModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left" onclick="event.stopPropagation()">
                
                <div class="flex items-center justify-between pb-4 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Confirm Restock Batch</h3>
                    <button type="button" onclick="toggleRestockConfirmModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#10141D] transition-colors text-xl font-bold">
                        &times;
                    </button>
                </div>

                <p class="text-sm text-gray-600 dark:text-slate-300 mb-8 font-medium">
                    Are you sure you want to submit this restocking batch? Please review your entered quantities and destinations before proceeding.
                </p>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                    <button type="button" onclick="toggleRestockConfirmModal(false)" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                        Yes, Submit Batch
                    </button>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    function toggleRestockConfirmModal(show) {
        const modal = document.getElementById('restockConfirmModal');
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Debounced Live Search Script matching other modules
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('filterForm');
        let debounceTimer;

        if (searchInput) {
            const savedCursorPos = sessionStorage.getItem('restockSearchCursorPos');
            if (savedCursorPos !== null) {
                searchInput.focus();
                searchInput.setSelectionRange(parseInt(savedCursorPos), parseInt(savedCursorPos));
                sessionStorage.removeItem('restockSearchCursorPos');
            } else if (searchInput.value.length > 0) {
                searchInput.focus();
                searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                sessionStorage.setItem('restockSearchCursorPos', searchInput.selectionStart);

                debounceTimer = setTimeout(function () {
                    filterForm.submit();
                }, 200);
            });
        }

        // Restock Table Keyboard Navigation
        const inputs = Array.from(document.querySelectorAll('.restock-input'));
        const rows = Array.from(document.querySelectorAll('.restock-row'));

        function highlightRow(index) {
            rows.forEach(row => row.classList.remove('bg-amber-50', 'dark:bg-amber-950/30', 'ring-2', 'ring-inset', 'ring-amber-300', 'dark:ring-amber-700'));
            if (rows[index]) {
                rows[index].classList.add('bg-amber-50', 'dark:bg-amber-950/30', 'ring-2', 'ring-inset', 'ring-amber-300', 'dark:ring-amber-700');
                rows[index].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        }

        inputs.forEach((input, i) => {
            input.addEventListener('focus', function () {
                highlightRow(i);
            });

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    const next = inputs[i + 1];
                    if (next) {
                        next.focus();
                        next.select();
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prev = inputs[i - 1];
                    if (prev) {
                        prev.focus();
                        prev.select();
                    }
                }
            });
        });

        // Auto-focus first input if available and search is not actively focused
        if (inputs.length > 0 && document.activeElement !== searchInput) {
            inputs[0].focus();
        }
    });
</script>
@endpush