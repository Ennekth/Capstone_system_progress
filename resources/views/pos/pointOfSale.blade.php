@extends('layouts.default')

@section('title', 'Point of Sale')

@section('content')

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- PRODUCTS -->
        <div class="flex-[2] bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold mb-4">Products</h2>

            <!-- Search & Filter -->
            <form method="GET" action="{{ route('pos') }}" class="flex flex-col sm:flex-row gap-3 mb-5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">

                <select name="category_id"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                        class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800 whitespace-nowrap">
                    Search
                </button>

                @if (request('search') || request('category_id'))
                    <a href="{{ route('pos') }}"
                       class="text-center text-sm font-medium text-gray-600 hover:bg-gray-100 px-4 py-2 rounded-lg">
                        Clear
                    </a>
                @endif
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($items as $item)
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col">
                        <div class="cursor-pointer" onclick="openProductDetail({{ $item->id }})">
                            <h3 class="font-semibold text-gray-900">{{ $item->product_name }}</h3>
                            <p class="text-xs text-gray-400">{{ $item->category->name ?? '—' }}</p>
                            <p class="text-xs text-gray-500 mt-1">Stock: {{ $item->store_quantity }}</p>
                            <p class="text-lg font-bold text-gray-900 mt-2">₱{{ number_format($item->selling_price, 2) }}</p>
                        </div>
                        <form action="{{ route('pos.add', $item->id) }}" method="POST" class="mt-3 space-y-2">
                            @csrf
                            <div>
                                <label for="quantity_{{ $item->id }}" class="block text-xs font-medium text-gray-600 mb-1">
                                    Quantity
                                </label>
                                <input type="number" name="quantity" id="quantity_{{ $item->id }}"
                                       min="1" max="{{ $item->store_quantity }}" value="1" required
                                       class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm
                                              focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            </div>
                            <button type="submit"
                                    class="w-full bg-gray-900 text-white text-sm font-medium py-2 rounded-lg hover:bg-gray-800"
                                    {{ $item->store_quantity <= 0 ? 'disabled' : '' }}>
                                {{ $item->store_quantity <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm col-span-full text-center py-10">No products found.</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $items->links() }}
            </div>
        </div>

        <!-- CART -->
        <div class="flex-1 bg-white border border-gray-200 rounded-xl shadow-sm p-6 h-fit">
            <h2 class="text-lg font-bold mb-4">Cart</h2>

            @if (count($cart) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach ($cart as $id => $item)
                        <div class="py-3">
                            <h3 class="font-medium text-gray-900">{{ $item['product_name'] }}</h3>
                            <p class="text-sm text-gray-500">Price: ₱{{ number_format($item['selling_price'], 2) }}</p>
                            <p class="text-sm text-gray-500">Quantity: {{ $item['quantity'] }}</p>
                            <p class="text-sm font-medium text-gray-900">Subtotal: ₱{{ number_format($item['subtotal'], 2) }}</p>

                            <form action="{{ route('pos.remove', $id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <p class="text-xl font-bold text-gray-900 mt-4">Total: ₱{{ number_format($total, 2) }}</p>

                <a href="{{ route('checkout') }}"
                   class="block w-full text-center bg-green-600 text-white font-medium py-3 rounded-lg mt-4 hover:bg-green-700">
                    Checkout
                </a>
            @else
                <p class="text-gray-400 text-sm">Your cart is empty.</p>
            @endif
        </div>

    </div>
@include('partials.productDetailModal')
@endsection