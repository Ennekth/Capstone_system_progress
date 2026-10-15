@extends('layouts.default')

@section('title', 'EOQ Calibration')

@section('content')

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">EOQ Calibration</h1>
            <p class="text-sm text-gray-500">Select products and apply ordering/holding cost overrides in batch.</p>
        </div>
        <a href="{{ route('eoq.settings') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            Global EOQ Settings →
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
            Search
        </button>
    </form>

    <form method="POST" action="{{ route('eoq.calibration.apply') }}" id="batchForm">
        @csrf

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

            <!-- Batch input bar -->
            <div class="flex flex-wrap items-end gap-3 px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Ordering Cost (₱)</label>
                    <input type="number" step="0.01" min="0" name="ordering_cost"
                           class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm"
                           placeholder="e.g. 150">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Holding Cost Override (₱/unit/year)</label>
                    <input type="number" step="0.01" min="0" name="holding_cost_override"
                           class="w-44 rounded-lg border border-gray-300 px-3 py-2 text-sm"
                           placeholder="optional">
                </div>
                <button type="submit"
                        onclick="return confirm('Apply this calibration to all selected products?');"
                        class="bg-gray-900 text-white text-sm font-medium px-5 py-2.5 rounded-lg hover:bg-gray-800">
                    Apply to Selected
                </button>
                <span id="selectedCount" class="text-sm text-gray-500 ml-auto">0 selected</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                            </th>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Annual Demand</th>
                            <th class="px-4 py-3">Ordering Cost</th>
                            <th class="px-4 py-3">Holding Cost</th>
                            <th class="px-4 py-3">EOQ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                           class="row-checkbox rounded border-gray-300">
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $product->product_name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $product->category->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ number_format($product->computed_annual_demand) }}</td>
                                <td class="px-4 py-3 text-gray-600">₱{{ number_format($product->computed_ordering_cost, 2) }}</td>
                                <td class="px-4 py-3 text-gray-600">₱{{ number_format($product->computed_holding_cost, 2) }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">
                                    {{ $product->computed_eoq > 0 ? $product->computed_eoq . ' units' : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">{{ $products->links() }}</div>
        </div>
    </form>

    <script>
        const selectAll = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const selectedCount = document.getElementById('selectedCount');

        function updateCount() {
            const checked = document.querySelectorAll('.row-checkbox:checked').length;
            selectedCount.textContent = checked + ' selected';
        }

        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });

        rowCheckboxes.forEach(cb => cb.addEventListener('change', updateCount));

        document.getElementById('batchForm').addEventListener('submit', function (e) {
            const checked = document.querySelectorAll('.row-checkbox:checked').length;
            if (checked === 0) {
                e.preventDefault();
                alert('Please select at least one product.');
            }
        });
    </script>

@endsection