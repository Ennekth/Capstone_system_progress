@extends('layouts.default')

@section('title', 'Product Management')

@section('content')
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-bold">Product Management</h1>
                <p class="text-sm text-gray-500 mt-1">Manage products and services offered</p>
            </div>

            <div class="flex items-center gap-3">
                @include('partials.viewToggle')
                <a href="{{ route('admin.products.create') }}"
                   class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-800">
                    + Add Product
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mx-6 mt-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mx-6 mt-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
        @endif

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

                @if (request()->anyFilled(['search', 'category_id']) || request('sort', 'product_name') !== 'product_name')
                    <a href="{{ request()->url() }}" class="text-sm font-medium text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- ================= LIST VIEW ================= --}}
        <div class="view-list overflow-x-auto">
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
                            <td class="px-6 py-4 text-gray-600">{{ $product->category->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                ₱{{ $product->purchase_cost !== null ? number_format($product->purchase_cost, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                ₱{{ $product->selling_price !== null ? number_format($product->selling_price, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $product->store_quantity ?? 0 }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $product->warehouse_quantity ?? 0 }}</td>
                            <td class="px-6 py-4">@include('partials.stockBadge', ['product' => $product])</td>
                            <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="text-gray-600 hover:text-gray-900 font-medium text-sm">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                          onsubmit="return confirm('Delete this product? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-10 text-center text-gray-400">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= GRID VIEW ================= --}}
        <div class="view-grid grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 p-6">
            @forelse ($products as $product)
                <div class="border border-gray-200 rounded-xl overflow-hidden bg-white flex flex-col">

                    <div class="cursor-pointer" onclick="openProductDetail({{ $product->id }})">
                        @if ($product->product_image)
                            <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}"
                                 class="w-full aspect-square object-cover" loading="lazy">
                        @else
                            <div class="w-full aspect-square bg-gray-100 flex items-center justify-center text-gray-300">
                                <svg class="h-12 w-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif

                        <div class="p-3">
                            <p class="font-medium text-gray-900 text-sm line-clamp-2">{{ $product->product_name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $product->category->name ?? '—' }}</p>
                            <p class="text-sm font-semibold text-gray-900 mt-2">
                                ₱{{ $product->selling_price !== null ? number_format($product->selling_price, 2) : '—' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Store {{ $product->store_quantity ?? 0 }} · Warehouse {{ $product->warehouse_quantity ?? 0 }}
                            </p>
                            <div class="mt-2">@include('partials.stockBadge', ['product' => $product])</div>
                        </div>
                    </div>

                    <div class="mt-auto px-3 pb-3 flex items-center gap-4 text-sm font-medium">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Delete this product? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-gray-400 py-10">No products found.</p>
            @endforelse
        </div>

        <div class="px-6 py-4">{{ $products->links() }}</div>

    </div>

    @include('partials.productDetailModal')

@endsection