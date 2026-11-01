@extends('layouts.default')

@section('title', 'Point of Sale')

@section('content')

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 dark:bg-rose-950/85 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 dark:bg-emerald-950/85 text-green-800 dark:text-emerald-300 border border-green-200 dark:border-emerald-900 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- PRODUCTS -->
        <div class="flex-[2] bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Products</h2>

            <!-- Search & Filter -->
            <form method="GET" action="{{ route('pos') }}" class="flex flex-col sm:flex-row gap-3 mb-5">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                       class="flex-1 rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                              focus:outline-none focus:ring-1 focus:ring-slate-400">

                <select name="category_id"
                        class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100
                               focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Search Button -->
                <button type="submit"
                        class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-300 ease-in-out whitespace-nowrap shadow-xs cursor-pointer">
                    Search
                </button>

                @if (request('search') || request('category_id'))
                    <a href="{{ route('pos') }}"
                       class="text-center text-sm font-medium text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] px-4 py-2 rounded-lg transition-colors">
                        Clear
                    </a>
                @endif
            </form>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($items as $item)
                    <div class="border border-gray-200 dark:border-[#1e2536] bg-white dark:bg-[#161B26] rounded-lg p-4 flex flex-col transition-colors">
                        <div class="cursor-pointer" onclick="openProductDetail({{ $item->id }})">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->product_name }}</h3>
                            <p class="text-xs text-gray-400 dark:text-slate-400">{{ $item->category->name ?? '—' }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Stock: {{ $item->store_quantity }}</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-slate-100 mt-2">₱{{ number_format($item->selling_price, 2) }}</p>
                        </div>
                        <form action="{{ route('pos.add', $item->id) }}" method="POST" class="mt-3 space-y-2">
                            @csrf
                            <div>
                                <label for="quantity_{{ $item->id }}" class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">
                                    Quantity
                                </label>
                                <input type="number" name="quantity" id="quantity_{{ $item->id }}"
                                       min="1" max="{{ $item->store_quantity }}" value="1" required
                                       class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-2 py-1.5 text-sm text-gray-900 dark:text-slate-100
                                              focus:outline-none focus:ring-1 focus:ring-slate-400">
                            </div>
                            <!-- Add to Cart Button -->
                            <button type="submit"
                                    class="w-full bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white font-semibold py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer"
                                    {{ $item->store_quantity <= 0 ? 'disabled' : '' }}>
                                {{ $item->store_quantity <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-gray-400 dark:text-slate-500 text-sm col-span-full text-center py-10">No products found.</p>
                @endforelse
            </div>

            <div class="mt-6 text-gray-600 dark:text-slate-400">
                {{ $items->links() }}
            </div>
        </div>

        <!-- CART -->
        <div class="flex-1 bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm p-6 h-fit">
            <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Cart</h2>

            @if (count($cart) > 0)
                <div class="divide-y divide-gray-100 dark:divide-[#1e2536] mb-4">
                    @foreach ($cart as $id => $item)
                        <div class="py-3 flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">{{ $item['product_name'] }}</h3>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Price: ₱{{ number_format($item['selling_price'], 2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Quantity: {{ $item['quantity'] }}</p>
                                <p class="text-xs font-medium text-gray-900 dark:text-slate-200 mt-0.5">Subtotal: ₱{{ number_format($item['subtotal'] ?? ($item['selling_price'] * $item['quantity']), 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center mb-6 pt-4 border-t border-gray-200 dark:border-[#1e2536]">
                    <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">₱{{ number_format($total, 2) }}</span>
                </div>

                <div class="space-y-3">
                    <!-- Checkout Button -->
                    <a href="{{ route('checkout') }}"
                       class="block w-full text-center bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white font-semibold py-3 rounded-xl transition-all duration-300 ease-in-out shadow-sm cursor-pointer">
                        Checkout
                    </a>

                    @foreach ($cart as $id => $item)
                        <form action="{{ route('pos.remove', $id) }}" method="POST">
                            @csrf
                            <!-- Remove Item Button -->
                            <button type="submit" 
                                    class="w-full bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-sm font-semibold py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-sm text-center cursor-pointer">
                                Remove {{ count($cart) > 1 ? $item['product_name'] : 'Item' }}
                            </button>
                        </form>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 dark:text-slate-500 text-sm">Your cart is empty.</p>
            @endif
        </div>

    </div>
@include('partials.productDetailModal')
@endsection