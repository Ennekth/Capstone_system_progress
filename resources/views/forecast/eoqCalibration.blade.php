@extends('layouts.default')

@section('title', 'EOQ Calibration')

@section('content')

    <h1 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">EOQ Calibration</h1>
    <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">Select products and apply ordering/holding cost overrides in batch.</p>

    @if (session('success'))
        <div class="mb-6 rounded-2xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-2 border-emerald-200 dark:border-emerald-900 px-6 py-4 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <form id="filterForm" method="GET" class="flex flex-col sm:flex-row gap-3 w-full">
            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search products..."
                   class="flex-1 rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-slate-400">
            <select name="category_id" class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out whitespace-nowrap shadow-xs cursor-pointer">
                Search
            </button>
        </form>

        <button type="button" onclick="openEoqSettingsModal()" class="ml-4 whitespace-nowrap bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
            Global EOQ Settings →
        </button>
    </div>

    <form method="POST" action="{{ route('eoq.calibration.apply') }}" id="batchForm">
        @csrf

        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden mb-8">

            <!-- Batch input bar -->
            <div class="flex flex-wrap items-end gap-4 px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536] bg-gray-50 dark:bg-[#161B26]/50">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Ordering Cost (₱)</label>
                    <input type="number" step="0.01" min="0" name="ordering_cost"
                           class="w-36 rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-slate-400"
                           placeholder="e.g. 150">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Holding Cost Override (₱/unit/yr)</label>
                    <input type="number" step="0.01" min="0" name="holding_cost_override"
                           class="w-48 rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-slate-400"
                           placeholder="optional">
                </div>
                <button type="submit"
                        onclick="return confirm('Apply this calibration to all selected products?');"
                        class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    Apply to Selected
                </button>
                <span id="selectedCount" class="text-sm font-semibold text-gray-500 dark:text-slate-400 ml-auto">0 selected</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-8 py-4 w-12">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 dark:border-[#1e2536] bg-white dark:bg-[#10141D] text-blue-600 focus:ring-slate-400 w-4 h-4 cursor-pointer">
                            </th>
                            <th class="px-8 py-4">Product</th>
                            <th class="px-8 py-4">Category</th>
                            <th class="px-8 py-4">Annual Demand</th>
                            <th class="px-8 py-4">Ordering Cost</th>
                            <th class="px-8 py-4">Holding Cost</th>
                            <th class="px-8 py-4">EOQ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                                <td class="px-8 py-4">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                           class="row-checkbox rounded border-gray-300 dark:border-[#1e2536] bg-white dark:bg-[#10141D] text-blue-600 focus:ring-slate-400 w-4 h-4 cursor-pointer">
                                </td>
                                <td class="px-8 py-4 font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400">{{ $product->category->name ?? '—' }}</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-semibold">{{ number_format($product->computed_annual_demand) }}</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400">₱{{ number_format($product->computed_ordering_cost, 2) }}</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400">₱{{ number_format($product->computed_holding_cost, 2) }}</td>
                                <td class="px-8 py-4 font-bold text-gray-900 dark:text-slate-100">
                                    {{ $product->computed_eoq > 0 ? $product->computed_eoq . ' units' : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pill Pagination Footer (Auto-hides if items <= 16) -->
            @if ($products->hasPages())
                <div class="px-8 py-5 border-t-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-slate-400">
                    <div>
                        Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $products->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900 dark:text-white">{{ $products->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $products->total() }}</span> results
                    </div>

                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
                        @if ($products->onFirstPage())
                            <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                                Previous
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                Previous
                            </a>
                        @endif

                        @foreach ($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                            @if ($page == $products->currentPage())
                                <span class="px-4 py-2 text-xs font-bold text-white bg-slate-900 dark:bg-[#1E293B] rounded-full shadow-xs border border-transparent">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
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
    </form>

    <!-- Global EOQ Settings Modal -->
    <div id="eoqSettingsModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left">
            
            <div class="flex items-center justify-between pb-6 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">EOQ Settings</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Default values used when a product hasn't been individually calibrated.</p>
                </div>
                <button type="button" onclick="closeEoqSettingsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer text-lg font-bold">
                    &times;
                </button>
            </div>

            <form method="POST" action="{{ route('eoq.settings.update') ?? '#' }}" class="space-y-6 text-sm">
                @csrf
                
                <div class="space-y-4">
                    <h4 class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wider">Ordering Cost</h4>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Flat Default (₱ per order)</label>
                        <input type="number" step="0.01" name="flat_default" placeholder="Leave blank to use percentage instead"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Fallback: % of Purchase Cost</label>
                        <input type="number" step="0.01" name="fallback_percentage" value="10"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div class="space-y-4 pt-4 border-t-2 border-gray-200 dark:border-[#1e2536]">
                    <h4 class="font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wider">Holding Cost</h4>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">% of Purchase Cost / Year</label>
                        <input type="number" step="0.01" name="holding_percentage" value="20"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">₱ per Cubic Meter / Year</label>
                        <input type="number" step="0.01" name="holding_cbm" value="0"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                    <button type="button" onclick="closeEoqSettingsModal()" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
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

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                updateCount();
            });
        }

        rowCheckboxes.forEach(cb => cb.addEventListener('change', updateCount));

        const batchForm = document.getElementById('batchForm');
        if (batchForm) {
            batchForm.addEventListener('submit', function (e) {
                const checked = document.querySelectorAll('.row-checkbox:checked').length;
                if (checked === 0) {
                    e.preventDefault();
                    alert('Please select at least one product.');
                }
            });
        }

        function openEoqSettingsModal() {
            document.getElementById('eoqSettingsModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeEoqSettingsModal() {
            document.getElementById('eoqSettingsModal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const filterForm = document.getElementById('filterForm');
            let debounceTimer;

            if (searchInput) {
                const savedCursorPos = sessionStorage.getItem('eoqSearchCursorPos');
                if (savedCursorPos !== null) {
                    searchInput.focus();
                    searchInput.setSelectionRange(parseInt(savedCursorPos), parseInt(savedCursorPos));
                    sessionStorage.removeItem('eoqSearchCursorPos');
                } else if (searchInput.value.length > 0) {
                    searchInput.focus();
                    searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
                }

                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    sessionStorage.setItem('eoqSearchCursorPos', searchInput.selectionStart);

                    debounceTimer = setTimeout(function () {
                        filterForm.submit();
                    }, 300);
                });
            }
        });
    </script>

@endsection