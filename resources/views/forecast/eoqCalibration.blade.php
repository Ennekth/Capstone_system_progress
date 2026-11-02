@extends('layouts.default')

@section('title', 'EOQ Calibration')

@section('content')

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 dark:bg-emerald-950/85 text-green-800 dark:text-emerald-300 border border-green-200 dark:border-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 dark:bg-rose-950/85 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">EOQ Calibration</h1>
            <p class="text-sm text-gray-500 dark:text-slate-400">Select products and apply ordering/holding cost overrides in batch.</p>
        </div>
        <button type="button" onclick="openEoqSettingsModal()" class="text-sm font-medium text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors cursor-pointer">
            Global EOQ Settings →
        </button>
    </div>

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
        <!-- Search Button (Option B Style) -->
        <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-300 ease-in-out whitespace-nowrap shadow-xs cursor-pointer">
            Search
        </button>
    </form>

    <form method="POST" action="{{ route('eoq.calibration.apply') }}" id="batchForm">
        @csrf

        <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm">

            <!-- Batch input bar -->
            <div class="flex flex-wrap items-end gap-3 px-6 py-4 border-b border-gray-200 dark:border-[#1e2536] bg-gray-50 dark:bg-[#161B26]">
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">Ordering Cost (₱)</label>
                    <input type="number" step="0.01" min="0" name="ordering_cost"
                           class="w-32 rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-400"
                           placeholder="e.g. 150">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">Holding Cost Override (₱/unit/year)</label>
                    <input type="number" step="0.01" min="0" name="holding_cost_override"
                           class="w-44 rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-400"
                           placeholder="optional">
                </div>
                <!-- Apply to Selected Button (Option B Style) -->
                <button type="submit"
                        onclick="return confirm('Apply this calibration to all selected products?');"
                        class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    Apply to Selected
                </button>
                <span id="selectedCount" class="text-sm text-gray-500 dark:text-slate-400 ml-auto">0 selected</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-4 py-3">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 dark:border-[#1e2536] bg-white dark:bg-[#10141D] text-blue-600 focus:ring-slate-400">
                            </th>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Annual Demand</th>
                            <th class="px-4 py-3">Ordering Cost</th>
                            <th class="px-4 py-3">Holding Cost</th>
                            <th class="px-4 py-3">EOQ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                           class="row-checkbox rounded border-gray-300 dark:border-[#1e2536] bg-white dark:bg-[#10141D] text-blue-600 focus:ring-slate-400">
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-slate-400">{{ $product->category->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-slate-400">{{ number_format($product->computed_annual_demand) }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-slate-400">₱{{ number_format($product->computed_ordering_cost, 2) }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-slate-400">₱{{ number_format($product->computed_holding_cost, 2) }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-slate-100">
                                    {{ $product->computed_eoq > 0 ? $product->computed_eoq . ' units' : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-[#1e2536] text-gray-600 dark:text-slate-400">{{ $products->links() }}</div>
        </div>
    </form>

    <!-- Global EOQ Settings Modal -->
    <div id="eoqSettingsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-xl bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-2xl shadow-2xl p-6 sm:p-8 transform transition-all text-left">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-[#1e2536]">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">EOQ Settings</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Default values used when a product hasn't been individually calibrated.</p>
                </div>
                <button type="button" onclick="closeEoqSettingsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form method="POST" action="{{ route('eoq.settings.update') ?? '#' }}" class="space-y-5 text-sm">
                @csrf
                
                <!-- Ordering Cost Section -->
                <div class="space-y-3">
                    <h4 class="font-semibold text-gray-900 dark:text-white text-xs uppercase tracking-wider">Ordering Cost</h4>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">Flat Default (₱ per order)</label>
                        <input type="number" step="0.01" name="flat_default" placeholder="Leave blank to use percentage instead"
                               class="w-full rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-400">
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">If set, this is used for every uncalibrated product regardless of its price. Most businesses use this, since ordering cost (paperwork, delivery) usually doesn't scale with item price.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">Fallback: % of Purchase Cost</label>
                        <input type="number" step="0.01" name="fallback_percentage" value="10"
                               class="w-full rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Only used if the flat default above is left blank.</p>
                    </div>
                </div>

                <!-- Holding Cost Section -->
                <div class="space-y-3 pt-3 border-t border-gray-200 dark:border-[#1e2536]">
                    <h4 class="font-semibold text-gray-900 dark:text-white text-xs uppercase tracking-wider">Holding Cost</h4>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">% of Purchase Cost / Year</label>
                        <input type="number" step="0.01" name="holding_percentage" value="20"
                               class="w-full rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Used for products without a storage space (cbm) set. 20-30% is a common industry range.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">₱ per Cubic Meter / Year</label>
                        <input type="number" step="0.01" name="holding_cbm" value="0"
                               class="w-full rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                        <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">Takes priority over the percentage when a product has storage space set.</p>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-[#1e2536]">
                    <button type="button" onclick="closeEoqSettingsModal()" class="text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#161B26] px-4 py-2 rounded-lg transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <!-- Save Settings Button (Option B Style) -->
                    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

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

        function openEoqSettingsModal() {
            document.getElementById('eoqSettingsModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeEoqSettingsModal() {
            document.getElementById('eoqSettingsModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    </script>

@endsection