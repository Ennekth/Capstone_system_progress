@extends('layouts.default')

@section('title', $subcategory->name . ' - Products')

@section('content')

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white uppercase">{{ $subcategory->name }}</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900">BRAND SUBCATEGORY</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">
                Parent Group: <strong class="text-gray-700 dark:text-slate-300">{{ $parentCategory->name ?? 'None' }}</strong> | Code: <span class="font-mono">{{ $subcategory->code ?? '—' }}</span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            @include('partials.viewToggle')
            <button type="button" onclick="toggleAddProductModal(true)"
               class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer whitespace-nowrap">
                + Add Product
            </button>
            <a href="{{ route('admin.categories.show', $parentCategory) }}"
               class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer whitespace-nowrap">
                &larr; Back to Main Category
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden mb-8">

        <!-- Search Header -->
        <div class="px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536] bg-gray-50/50 dark:bg-[#161B26]/50">
            <form id="filterForm" method="GET" action="{{ route('admin.categories.subcategories.show', $subcategory) }}" class="flex flex-col sm:flex-row flex-wrap gap-4">
                <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search products by name or SKU..." autocomplete="off"
                       class="flex-1 min-w-[200px] rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-slate-400">
            </form>
        </div>

        {{-- ================= LIST VIEW ================= --}}
        <div class="view-list overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-8 py-4">Product Name</th>
                        <th class="px-8 py-4">SKU / Code</th>
                        <th class="px-8 py-4">Volume</th>
                        <th class="px-8 py-4">Price</th>
                        <th class="px-8 py-4">Store Qty</th>
                        <th class="px-8 py-4">Warehouse Qty</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                            <td class="px-8 py-4 font-bold text-gray-900 dark:text-white">
                                {{ $product->product_name }}
                            </td>
                            <td class="px-8 py-4 font-mono text-xs text-gray-600 dark:text-slate-400">
                                {{ $product->sku ?? ($product->code ?? '—') }}
                            </td>
                            <td class="px-8 py-4 text-gray-600 dark:text-slate-400">
                                {{ $product->approx_volume ?? ($product->volume_liters ? $product->volume_liters . 'L' : '—') }}
                            </td>
                            <td class="px-8 py-4 font-semibold text-gray-900 dark:text-slate-100">
                                ₱{{ number_format($product->selling_price, 2) }}
                            </td>
                            <td class="px-8 py-4 font-medium text-gray-700 dark:text-slate-300">
                                {{ $product->store_quantity ?? 0 }}
                            </td>
                            <td class="px-8 py-4 font-medium text-gray-700 dark:text-slate-300">
                                {{ $product->warehouse_quantity ?? 0 }}
                            </td>
                            <td class="px-8 py-4 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <!-- View Button -->
                                    <button type="button" onclick="openViewProductModal({{ json_encode($product) }})" 
                                       class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-3.5 py-1.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                        View
                                    </button>
                                    <!-- Edit Button -->
                                    <button type="button" onclick="openEditProductModal({{ json_encode($product) }})"
                                       class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-3.5 py-1.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                        Edit
                                    </button>
                                    <!-- Delete Button -->
                                    <button type="button" onclick="openDeleteProductModal('{{ route('admin.products.destroy', $product) }}')"
                                       class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-3.5 py-1.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">
                                No products found under this brand subcategory.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= GRID VIEW ================= --}}
        <div class="view-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-6 justify-items-center sm:justify-items-stretch">
            @forelse ($products as $product)
                <div class="w-full border-2 border-gray-200 dark:border-[#1e2536] rounded-2xl overflow-hidden bg-white dark:bg-[#161B26] flex flex-col shadow-xs p-5 justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider">
                                {{ $product->product_name }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900">PRODUCT</span>
                        </div>

                        <p class="text-xs text-gray-400 dark:text-slate-400 font-mono mb-2">SKU: {{ $product->sku ?? ($product->code ?? '—') }}</p>
                        
                        <div class="mb-4 flex items-center justify-between text-xs text-gray-600 dark:text-slate-300">
                            <span>Price: <strong class="text-gray-900 dark:text-white">₱{{ number_format($product->selling_price, 2) }}</strong></span>
                            <span>Vol: {{ $product->approx_volume ?? '—' }}</span>
                        </div>

                        <div class="mb-4 grid grid-cols-2 gap-2 text-xs bg-gray-50 dark:bg-[#10141D] p-2.5 rounded-xl border border-gray-100 dark:border-[#1e2536]">
                            <div>Store Qty: <span class="font-bold text-gray-900 dark:text-white">{{ $product->store_quantity ?? 0 }}</span></div>
                            <div>WH Qty: <span class="font-bold text-gray-900 dark:text-white">{{ $product->warehouse_quantity ?? 0 }}</span></div>
                        </div>
                    </div>

                    <div class="pt-3 border-t-2 border-gray-100 dark:border-[#1e2536] flex items-center gap-1.5">
                        <button type="button" onclick="openViewProductModal({{ json_encode($product) }})" 
                           class="flex-1 bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-[10px] font-semibold px-2 py-1.5 rounded-xl transition-all shadow-xs cursor-pointer text-center">
                            View
                        </button>
                        <button type="button" onclick="openEditProductModal({{ json_encode($product) }})"
                           class="flex-1 bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-[10px] font-semibold px-2 py-1.5 rounded-xl transition-all shadow-xs cursor-pointer text-center">
                            Edit
                        </button>
                        <button type="button" onclick="openDeleteProductModal('{{ route('admin.products.destroy', $product) }}')"
                           class="flex-1 bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-[10px] font-semibold px-2 py-1.5 rounded-xl transition-all shadow-xs cursor-pointer text-center">
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-400 dark:text-slate-500 py-12 font-medium">No products found under this brand subcategory.</p>
            @endforelse
        </div>

        <!-- Pagination Footer -->
        @if ($products->hasPages())
            <div class="px-8 py-5 border-t-2 border-gray-200 dark:border-[#1e2536]">
                {{ $products->links() }}
            </div>
        @endif

    </div>

    <!-- VIEW PRODUCT POPUP MODAL -->
    <div id="viewProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left" onclick="event.stopPropagation()">
            
            <div class="flex items-center justify-between pb-6 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Product Details</h3>
                <button type="button" onclick="toggleViewProductModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors text-xl font-bold">
                    &times;
                </button>
            </div>

            <div class="space-y-4">
                <div class="flex justify-center mb-4">
                    <img id="modal_view_image" src="" alt="Product Image" class="w-32 h-32 object-cover rounded-2xl border-2 border-gray-200 dark:border-[#1e2536] hidden">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Product Name</span>
                        <p id="modal_product_name" class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">—</p>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">SKU / Code</span>
                        <p id="modal_sku" class="text-sm font-mono text-gray-900 dark:text-white mt-0.5">—</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Approx. Volume</span>
                        <p id="modal_approx_volume" class="text-sm text-gray-900 dark:text-white mt-0.5">—</p>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Volume (Liters)</span>
                        <p id="modal_volume_liters" class="text-sm text-gray-900 dark:text-white mt-0.5">—</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Purchase Cost</span>
                        <p id="modal_purchase_cost" class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">—</p>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Selling Price</span>
                        <p id="modal_selling_price" class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">—</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Store Quantity</span>
                        <p id="modal_store_quantity" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">0</p>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Warehouse Quantity</span>
                        <p id="modal_warehouse_quantity" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">0</p>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Reorder Point</span>
                        <p id="modal_reorder_point" class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">0</p>
                    </div>
                </div>

                <div>
                    <span class="block text-xs font-bold text-gray-400 dark:text-slate-500 uppercase">Description</span>
                    <p id="modal_product_description" class="text-sm text-gray-700 dark:text-slate-300 mt-0.5">—</p>
                </div>
            </div>

            <div class="flex items-center justify-end pt-6 mt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                <button type="button" onclick="toggleViewProductModal(false)" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- EDIT PRODUCT POPUP MODAL -->
    <div id="editProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-3xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left" onclick="event.stopPropagation()">
            
            <div class="flex items-center justify-between pb-6 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Product</h3>
                <button type="button" onclick="toggleEditProductModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors text-xl font-bold">
                    &times;
                </button>
            </div>

            <form id="editProductForm" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Product Name</label>
                        <input type="text" id="edit_product_name" name="product_name" required
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Category</label>
                        <select id="edit_category_id" name="category_id" class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                            <option value="{{ $subcategory->id }}">{{ $subcategory->name }} (Current Brand)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Approx. Volume</label>
                        <input type="text" id="edit_approx_volume" name="approx_volume"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Volume (Liters)</label>
                        <input type="number" step="0.01" id="edit_volume_liters" name="volume_liters"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Purchase Cost</label>
                        <input type="number" step="0.01" id="edit_purchase_cost" name="purchase_cost" required
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Selling Price</label>
                        <input type="number" step="0.01" id="edit_selling_price" name="selling_price" required
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Store Quantity</label>
                        <input type="number" id="edit_store_quantity" name="store_quantity" required
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Warehouse Quantity</label>
                        <input type="number" id="edit_warehouse_quantity" name="warehouse_quantity" required
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Reorder Point</label>
                        <input type="number" id="edit_reorder_point" name="reorder_point"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Description <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea id="edit_product_description" name="product_description" rows="3"
                              class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Replace Image <span class="text-gray-400 font-normal">(optional)</span></label>
                    <div class="flex items-center gap-4">
                        <label class="bg-gray-100 hover:bg-gray-200 dark:bg-[#161B26] dark:hover:bg-[#1e2536] text-gray-700 dark:text-slate-300 text-xs font-semibold px-4 py-2.5 rounded-xl cursor-pointer border-2 border-gray-200 dark:border-[#1e2536] transition-colors">
                            Choose File
                            <input type="file" name="product_image" accept="image/*" class="hidden" onchange="document.getElementById('editFileNameLabel').textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
                        </label>
                        <span id="editFileNameLabel" class="text-sm text-gray-500 dark:text-slate-400">No file chosen</span>
                        <img id="edit_image_preview" src="" alt="" class="w-12 h-12 object-cover rounded-xl border-2 border-gray-200 dark:border-[#1e2536] hidden ml-auto">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                    <button type="button" onclick="toggleEditProductModal(false)" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE CONFIRMATION POPUP MODAL -->
    <div id="deleteProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-4 mb-4 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Deletion</h3>
                <button type="button" onclick="toggleDeleteProductModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors text-xl font-bold">&times;</button>
            </div>
            <p class="text-sm text-gray-600 dark:text-slate-300 mb-6">Are you sure you want to delete this product? This action cannot be undone.</p>
            <form id="deleteProductForm" method="POST" class="flex items-center justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="toggleDeleteProductModal(false)" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer">
                    Cancel
                </button>
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all shadow-xs cursor-pointer">
                    Delete Product
                </button>
            </form>
        </div>
    </div>

    <!-- ADD PRODUCT MODAL -->
    <div id="addProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-3xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left" onclick="event.stopPropagation()">
            
            <div class="flex items-center justify-between pb-6 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add New Product to {{ $subcategory->name }}</h3>
                <button type="button" onclick="toggleAddProductModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors text-xl font-bold">
                    &times;
                </button>
            </div>

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <input type="hidden" name="category_id" value="{{ $subcategory->id }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Product Name</label>
                        <input type="text" name="product_name" required placeholder="e.g. Purified Drinking Water"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Assigned Brand Subcategory</label>
                        <input type="text" readonly value="{{ $subcategory->name }} ({{ $parentCategory->name ?? '' }})"
                               class="w-full rounded-xl bg-gray-100 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-500 dark:text-slate-400 cursor-not-allowed focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Approx. Volume <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="text" name="approx_volume" placeholder="e.g. 20L, 5 gallons"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Volume (Liters) <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="number" step="0.01" name="volume_liters" placeholder="e.g. 20"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Purchase Cost</label>
                        <input type="number" step="0.01" name="purchase_cost" required placeholder="0.00"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" required placeholder="0.00"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Store Quantity</label>
                        <input type="number" name="store_quantity" value="0" required
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Warehouse Quantity</label>
                        <input type="number" name="warehouse_quantity" value="0" required
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Product Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <textarea name="product_description" rows="3" placeholder="Enter product details or description..."
                              class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Product Image <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <div class="flex items-center gap-4">
                        <label class="bg-gray-100 hover:bg-gray-200 dark:bg-[#161B26] dark:hover:bg-[#1e2536] text-gray-700 dark:text-slate-300 text-xs font-semibold px-4 py-2.5 rounded-xl cursor-pointer border-2 border-gray-200 dark:border-[#1e2536] transition-colors">
                            Choose File
                            <input type="file" name="product_image" accept="image/*" class="hidden" onchange="document.getElementById('addFileNameLabel').textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
                        </label>
                        <span id="addFileNameLabel" class="text-sm text-gray-500 dark:text-slate-400">No file chosen</span>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                    <button type="button" onclick="toggleAddProductModal(false)" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>

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

    function toggleViewProductModal(show) {
        const modal = document.getElementById('viewProductModal');
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function toggleEditProductModal(show) {
        const modal = document.getElementById('editProductModal');
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function toggleDeleteProductModal(show) {
        const modal = document.getElementById('deleteProductModal');
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openDeleteProductModal(actionUrl) {
        document.getElementById('deleteProductForm').action = actionUrl;
        toggleDeleteProductModal(true);
    }

    function openViewProductModal(product) {
        document.getElementById('modal_product_name').textContent = product.product_name || '—';
        document.getElementById('modal_sku').textContent = product.sku || product.code || '—';
        document.getElementById('modal_approx_volume').textContent = product.approx_volume || '—';
        document.getElementById('modal_volume_liters').textContent = product.volume_liters ? product.volume_liters + ' L' : '—';
        document.getElementById('modal_purchase_cost').textContent = '₱' + Number(product.purchase_cost || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('modal_selling_price').textContent = '₱' + Number(product.selling_price || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('modal_store_quantity').textContent = product.store_quantity ?? 0;
        document.getElementById('modal_warehouse_quantity').textContent = product.warehouse_quantity ?? 0;
        document.getElementById('modal_reorder_point').textContent = product.reorder_point ?? 0;
        document.getElementById('modal_product_description').textContent = product.product_description || product.description || 'No description provided.';
        
        const imgEl = document.getElementById('modal_view_image');
        if (product.product_image || product.image_path) {
            imgEl.src = '/storage/' + (product.product_image || product.image_path);
            imgEl.classList.remove('hidden');
        } else {
            imgEl.classList.add('hidden');
        }
        
        toggleViewProductModal(true);
    }

    function openEditProductModal(product) {
        const form = document.getElementById('editProductForm');
        form.action = `/admin/products/${product.id}`;

        document.getElementById('edit_product_name').value = product.product_name || '';
        document.getElementById('edit_category_id').value = product.category_id || '';
        document.getElementById('edit_product_description').value = product.product_description || product.description || '';
        document.getElementById('edit_approx_volume').value = product.approx_volume || '';
        document.getElementById('edit_volume_liters').value = product.volume_liters || '';
        document.getElementById('edit_purchase_cost').value = product.purchase_cost || '';
        document.getElementById('edit_selling_price').value = product.selling_price || '';
        document.getElementById('edit_store_quantity').value = product.store_quantity ?? 0;
        document.getElementById('edit_warehouse_quantity').value = product.warehouse_quantity ?? 0;
        document.getElementById('edit_reorder_point').value = product.reorder_point || '';

        const previewImg = document.getElementById('edit_image_preview');
        if (product.product_image || product.image_path) {
            previewImg.src = '/storage/' + (product.product_image || product.image_path);
            previewImg.classList.remove('hidden');
        } else {
            previewImg.classList.add('hidden');
        }

        toggleEditProductModal(true);
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
</script>
@endpush