@extends('layouts.default')

@section('title', 'Inventory')

@section('content')

    @php
        $canTransfer = in_array(auth()->user()->role, ['admin', 'manager']);
    @endphp

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 dark:bg-emerald-950/85 text-green-800 dark:text-emerald-300 border border-green-200 dark:border-emerald-900 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 dark:bg-rose-950/85 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm">

        <div class="px-6 py-5 border-b border-gray-200 dark:border-[#1e2536]">
            <div class="flex items-center justify-between pb-5 border-b border-gray-200 dark:border-[#1e2536]">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Inventory</h1>
                <div class="flex items-center gap-3">
                    @include('partials.viewToggle')
                    <!-- Restock Mode Button -->
                    <a href="{{ route('inventory.restockBatch') }}"
                       class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer inline-flex items-center">
                        + Restock Mode
                    </a>
                </div>
            </div>

            <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 pt-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                       class="flex-1 min-w-[180px] rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                              focus:outline-none focus:ring-1 focus:ring-slate-400">

                <select name="category_id" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="">All Status</option>
                    <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>

                <select name="sort" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="product_name" {{ request('sort', 'product_name') === 'product_name' ? 'selected' : '' }}>Sort: Name</option>
                    <option value="store_quantity" {{ request('sort') === 'store_quantity' ? 'selected' : '' }}>Sort: Store Qty</option>
                    <option value="warehouse_quantity" {{ request('sort') === 'warehouse_quantity' ? 'selected' : '' }}>Sort: Warehouse Qty</option>
                    <option value="selling_price" {{ request('sort') === 'selling_price' ? 'selected' : '' }}>Sort: Price</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Sort: Newest</option>
                </select>

                <select name="direction" class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <!-- Filter Apply Button -->
                <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    Apply
                </button>

                @if (request()->anyFilled(['search', 'category_id', 'status']) || request('sort', 'product_name') !== 'product_name')
                    <a href="{{ request()->url() }}" class="text-sm font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] px-4 py-2 rounded-lg transition-colors">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- ================= LIST VIEW ================= --}}
        <div class="view-list overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536]">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Store Qty</th>
                        <th class="px-6 py-3">Warehouse Qty</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 cursor-pointer transition-colors" onclick="openProductDetail({{ $product->id }})">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">{{ $product->category->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">{{ $product->store_quantity ?? 0 }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-slate-400">{{ $product->warehouse_quantity ?? 0 }}</td>
                            <td class="px-6 py-4">@include('partials.stockBadge', ['product' => $product])</td>
                            <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                                <!-- Restock Action Button -->
                                <button type="button" onclick="document.getElementById('restock-{{ $product->id }}').classList.toggle('hidden')"
                                        class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                    Restock
                                </button>
                                @if ($canTransfer)
                                    <!-- Transfer Action Button -->
                                    <button type="button" onclick="document.getElementById('transfer-{{ $product->id }}').classList.toggle('hidden')"
                                            class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer ml-2">
                                        Transfer
                                    </button>
                                @endif
                            </td>
                        </tr>

                        <tr id="restock-{{ $product->id }}" class="hidden bg-gray-50 dark:bg-[#161B26]/80">
                            <td colspan="6" class="px-6 py-4 border-t border-gray-200 dark:border-[#1e2536]">
                                @include('partials.restockForm', ['product' => $product])
                            </td>
                        </tr>

                        @if ($canTransfer)
                            <tr id="transfer-{{ $product->id }}" class="hidden bg-blue-50 dark:bg-blue-950/40">
                                <td colspan="6" class="px-6 py-4 border-t border-gray-200 dark:border-[#1e2536]">
                                    @include('partials.transferForm', ['product' => $product])
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= GRID VIEW ================= --}}
        <div class="view-grid grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 p-6">
            @forelse ($products as $product)
                <div class="border border-gray-200 dark:border-[#1e2536] rounded-xl overflow-hidden bg-white dark:bg-[#161B26] flex flex-col">

                    <div class="cursor-pointer" onclick="openProductDetail({{ $product->id }})">
                        @if ($product->product_image)
                            <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}"
                                 class="w-full aspect-square object-cover" loading="lazy">
                        @else
                            <div class="w-full aspect-square bg-gray-100 dark:bg-[#10141D] flex items-center justify-center text-gray-300 dark:text-slate-600">
                                <svg class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif

                        <div class="p-3">
                            <p class="font-medium text-gray-900 dark:text-white text-sm line-clamp-2">{{ $product->product_name }}</p>
                            <p class="text-xs text-gray-400 dark:text-slate-400 mt-0.5">{{ $product->category->name ?? '—' }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">
                                Store {{ $product->store_quantity ?? 0 }} · Warehouse {{ $product->warehouse_quantity ?? 0 }}
                            </p>
                            <div class="mt-2">@include('partials.stockBadge', ['product' => $product])</div>
                        </div>
                    </div>

                    <div class="mt-auto px-3 pb-3">
                        <div class="flex gap-2 text-sm font-medium">
                            <button type="button" onclick="document.getElementById('card-restock-{{ $product->id }}').classList.toggle('hidden')"
                                    class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-2.5 py-1 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">Restock</button>
                            @if ($canTransfer)
                                <button type="button" onclick="document.getElementById('card-transfer-{{ $product->id }}').classList.toggle('hidden')"
                                        class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-2.5 py-1 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">Transfer</button>
                            @endif
                        </div>

                        <div id="card-restock-{{ $product->id }}" class="hidden mt-3 pt-3 border-t border-gray-100 dark:border-[#1e2536]">
                            @include('partials.restockForm', ['product' => $product])
                        </div>

                        @if ($canTransfer)
                            <div id="card-transfer-{{ $product->id }}" class="hidden mt-3 pt-3 border-t border-gray-100 dark:border-[#1e2536]">
                                @include('partials.transferForm', ['product' => $product])
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-400 dark:text-slate-500 py-10">No products found.</p>
            @endforelse
        </div>

        <div class="px-6 py-4 border-t border-gray-200 dark:border-[#1e2536] text-gray-600 dark:text-slate-400">{{ $products->links() }}</div>
    </div>

    @include('partials.productDetailModal')

@endsection