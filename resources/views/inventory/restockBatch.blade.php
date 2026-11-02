@extends('layouts.default')

@section('title', 'Restock Mode')

@section('content')

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 dark:bg-rose-950/80 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-800 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Restock Mode</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400">
                Type a quantity, press <kbd class="px-1.5 py-0.5 bg-gray-100 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] text-gray-800 dark:text-slate-200 rounded text-xs">Enter</kbd> or
                <kbd class="px-1.5 py-0.5 bg-gray-100 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] text-gray-800 dark:text-slate-200 rounded text-xs">↓</kbd> to move to the next item.
            </p>
        </div>
        <a href="{{ route('inventory.index') }}" class="text-sm font-medium text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors cursor-pointer">
            Exit Restock Mode
        </a>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
               class="flex-1 rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-400">
        
        <select name="category_id" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
            <option value="">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-300 ease-in-out whitespace-nowrap shadow-xs cursor-pointer">
            Filter List
        </button>
    </form>

    <form method="POST" action="{{ route('inventory.restockBatch.store') }}" id="restockForm">
        @csrf

        <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm overflow-hidden">

            <!-- Batch Configuration Header -->
            <div class="flex flex-wrap items-end gap-3 px-6 py-4 border-b border-gray-200 dark:border-[#1e2536] bg-gray-50/50 dark:bg-[#161B26]/50">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Restocking Location</label>
                    @if (in_array(auth()->user()->role, ['admin', 'manager']))
                        <select name="location" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                            <option value="store" {{ $location === 'store' ? 'selected' : '' }}>Store</option>
                            <option value="warehouse" {{ $location === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                        </select>
                    @else
                        <input type="hidden" name="location" value="{{ $location }}">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($location) }}</p>
                    @endif
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Batch Note (optional)</label>
                    <input type="text" name="note" placeholder="e.g. Weekly delivery from supplier"
                           class="w-full rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-400">
                </div>

                <!-- Done — Submit Batch Button (Success Emerald Style) -->
                <button type="submit"
                        onclick="return confirm('Submit this restocking batch?');"
                        class="bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    Done — Submit Batch
                </button>
            </div>

            <!-- Products Table -->
            <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536] sticky top-0">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3 w-40">Add Quantity</th>
                            <th class="px-6 py-3">Current Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($products as $index => $product)
                            <tr id="row-{{ $index }}" class="restock-row transition-colors">
                                <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</td>
                                <td class="px-6 py-3">
                                    <input type="number" min="0"
                                        name="quantities[{{ $product->id }}]"
                                        data-index="{{ $index }}"
                                        class="restock-input w-28 rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-1.5 text-sm text-gray-900 dark:text-slate-100
                                                focus:outline-none focus:ring-1 focus:ring-slate-400">
                                </td>
                                <td class="px-6 py-3 text-gray-500 dark:text-slate-400">
                                    {{ $location === 'store' ? $product->store_quantity : $product->warehouse_quantity }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">
                                    No products found for restock.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <script>
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

        if (inputs.length > 0) {
            inputs[0].focus();
        }
    </script>

@endsection