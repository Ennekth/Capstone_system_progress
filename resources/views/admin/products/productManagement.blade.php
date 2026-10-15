@extends('layouts.default')

@section('title', 'Product Management')

@section('content')
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-bold">Product Management</h1>
                <p class="text-sm text-gray-500 mt-1">Manage products and services offered</p>
            </div>

            <a href="{{ route('admin.products.create') }}"
               class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-800">
                + Add Product
            </a>
        </div>

        <!-- Flash messages -->
        @if (session('success'))
            <div class="mx-6 mt-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mx-6 mt-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Search / Filter / Sort -->
        <div class="px-6 py-4 border-b border-gray-200">
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

                <select name="sort" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="product_name" {{ request('sort', 'product_name') === 'product_name' ? 'selected' : '' }}>Sort: Name</option>
                    <option value="store_quantity" {{ request('sort') === 'store_quantity' ? 'selected' : '' }}>Sort: Store Qty</option>
                    <option value="warehouse_quantity" {{ request('sort') === 'warehouse_quantity' ? 'selected' : '' }}>Sort: Warehouse Qty</option>
                    <option value="selling_price" {{ request('sort') === 'selling_price' ? 'selected' : '' }}>Sort: Price</option>
                    <option value="purchase_cost" {{ request('sort') === 'purchase_cost' ? 'selected' : '' }}>Sort: Cost</option>
                    <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Sort: Newest</option>
                </select>

                <select name="direction" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="asc" {{ request('direction', 'asc') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                    Apply
                </button>

                @if (request()->anyFilled(['search', 'category_id']) || request('sort') !== 'product_name')
                    <a href="{{ request()->url() }}" class="text-sm font-medium text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Cost</th>
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Store Qty</th>
                        <th class="px-6 py-3">Warehouse Qty</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="openProductDetail({{ $product->id }})">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $product->product_name }}</div>
                                @if ($product->approx_volume)
                                    <div class="text-xs text-gray-400">{{ $product->approx_volume }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $product->category->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                ₱{{ $product->purchase_cost !== null ? number_format($product->purchase_cost, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                ₱{{ $product->selling_price !== null ? number_format($product->selling_price, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $product->store_quantity ?? 0 }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $product->warehouse_quantity ?? 0 }}</td>
                            <td class="px-6 py-4">
                                @if ($product->isLowStock())
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        Low Stock
                                    </span>
                                @elseif (($product->store_quantity ?? 0) === 0)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-600">
                                        Out of Stock
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        In Stock
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                          onsubmit="return confirm('Delete this product? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-400">
                                No products found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">
            {{ $products->links() }}
        </div>

    </div>

    @include('partials.productDetailModal')

@endsection