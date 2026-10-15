@extends('layouts.default')

@section('title', 'Restock Mode')

@section('content')

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">Restock Mode</h1>
            <p class="text-sm text-gray-500">
                Type a quantity, press <kbd class="px-1.5 py-0.5 bg-gray-200 rounded text-xs">Enter</kbd> or
                <kbd class="px-1.5 py-0.5 bg-gray-200 rounded text-xs">↓</kbd> to move to the next item.
            </p>
        </div>
        <a href="{{ route('inventory.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            Exit Restock Mode
        </a>
    </div>

    <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <select name="category_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">All Categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
            Filter List
        </button>
    </form>

    <form method="POST" action="{{ route('inventory.restockBatch.store') }}" id="restockForm">
        @csrf

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

            <div class="flex flex-wrap items-end gap-3 px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Restocking Location</label>
                    @if (in_array(auth()->user()->role, ['admin', 'manager']))
                        <select name="location" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="store" {{ $location === 'store' ? 'selected' : '' }}>Store</option>
                            <option value="warehouse" {{ $location === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                        </select>
                    @else
                        <input type="hidden" name="location" value="{{ $location }}">
                        <p class="text-sm font-medium text-gray-900">{{ ucfirst($location) }}</p>
                    @endif
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Batch Note (optional)</label>
                    <input type="text" name="note" placeholder="e.g. Weekly delivery from supplier"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>

                <button type="submit"
                        onclick="return confirm('Submit this restocking batch?');"
                        class="bg-green-600 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-green-700">
                    Done — Submit Batch
                </button>
            </div>

            <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs sticky top-0">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3 w-40">Add Quantity</th>
                            <th class="px-6 py-3">Current Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($products as $index => $product)
                            <tr id="row-{{ $index }}" class="restock-row transition-colors">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $product->product_name }}</td>
                                <td class="px-6 py-3">
                                    <input type="number" min="0"
                                        name="quantities[{{ $product->id }}]"
                                        data-index="{{ $index }}"
                                        class="restock-input w-28 rounded-lg border border-gray-300 px-3 py-1.5 text-sm
                                                focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                </td>
                                <td class="px-6 py-3 text-gray-500">
                                    {{ $location === 'store' ? $product->store_quantity : $product->warehouse_quantity }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <script>
        const inputs = Array.from(document.querySelectorAll('.restock-input'));
        const rows = Array.from(document.querySelectorAll('.restock-row'));

        function highlightRow(index) {
            rows.forEach(row => row.classList.remove('bg-amber-50', 'ring-2', 'ring-inset', 'ring-amber-300'));
            if (rows[index]) {
                rows[index].classList.add('bg-amber-50', 'ring-2', 'ring-inset', 'ring-amber-300');
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