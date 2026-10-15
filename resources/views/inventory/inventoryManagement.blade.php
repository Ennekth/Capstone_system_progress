@extends('layouts.default')

@section('title', 'Inventory')

@section('content')

    @php
        $canTransfer = in_array(auth()->user()->role, ['admin', 'manager']);
    @endphp

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <div class="px-6 py-5 border-b border-gray-200">

            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
                <h1 class="text-xl font-bold">Restock and Move Products</h1>
                <a href="{{ route('inventory.restockBatch') }}"
                class="bg-green-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-green-700">
                    + Restock Mode
                </a>
            </div>

            <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                       class="flex-1 min-w-[180px] rounded-lg border border-gray-300 px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">

                <select name="category_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="">All Status</option>
                    <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>

                <select name="sort" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="product_name" {{ request('sort', 'product_name') === 'product_name' ? 'selected' : '' }}>Sort: Name</option>
                    <option value="store_quantity" {{ request('sort') === 'store_quantity' ? 'selected' : '' }}>Sort: Store Qty</option>
                    <option value="warehouse_quantity" {{ request('sort') === 'warehouse_quantity' ? 'selected' : '' }}>Sort: Warehouse Qty</option>
                    <option value="selling_price" {{ request('sort') === 'selling_price' ? 'selected' : '' }}>Sort: Price</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Sort: Newest</option>
                </select>

                <select name="direction" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                    Apply
                </button>

                @if (request()->anyFilled(['search', 'category_id', 'status']) || request('sort') !== 'product_name')
                    <a href="{{ request()->url() }}" class="text-sm font-medium text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Store Qty</th>
                        <th class="px-6 py-3">Warehouse Qty</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="openProductDetail({{ $product->id }})">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $product->product_name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $product->category->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $product->store_quantity ?? 0 }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $product->warehouse_quantity ?? 0 }}</td>
                            <td class="px-6 py-4">
                                @if (($product->store_quantity ?? 0) === 0)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-600">Out of Stock</span>
                                @elseif ($product->isLowStock())
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Low Stock</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">In Stock</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                                <button type="button" onclick="document.getElementById('restock-{{ $product->id }}').classList.toggle('hidden')"
                                        class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                                    Restock
                                </button>
                                @if ($canTransfer)
                                    <button type="button" onclick="document.getElementById('transfer-{{ $product->id }}').classList.toggle('hidden')"
                                            class="text-blue-600 hover:text-blue-800 font-medium text-sm ml-3">
                                        Transfer
                                    </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Restock form row -->
                        <tr id="restock-{{ $product->id }}" class="hidden bg-gray-50">
                            <td colspan="6" class="px-6 py-4">
                                <form action="{{ route('inventory.restock', $product) }}" method="POST" class="flex flex-wrap items-end gap-3">
                                    @csrf
                                    <input type="hidden" name="type"
                                           value="{{ auth()->user()->role === 'warehouse_staff' ? 'restock_warehouse' : 'restock_store' }}">

                                    @if (!in_array(auth()->user()->role, ['warehouse_staff', 'staff']))
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Location</label>
                                            <select name="location" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                                <option value="store">Store</option>
                                                <option value="warehouse">Warehouse</option>
                                            </select>
                                        </div>
                                    @else
                                        <input type="hidden" name="location"
                                               value="{{ auth()->user()->role === 'warehouse_staff' ? 'warehouse' : 'store' }}">
                                        <p class="text-xs text-gray-500">
                                            Restocking: <strong>{{ auth()->user()->role === 'warehouse_staff' ? 'Warehouse' : 'Store' }}</strong>
                                        </p>
                                    @endif

                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Quantity</label>
                                        <input type="number" name="quantity" min="1" required
                                               class="w-24 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    </div>

                                    <div class="flex-1 min-w-[160px]">
                                        <label class="block text-xs text-gray-600 mb-1">Note (optional)</label>
                                        <input type="text" name="note" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                    </div>

                                    <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-800">
                                        Submit
                                    </button>
                                </form>
                                @if (auth()->user()->role === 'staff')
                                    <p class="text-xs text-amber-600 mt-2">This request will need admin/manager approval before stock updates.</p>
                                @endif
                            </td>
                        </tr>

                        @if ($canTransfer)
                            <tr id="transfer-{{ $product->id }}" class="hidden bg-blue-50">
                                <td colspan="6" class="px-6 py-4">
                                    <form action="{{ route('inventory.transfer', $product) }}" method="POST" class="flex flex-wrap items-end gap-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Direction</label>
                                            <select name="direction" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                                <option value="to_store">Warehouse → Store</option>
                                                <option value="to_warehouse">Store → Warehouse</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Quantity</label>
                                            <input type="number" name="quantity" min="1" required
                                                   class="w-24 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        </div>
                                        <div class="flex-1 min-w-[160px]">
                                            <label class="block text-xs text-gray-600 mb-1">Note (optional)</label>
                                            <input type="text" name="note" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                                        </div>
                                        <button type="submit" class="bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-700">
                                            Transfer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $products->links() }}</div>
    </div>

    @include('partials.productDetailModal')

@endsection