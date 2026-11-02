@extends('layouts.default')

@section('title', 'Product Management')

@section('content')
    <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200 dark:border-[#1e2536]">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Product Management</h1>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Manage products and services offered</p>
            </div>

            <div class="flex items-center gap-3">
                @include('partials.viewToggle')
                <!-- Add Product Button -->
                <button type="button" onclick="toggleAddProductModal(true)"
                   class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    + Add Product
                </button>
            </div>
        </div>

        <!-- Search / Filter / Sort Form -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-[#1e2536]">
            <form id="filterForm" method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-3">
                <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search products..." autocomplete="off"
                       class="flex-1 min-w-[180px] rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                              focus:outline-none focus:ring-1 focus:ring-slate-400">

                <select name="category_id" onchange="this.form.submit()" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <select name="sort" onchange="this.form.submit()" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="product_name" {{ request('sort', 'product_name') === 'product_name' ? 'selected' : '' }}>Sort: Name</option>
                    <option value="store_quantity" {{ request('sort') === 'store_quantity' ? 'selected' : '' }}>Sort: Store Qty</option>
                    <option value="warehouse_quantity" {{ request('sort') === 'warehouse_quantity' ? 'selected' : '' }}>Sort: Warehouse Qty</option>
                    <option value="selling_price" {{ request('sort') === 'selling_price' ? 'selected' : '' }}>Sort: Price</option>
                    <option value="purchase_cost" {{ request('sort') === 'purchase_cost' ? 'selected' : '' }}>Sort: Cost</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Sort: Newest</option>
                </select>

                <select name="direction" onchange="this.form.submit()" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>
            </form>
        </div>

        {{-- ================= LIST VIEW ================= --}}
        <div id="productResultsContainer">
            <div class="view-list overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Cost</th>
                            <th class="px-6 py-3">Price</th>
                            <th class="px-6 py-3">Store Qty</th>
                            <th class="px-6 py-3">Warehouse Qty</th>
                            <th class="px-6 py-3">Store Status</th>
                            <th class="px-6 py-3">Warehouse Status</th>
                            <th class="px-6 py-3">Total Status</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($products as $product)
                            @php
                                $storeQty = $product->store_quantity ?? 0;
                                $whQty = $product->warehouse_quantity ?? 0;
                                $totalQty = $storeQty + $whQty;
                                $reorder = $product->reorder_point ?? 10;

                                $storeStatus = $product->store_status ?? ($storeQty == 0 ? 'Out of Stock' : ($storeQty <= $reorder ? 'Low Stock' : 'In Stock'));
                                $whStatus = $product->warehouse_status ?? ($whQty == 0 ? 'Out of Stock' : ($whQty <= $reorder ? 'Low Stock' : 'In Stock'));
                                $totalStatus = $product->total_status ?? ($totalQty == 0 ? 'Out of Stock' : ($totalQty <= $reorder ? 'Low Stock' : 'In Stock'));
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 cursor-pointer transition-colors" onclick="openProductDetail({{ $product->id }})">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</div>
                                    @if ($product->approx_volume)
                                        <div class="text-xs text-gray-400">{{ $product->approx_volume }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-400">{{ $product->category->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-400">
                                    ₱{{ $product->purchase_cost !== null ? number_format($product->purchase_cost, 2) : '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-400">
                                    ₱{{ $product->selling_price !== null ? number_format($product->selling_price, 2) : '—' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-400">{{ $storeQty }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-slate-400">{{ $whQty }}</td>
                                
                                <td class="px-6 py-4">
                                    @if ($storeStatus === 'Out of Stock')
                                        <span class="inline-block bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Out of Stock</span>
                                    @elseif ($storeStatus === 'Low Stock')
                                        <span class="inline-block bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Low Stock</span>
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">In Stock</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if ($whStatus === 'Out of Stock')
                                        <span class="inline-block bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Out of Stock</span>
                                    @elseif ($whStatus === 'Low Stock')
                                        <span class="inline-block bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Low Stock</span>
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">In Stock</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if ($totalStatus === 'Out of Stock')
                                        <span class="inline-block bg-red-100 text-red-700 dark:bg-red-950 dark:text-rose-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Out of Stock</span>
                                    @elseif ($totalStatus === 'Low Stock')
                                        <span class="inline-block bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">Low Stock</span>
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider">In Stock</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                                    <div class="flex justify-end items-center gap-2">
                                        <button type="button" onclick="toggleEditProductModal({{ $product->id }}, true)"
                                           class="bg-gray-100 hover:bg-gray-200 dark:bg-[#161B26] dark:hover:bg-[#1d2636] border border-gray-300 dark:border-[#1e2536] text-gray-700 dark:text-slate-200 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors shadow-xs">
                                            Edit
                                        </button>
                                        <button type="button" onclick="toggleDeleteModal({{ $product->id }}, true)" 
                                                class="bg-red-50 hover:bg-red-100 dark:bg-rose-950/50 dark:hover:bg-rose-900/60 border border-red-200 dark:border-rose-900/60 text-red-600 dark:text-rose-300 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors shadow-xs">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================= GRID VIEW ================= --}}
            <div class="view-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 p-6 justify-items-center sm:justify-items-stretch">
                @forelse ($products as $product)
                    <div class="w-full max-w-[240px] sm:max-w-none border border-gray-200 dark:border-[#1e2536] rounded-xl overflow-hidden bg-white dark:bg-[#161B26] flex flex-col shadow-xs">

                        <div class="cursor-pointer" onclick="openProductDetail({{ $product->id }})">
                            @if (!empty($product->product_image))
                                <img src="{{ asset('storage/' . ltrim($product->product_image, '/')) }}" alt="{{ $product->product_name }}"
                                     class="w-full h-40 object-cover" loading="lazy">
                            @else
                                <div class="w-full h-40 bg-gray-100 dark:bg-[#10141D] flex items-center justify-center text-gray-300 dark:text-slate-600">
                                    <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            <div class="p-3.5">
                                <p class="font-medium text-gray-900 dark:text-white text-sm line-clamp-1">{{ $product->product_name }}</p>
                                <p class="text-xs text-gray-400 dark:text-slate-400 mt-0.5">{{ $product->category->name ?? '—' }}</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-slate-100 mt-2">
                                    ₱{{ $product->selling_price !== null ? number_format($product->selling_price, 2) : '—' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                    Store {{ $product->store_quantity ?? 0 }} · WH {{ $product->warehouse_quantity ?? 0 }}
                                </p>
                            </div>
                        </div>

                        <!-- Grid Card Action Buttons -->
                        <div class="mt-auto p-3 pt-0 flex items-center justify-end gap-2" onclick="event.stopPropagation()">
                            <button type="button" onclick="toggleEditProductModal({{ $product->id }}, true)"
                               class="bg-gray-100 hover:bg-gray-200 dark:bg-[#161B26] dark:hover:bg-[#1d2636] border border-gray-300 dark:border-[#1e2536] text-gray-700 dark:text-slate-200 text-xs font-semibold px-2.5 py-1 rounded-lg transition-colors shadow-xs">
                                Edit
                            </button>
                            <button type="button" onclick="toggleDeleteModal({{ $product->id }}, true)"
                               class="bg-red-50 hover:bg-red-100 dark:bg-rose-950/50 dark:hover:bg-rose-900/60 border border-red-200 dark:border-rose-900/60 text-red-600 dark:text-rose-300 text-xs font-semibold px-2.5 py-1 rounded-lg transition-colors shadow-xs">
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-400 dark:text-slate-500 py-10">No products found.</p>
                @endforelse
            </div>

            {{-- ================= RENDER EDIT MODALS FOR ALL PRODUCTS ================= --}}
            @foreach ($products as $product)
                <div id="editProductModal-{{ $product->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
                    <div class="relative w-full max-w-2xl bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-2xl shadow-2xl p-6 sm:p-8 transform transition-all text-left" onclick="event.stopPropagation()">
                        
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-200 dark:border-[#1e2536]">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Product: {{ $product->product_name }}</h3>
                            <button type="button" onclick="toggleEditProductModal({{ $product->id }}, false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-[#10141D] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Product Name</label>
                                <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}" required 
                                       class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Description</label>
                                <textarea name="product_description" rows="2" 
                                          class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">{{ old('product_description', $product->product_description) }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Category <span class="text-gray-400 dark:text-slate-500 font-normal">(optional)</span></label>
                                    <select name="category_id" class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                                        <option value="">Uncategorized (default)</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Approx. Volume <span class="text-gray-400 dark:text-slate-500 font-normal">(optional)</span></label>
                                    <input type="text" name="approx_volume" value="{{ old('approx_volume', $product->approx_volume) }}" placeholder="e.g. 20L, 5 gallons" 
                                           class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Volume (liters) <span class="text-gray-400 dark:text-slate-500 font-normal">(optional)</span></label>
                                    <input type="number" step="0.01" name="volume_liters" value="{{ old('volume_liters', $product->volume_liters) }}" 
                                           class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Purchase Cost</label>
                                    <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost', $product->purchase_cost) }}" required 
                                           class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Selling Price</label>
                                    <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required 
                                           class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Store Quantity</label>
                                    <input type="number" name="store_quantity" value="{{ old('store_quantity', $product->store_quantity) }}" required 
                                           class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Warehouse Quantity</label>
                                    <input type="number" name="warehouse_quantity" value="{{ old('warehouse_quantity', $product->warehouse_quantity) }}" required 
                                           class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Reorder Point <span class="text-gray-400 dark:text-slate-500 font-normal">(optional)</span></label>
                                    <input type="number" name="reorder_point" value="{{ old('reorder_point', $product->reorder_point) }}" placeholder="e.g. 10" 
                                           class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Product Image <span class="text-gray-400 dark:text-slate-500 font-normal">(optional)</span></label>
                                <input type="file" name="product_image" 
                                       class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 dark:file:bg-[#10141D] file:text-gray-700 dark:file:text-slate-200 hover:file:bg-gray-200 dark:hover:file:bg-[#1e2536]">
                            </div>

                            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-[#1e2536]">
                                <button type="button" onclick="toggleEditProductModal({{ $product->id }}, false)" class="text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#10141D] px-4 py-2 rounded-lg transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-[#10141D] hover:bg-[#1e2536] dark:bg-[#161B26] dark:hover:bg-[#1d2636] border border-slate-700/60 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors shadow-xs">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- ================= RENDER CUSTOM DELETE CONFIRMATION MODALS FOR ALL PRODUCTS ================= --}}
            @foreach ($products as $product)
                <div id="deleteModal-{{ $product->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
                    <div class="relative w-full max-w-md bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-2xl shadow-2xl p-6 transform transition-all text-left" onclick="event.stopPropagation()">
                        
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200 dark:border-[#1e2536]">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Delete Product</h3>
                            <button type="button" onclick="toggleDeleteModal({{ $product->id }}, false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-[#10141D] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <p class="text-sm text-gray-600 dark:text-slate-300 mb-6">
                            Are you sure you want to delete <strong class="text-gray-900 dark:text-white">{{ $product->product_name }}</strong>? This cannot be undone.
                        </p>

                        <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-200 dark:border-[#1e2536]">
                            <button type="button" onclick="toggleDeleteModal({{ $product->id }}, false)" class="text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#10141D] px-4 py-2 rounded-lg transition-colors">
                                Cancel
                            </button>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors shadow-xs">
                                    Delete Product
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="px-6 py-4 border-t border-gray-200 dark:border-[#1e2536] text-gray-600 dark:text-slate-400">{{ $products->links() }}</div>
        </div>

    </div>

    <!-- Add Product Modal Popup -->
    <div id="addProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-2xl shadow-2xl p-6 sm:p-8 transform transition-all">
            
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-200 dark:border-[#1e2536]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Add New Product</h3>
                <button type="button" onclick="toggleAddProductModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-[#10141D] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                @if ($errors->any())
                    <div class="rounded-lg bg-red-100 dark:bg-rose-950/80 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-800 px-4 py-3 text-sm">
                        Please check the fields below and try again.
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Product Name</label>
                    <input type="text" name="product_name" value="{{ old('product_name') }}" required 
                           class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Description</label>
                    <textarea name="product_description" rows="2" 
                              class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">{{ old('product_description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Category <span class="text-gray-400 dark:text-slate-500 font-normal">(optional)</span></label>
                        <select name="category_id" class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                            <option value="">Uncategorized (default)</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Approx. Volume <span class="text-gray-400 dark:text-slate-500 font-normal">(optional)</span></label>
                        <input type="text" name="approx_volume" value="{{ old('approx_volume') }}" placeholder="e.g. 20L, 5 gallons" 
                               class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Volume (liters) <span class="text-gray-400 dark:text-slate-500 font-normal">(optional)</span></label>
                        <input type="number" step="0.01" name="volume_liters" value="{{ old('volume_liters') }}" 
                               class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Purchase Cost</label>
                        <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost') }}" required 
                               class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price') }}" required 
                               class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Store Quantity</label>
                        <input type="number" name="store_quantity" value="{{ old('store_quantity', 0) }}" required 
                               class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Warehouse Quantity</label>
                        <input type="number" name="warehouse_quantity" value="{{ old('warehouse_quantity', 0) }}" required 
                               class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Reorder Point <span class="text-gray-400 dark:text-slate-500 font-normal">(optional)</span></label>
                        <input type="number" name="reorder_point" value="{{ old('reorder_point') }}" placeholder="e.g. 10" 
                               class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3.5 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300 mb-1">Product Image <span class="text-gray-400 dark:text-slate-500 font-normal">(optional)</span></label>
                    <input type="file" name="product_image" 
                           class="w-full text-xs text-gray-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 dark:file:bg-[#10141D] file:text-gray-700 dark:file:text-slate-200 hover:file:bg-gray-200 dark:hover:file:bg-[#1e2536]">
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-[#1e2536]">
                    <button type="button" onclick="toggleAddProductModal(false)" class="text-sm font-medium text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#10141D] px-4 py-2 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                        Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('partials.productDetailModal')

@endsection

@push('scripts')
<script>
    function toggleAddProductModal(show) {
        const modal = document.getElementById('addProductModal');
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function toggleEditProductModal(productId, show) {
        const modal = document.getElementById(`editProductModal-${productId}`);
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function toggleDeleteModal(productId, show) {
        const modal = document.getElementById(`deleteModal-${productId}`);
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('filterForm');
        let debounceTimer;

        if (searchInput) {
            if (searchInput.value.length > 0) {
                searchInput.focus();
                searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    filterForm.submit();
                }, 150);
            });
        }
    });

    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function () {
            toggleAddProductModal(true);
        });
    @endif
</script>
@endpush