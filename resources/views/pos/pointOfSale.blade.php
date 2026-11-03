@extends('layouts.default')

@section('title', 'Point of Sale')

@section('content')

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4 flex-wrap">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Point of Sale</h1>

            <div class="flex items-center gap-2">
                <!-- Counter POS badge pill -->
                <div class="inline-flex items-center gap-2 bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-3.5 py-1.5 rounded-full shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Counter POS</span>
                </div>

                <!-- Parked Orders Button -->
                @php
                    $parkedOrders = session()->get('parked_orders', []);
                    $parkedCount = count($parkedOrders);
                @endphp
                <button type="button" onclick="openParkedModal()"
                   class="inline-flex items-center gap-1.5 bg-amber-50 dark:bg-amber-950/40 border-2 border-amber-200 dark:border-amber-900/60 px-3.5 py-1.5 rounded-full text-xs font-bold text-amber-700 dark:text-amber-400 hover:bg-amber-100 transition-all cursor-pointer shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Parked Orders ({{ $parkedCount }})
                </button>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="mb-6 rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-6 rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-2 border-emerald-200 dark:border-emerald-900 px-6 py-4 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">

        <!-- PRODUCTS SECTION -->
        <div class="flex-[2] flex flex-col gap-6">
            
            <!-- Filter Bar & View Switcher -->
            <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <form id="filterForm" method="GET" action="{{ route('pos') }}" class="relative w-full md:w-80">
                    @if(request('category_id'))
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    @endif
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search items or categories..." autocomplete="off"
                           class="w-full rounded-xl bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] pl-10 pr-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 focus:outline-none focus:border-slate-400">
                </form>

                <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                    <!-- Structured Category Dropdown -->
                    <select onchange="window.location.href=this.value"
                            class="w-full sm:w-60 rounded-xl bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                        <option value="{{ route('pos', ['search' => request('search')]) }}">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ route('pos', ['category_id' => $category->id, 'search' => request('search')]) }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @foreach ($category->children->sortBy('name') as $child)
                                <option value="{{ route('pos', ['category_id' => $child->id, 'search' => request('search')]) }}"
                                        {{ request('category_id') == $child->id ? 'selected' : '' }}>
                                    &nbsp;&nbsp;&mdash; {{ $child->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>

                    <!-- Grid & List View Switcher Buttons -->
                    <div class="flex items-center bg-gray-100 dark:bg-[#161B26] p-1 rounded-xl border-2 border-gray-200 dark:border-[#1e2536] shrink-0">
                        <button type="button" onclick="switchView('grid')" id="gridViewBtn" 
                                class="p-2 rounded-lg text-gray-600 dark:text-slate-300 transition-all cursor-pointer" title="Grid View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </button>
                        <button type="button" onclick="switchView('list')" id="listViewBtn" 
                                class="p-2 rounded-lg text-gray-600 dark:text-slate-300 transition-all cursor-pointer" title="List View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Containers (Grid & List View Switcher Wrappers) -->
            <div class="overflow-y-auto pr-1">
                
                <!-- GRID VIEW CONTAINER -->
                <div id="productGridView" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 pb-4">
                    @forelse ($items as $item)
                        @php
                            $cartQty = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
                            $remainingStock = $item->store_quantity;
                        @endphp

                        @if($remainingStock > 0)
                            <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-6 flex flex-col justify-between shadow-xs transition-all duration-200">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $item->product_name }}</h3>
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">{{ $item->category->name ?? 'Uncategorized' }}</p>
                                </div>

                                <div class="mt-6 flex items-center justify-between">
                                    <span class="text-lg font-bold text-gray-900 dark:text-slate-100">₱{{ number_format($item->selling_price, 2) }}</span>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 bg-gray-100 dark:bg-[#161B26] px-2.5 py-1 rounded-full border border-gray-200 dark:border-[#1e2536]">Stock: {{ $remainingStock }}</span>
                                </div>

                                <div class="mt-6 pt-4 border-t-2 border-gray-100 dark:border-[#1e2536] flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-500 dark:text-slate-400">In Cart: <strong class="text-gray-900 dark:text-white">{{ $cartQty }}</strong></span>
                                    
                                    <div class="flex items-center border-2 border-gray-200 dark:border-[#1e2536] rounded-xl overflow-hidden bg-gray-50 dark:bg-[#161B26]" onclick="event.stopPropagation()">
                                        @if($cartQty > 0)
                                            <form action="{{ route('pos.add', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="quantity" value="-1">
                                                <button type="submit" class="px-3 py-1.5 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-[#1e2536] transition-colors text-xs font-bold cursor-pointer" title="Decrease">-</button>
                                            </form>
                                        @else
                                            <button type="button" disabled class="px-3 py-1.5 text-gray-300 dark:text-slate-600 text-xs font-bold cursor-not-allowed">-</button>
                                        @endif

                                        <span class="px-3.5 py-1.5 text-xs font-bold text-gray-900 dark:text-slate-100">{{ $cartQty }}</span>

                                        <form action="{{ route('pos.add', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="px-3 py-1.5 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-[#1e2536] transition-colors text-xs font-bold cursor-pointer" title="Increase">+</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50/50 dark:bg-[#10141D]/50 border-2 border-gray-200/60 dark:border-[#1e2536]/60 rounded-3xl p-6 flex flex-col justify-between opacity-60 pointer-events-none select-none">
                                <div>
                                    <h3 class="font-bold text-gray-500 dark:text-slate-400">{{ $item->product_name }}</h3>
                                    <p class="text-xs text-gray-400 dark:text-slate-600 mt-1">{{ $item->category->name ?? 'Uncategorized' }}</p>
                                </div>
                                <div class="mt-6 flex items-end justify-between">
                                    <span class="text-lg font-bold text-gray-400 dark:text-slate-500">₱{{ number_format($item->selling_price, 2) }}</span>
                                    <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-2.5 py-1 rounded-full border border-rose-200 dark:border-rose-900">Out of Stock</span>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="col-span-full py-16 text-center text-gray-400 dark:text-slate-500 font-medium text-sm">
                            No products found matching your criteria.
                        </div>
                    @endforelse
                </div>

                <!-- LIST VIEW CONTAINER -->
                <div id="productListView" class="hidden flex flex-col gap-3 pb-4">
                    @forelse ($items as $item)
                        @php
                            $cartQty = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
                            $remainingStock = $item->store_quantity;
                        @endphp

                        @if($remainingStock > 0)
                            <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-2xl p-4 flex items-center justify-between gap-4 shadow-xs">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 dark:text-white truncate">{{ $item->product_name }}</h3>
                                    <p class="text-xs text-gray-400 dark:text-slate-500">{{ $item->category->name ?? 'Uncategorized' }}</p>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-base font-bold text-gray-900 dark:text-slate-100">₱{{ number_format($item->selling_price, 2) }}</span>
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400">Stock: {{ $remainingStock }}</p>
                                </div>

                                <div class="flex items-center gap-4 shrink-0">
                                    <span class="text-xs text-gray-500 hidden sm:inline">In Cart: <strong>{{ $cartQty }}</strong></span>
                                    <div class="flex items-center border-2 border-gray-200 dark:border-[#1e2536] rounded-xl overflow-hidden bg-gray-50 dark:bg-[#161B26]">
                                        @if($cartQty > 0)
                                            <form action="{{ route('pos.add', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="quantity" value="-1">
                                                <button type="submit" class="px-3 py-1.5 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-[#1e2536] text-xs font-bold">-</button>
                                            </form>
                                        @else
                                            <button type="button" disabled class="px-3 py-1.5 text-gray-300 dark:text-slate-600 text-xs font-bold cursor-not-allowed">-</button>
                                        @endif

                                        <span class="px-3 py-1.5 text-xs font-bold text-gray-900 dark:text-slate-100">{{ $cartQty }}</span>

                                        <form action="{{ route('pos.add', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="px-3 py-1.5 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-[#1e2536] text-xs font-bold">+</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50/50 dark:bg-[#10141D]/50 border-2 border-gray-200/60 dark:border-[#1e2536]/60 rounded-2xl p-4 flex items-center justify-between opacity-60 pointer-events-none select-none">
                                <div>
                                    <h3 class="font-bold text-gray-500 dark:text-slate-400">{{ $item->product_name }}</h3>
                                    <p class="text-xs text-gray-400 dark:text-slate-600">{{ $item->category->name ?? 'Uncategorized' }}</p>
                                </div>
                                <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 px-2.5 py-1 rounded-full border border-rose-200 dark:border-rose-900">Out of Stock</span>
                            </div>
                        @endif
                    @empty
                    @endforelse
                </div>

            </div>
        </div>

        <!-- ORDER SUMMARY SIDEBAR -->
        <div class="flex-1 bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8 flex flex-col justify-between h-fit min-h-[550px]">
            <div>
                <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Order Summary</h2>

                    <div class="flex items-center gap-2">
                        @if (count($cart) > 0)
                            <form action="{{ route('pos.hold') }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-900 px-3 py-1 rounded-full text-xs font-bold transition-all cursor-pointer flex items-center gap-1 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Hold
                                </button>
                            </form>
                        @endif

                        <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 bg-gray-100 dark:bg-[#161B26] px-3 py-1 rounded-full border border-gray-200 dark:border-[#1e2536]">
                            {{ array_sum(array_column($cart, 'quantity')) }} {{ array_sum(array_column($cart, 'quantity')) === 1 ? 'item' : 'items' }}
                        </span>
                    </div>
                </div>

                @if (count($cart) > 0)
                    <div class="divide-y divide-gray-100 dark:divide-[#1e2536] max-h-[360px] overflow-y-auto pr-1">
                        @foreach ($cart as $id => $item)
                            <div class="py-4 flex flex-col gap-3 group border-b-2 border-gray-100 dark:border-[#1e2536]">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['product_name'] }}</h3>
                                            <span class="text-sm font-bold text-gray-900 dark:text-slate-100">₱{{ number_format($item['subtotal'], 2) }}</span>
                                        </div>
                                        <p class="text-xs text-gray-400 dark:text-slate-400 mt-0.5">Qty: {{ $item['quantity'] }} × ₱{{ number_format($item['selling_price'], 2) }}</p>
                                        
                                        @if(($item['item_discount_type'] ?? 'none') !== 'none' && ($item['item_discount_amount'] ?? 0) > 0)
                                            <p class="text-xs font-medium text-rose-600 dark:text-rose-400 mt-1 flex items-center gap-1">
                                                <span>Item Disc:</span>
                                                <span>-₱{{ number_format($item['item_discount_amount'], 2) }}</span>
                                                <span class="text-[10px] text-gray-400">({{ ($item['item_discount_scope'] ?? 'whole') === 'per_item' ? 'Per Piece' : 'Whole Row' }})</span>
                                            </p>
                                        @endif
                                    </div>
                                    <form action="{{ route('pos.remove', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors p-1 cursor-pointer" title="Remove item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>

                                <!-- Per-Item Discount Form -->
                                <form action="{{ route('pos.item-discount', $id) }}" method="POST" class="flex flex-wrap items-center gap-2 pt-1 text-xs bg-gray-50 dark:bg-[#161B26] p-2.5 rounded-xl border-2 border-gray-200 dark:border-[#1e2536]">
                                    @csrf
                                    <select name="item_discount_type" class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-lg px-2 py-1 text-gray-600 dark:text-slate-300 text-xs cursor-pointer">
                                        <option value="none" {{ ($item['item_discount_type'] ?? '') == 'none' ? 'selected' : '' }}>No Disc</option>
                                        <option value="amount" {{ ($item['item_discount_type'] ?? '') == 'amount' ? 'selected' : '' }}>Disc (₱)</option>
                                        <option value="percentage" {{ ($item['item_discount_type'] ?? '') == 'percentage' ? 'selected' : '' }}>Disc (%)</option>
                                    </select>

                                    <select name="item_discount_scope" class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-lg px-2 py-1 text-gray-600 dark:text-slate-300 text-xs cursor-pointer">
                                        <option value="whole" {{ ($item['item_discount_scope'] ?? 'whole') == 'whole' ? 'selected' : '' }}>Whole Row</option>
                                        <option value="per_item" {{ ($item['item_discount_scope'] ?? '') == 'per_item' ? 'selected' : '' }}>Per Piece</option>
                                    </select>

                                    <input type="number" step="0.01" min="0" name="item_discount_value" value="{{ $item['item_discount_value'] ?? '' }}" placeholder="Value"
                                           class="w-20 bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-lg px-2.5 py-1 text-gray-900 dark:text-slate-100 text-xs focus:outline-none focus:border-slate-400">
                                    
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3 py-1 rounded-lg transition-colors ml-auto cursor-pointer">Set</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-20 text-center text-gray-400 dark:text-slate-500 font-medium text-sm">
                        Your cart is empty. Use the +/- buttons on product cards to add items.
                    </div>
                @endif
            </div>

            <!-- Cart Footer Totals & Discounts Integration -->
            <div class="pt-6 border-t-2 border-gray-200 dark:border-[#1e2536] mt-6">
                @php
                    $discountedItemsList = [];
                    foreach ($cart as $item) {
                        if (($item['item_discount_type'] ?? 'none') !== 'none' && ($item['item_discount_amount'] ?? 0) > 0) {
                            $discountedItemsList[] = $item;
                        }
                    }
                    $totalAllDiscounts = $totalItemDiscounts + ($discountAmount ?? 0);
                @endphp

                <div class="space-y-2 mb-6 text-sm">
                    <div class="flex justify-between text-gray-600 dark:text-slate-400 font-medium">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center text-gray-600 dark:text-slate-400 relative">
                        <button type="button" onclick="openDiscountModal()" class="text-emerald-600 dark:text-emerald-400 hover:underline font-bold text-xs flex items-center gap-1 cursor-pointer z-10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            Discounts ({{ isset($cartDiscount) && $cartDiscount['type'] !== 'none' ? ucfirst(str_replace('_', ' ', $cartDiscount['type'])) : 'Configure' }})
                        </button>
                        <span class="text-rose-600 dark:text-rose-400 font-bold">-₱{{ number_format($totalAllDiscounts, 2) }}</span>
                    </div>

                    @if(count($discountedItemsList) > 0 || (isset($cartDiscount) && $cartDiscount['type'] !== 'none'))
                        <div class="bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] rounded-2xl p-4 text-xs space-y-3 my-3">
                            <div class="flex justify-between text-gray-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px] pb-2 border-b-2 border-gray-200 dark:border-[#1e2536]">
                                <span>Applied Discount Details</span>
                                <span class="text-emerald-600 dark:text-emerald-400">
                                    Total Saved: ₱{{ number_format($totalAllDiscounts, 2) }}
                                </span>
                            </div>

                            @if(count($discountedItemsList) > 0)
                                <div class="space-y-1.5">
                                    <p class="font-bold text-gray-700 dark:text-slate-300 text-[11px] uppercase">Item Discounts:</p>
                                    @foreach($discountedItemsList as $discItem)
                                        @php
                                            $scopeLabel = (($discItem['item_discount_scope'] ?? 'whole') === 'per_item') ? 'Per Piece' : 'Whole Row';
                                        @endphp
                                        <div class="flex justify-between items-center text-gray-600 dark:text-slate-400 pl-2 text-[11px]">
                                            <span class="truncate max-w-[170px]">• {{ $discItem['product_name'] }} <span class="text-[10px] text-gray-400 dark:text-slate-500">({{ $scopeLabel }})</span></span>
                                            <span class="text-rose-600 dark:text-rose-400 font-semibold">-₱{{ number_format($discItem['item_discount_amount'], 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(isset($cartDiscount) && $cartDiscount['type'] !== 'none')
                                <div class="pt-2 border-t-2 border-gray-200 dark:border-[#1e2536] space-y-1 text-[11px]">
                                    <div class="flex justify-between font-bold text-gray-700 dark:text-slate-300">
                                        <span>Cart Discount ({{ ucfirst(str_replace('_', ' ', $cartDiscount['type'])) }})</span>
                                        <span class="text-rose-600 dark:text-rose-400">-₱{{ number_format($discountAmount ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="flex justify-between items-center text-lg font-bold text-gray-900 dark:text-white pt-3 border-t-2 border-gray-100 dark:border-[#1e2536]">
                        <span>Total</span>
                        <span class="text-xl">₱{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                @if(count($cart) > 0)
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="openClearModal()"
                                class="w-1/3 text-center bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white font-semibold py-3 px-3 rounded-xl transition-all duration-300 ease-in-out text-xs sm:text-sm cursor-pointer shadow-xs">
                            Clear
                        </button>
                        <a href="{{ route('checkout') }}"
                           class="w-2/3 text-center bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 ease-in-out text-xs sm:text-sm shadow-xs cursor-pointer">
                            Proceed to Checkout
                        </a>
                    </div>
                @else
                    <button disabled
                            class="w-full text-center bg-gray-100 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] text-gray-400 dark:text-slate-600 font-semibold py-3 rounded-xl cursor-not-allowed">
                        Proceed to Checkout
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- PARKED ORDERS MODAL -->
    <div id="parkedModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left max-h-[85vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between pb-4 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Parked Orders</h3>
                </div>
                <button type="button" onclick="closeParkedModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer text-xl font-bold">
                    &times;
                </button>
            </div>

            @php
                $parkedList = session()->get('parked_orders', []);
            @endphp

            @if(count($parkedList) > 0)
                <div class="space-y-4">
                    @foreach($parkedList as $index => $parked)
                        <div class="border-2 border-gray-200 dark:border-[#1e2536] rounded-2xl p-5 bg-gray-50/50 dark:bg-[#161B26] flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $parked['id'] }}</span>
                                    <span class="text-[10px] font-semibold bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-900">
                                        {{ $parked['item_count'] }} {{ $parked['item_count'] === 1 ? 'item' : 'items' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 dark:text-slate-400">Parked at: {{ $parked['time'] }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('pos.parked.restore', $index) }}" 
                                   class="bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all shadow-xs cursor-pointer">
                                    Restore
                                </a>
                                <form action="{{ route('pos.parked.delete', $index) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-3 py-2 rounded-xl transition-all shadow-xs cursor-pointer">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-gray-400 dark:text-slate-500 font-medium text-sm">
                    No parked orders found.
                </div>
            @endif

            <div class="flex justify-end pt-6 border-t-2 border-gray-200 dark:border-[#1e2536] mt-6">
                <button type="button" onclick="closeParkedModal()" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all cursor-pointer">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- CLEAR ORDER CONFIRMATION MODAL -->
    <div id="clearModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-md bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left" onclick="event.stopPropagation()">
            <div class="flex items-center gap-4 mb-6">
                <span class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Clear Order</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Are you sure you want to clear the entire order?</p>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                <button type="button" onclick="closeClearModal()"
                        class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out cursor-pointer">
                    Cancel
                </button>
                <form action="{{ route('pos.clear') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out cursor-pointer shadow-xs">
                        Yes, Clear Order
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- DISCOUNT POPUP MODAL (Enhanced with Employee & Voucher Configs) -->
    <div id="discountModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            
            <div class="flex items-center justify-between pb-4 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Apply Cart Discount</h3>
                </div>
                <button type="button" onclick="closeDiscountModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('pos.discount') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="discount_type" id="selectedDiscountType" value="{{ $cartDiscount['type'] ?? 'none' }}">

                <!-- GOVERNMENT DISCOUNTS -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-3">Government Discounts</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="selectDiscountCard('senior')" id="card-senior"
                                class="discount-card p-4 rounded-2xl border-2 border-gray-200 dark:border-[#1e2536] bg-white dark:bg-[#161B26] text-left transition-all cursor-pointer hover:border-emerald-500">
                            <div class="font-bold text-gray-900 dark:text-white text-sm">Senior Citizen</div>
                            <div class="text-xs text-gray-400 dark:text-slate-400 mt-0.5">20% off</div>
                        </button>
                        <button type="button" onclick="selectDiscountCard('pwd')" id="card-pwd"
                                class="discount-card p-4 rounded-2xl border-2 border-gray-200 dark:border-[#1e2536] bg-white dark:bg-[#161B26] text-left transition-all cursor-pointer hover:border-emerald-500">
                            <div class="font-bold text-gray-900 dark:text-white text-sm">PWD</div>
                            <div class="text-xs text-gray-400 dark:text-slate-400 mt-0.5">20% off</div>
                        </button>
                    </div>
                </div>

                <!-- SPECIAL & CUSTOM DISCOUNTS -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-slate-500 mb-3">Special & Custom Discounts</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <button type="button" onclick="selectDiscountCard('employee')" id="card-employee"
                                class="discount-card p-3 rounded-2xl border-2 border-gray-200 dark:border-[#1e2536] bg-white dark:bg-[#161B26] text-left transition-all cursor-pointer hover:border-emerald-500">
                            <div class="font-bold text-gray-900 dark:text-white text-xs">Employee</div>
                            <div class="text-[10px] text-gray-400 dark:text-slate-400 mt-0.5">Staff perk</div>
                        </button>
                        <button type="button" onclick="selectDiscountCard('voucher')" id="card-voucher"
                                class="discount-card p-3 rounded-2xl border-2 border-gray-200 dark:border-[#1e2536] bg-white dark:bg-[#161B26] text-left transition-all cursor-pointer hover:border-emerald-500">
                            <div class="font-bold text-gray-900 dark:text-white text-xs">Voucher</div>
                            <div class="text-[10px] text-gray-400 dark:text-slate-400 mt-0.5">Promo code</div>
                        </button>
                        <button type="button" onclick="selectDiscountCard('percentage')" id="card-percentage"
                                class="discount-card p-3 rounded-2xl border-2 border-gray-200 dark:border-[#1e2536] bg-white dark:bg-[#161B26] text-left transition-all cursor-pointer hover:border-emerald-500">
                            <div class="font-bold text-gray-900 dark:text-white text-xs">Percentage</div>
                            <div class="text-[10px] text-gray-400 dark:text-slate-400 mt-0.5">% off total</div>
                        </button>
                        <button type="button" onclick="selectDiscountCard('amount')" id="card-amount"
                                class="discount-card p-3 rounded-2xl border-2 border-gray-200 dark:border-[#1e2536] bg-white dark:bg-[#161B26] text-left transition-all cursor-pointer hover:border-emerald-500">
                            <div class="font-bold text-gray-900 dark:text-white text-xs">Amount</div>
                            <div class="text-[10px] text-gray-400 dark:text-slate-400 mt-0.5">Fixed peso</div>
                        </button>
                    </div>
                </div>

                <!-- CONDITIONAL INPUT CONTAINERS -->
                <div id="dynamicInputContainer" class="hidden bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] rounded-2xl p-4 space-y-4">
                    
                    <!-- Senior / PWD fields -->
                    <div id="fields-senior-pwd" class="discount-field-group hidden space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">Customer Name *</label>
                            <input type="text" name="customer_name" value="{{ $cartDiscount['customer_name'] ?? '' }}" placeholder="Enter full name" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-4 py-2.5 text-xs text-gray-900 dark:text-slate-100">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">ID Number *</label>
                            <input type="text" name="id_number" value="{{ $cartDiscount['id_number'] ?? '' }}" placeholder="Enter ID number" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-4 py-2.5 text-xs text-gray-900 dark:text-slate-100">
                        </div>
                    </div>

                    <!-- Employee fields -->
                    <div id="fields-employee" class="discount-field-group hidden space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">Employee Name *</label>
                            <input type="text" name="employee_name" value="{{ $cartDiscount['employee_name'] ?? '' }}" placeholder="Enter employee name" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-4 py-2.5 text-xs text-gray-900 dark:text-slate-100">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">Employee ID *</label>
                            <input type="text" name="employee_id" value="{{ $cartDiscount['employee_id'] ?? '' }}" placeholder="Enter employee ID" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-4 py-2.5 text-xs text-gray-900 dark:text-slate-100">
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-200 dark:border-[#1e2536]">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">Discount Mode</label>
                                <select name="employee_mode" id="employeeModeSelect" onchange="toggleEmployeeMode()" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-slate-100 cursor-pointer">
                                    <option value="percentage" {{ (isset($cartDiscount['mode']) && $cartDiscount['mode'] == 'percentage') ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="amount" {{ (isset($cartDiscount['mode']) && $cartDiscount['mode'] == 'amount') ? 'selected' : '' }}>Fixed Amount (₱)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1" id="employeeValueLabel">Value</label>
                                <input type="number" step="0.01" min="0" name="employee_value" id="employeeValueInput" value="{{ $cartDiscount['value'] ?? '' }}" placeholder="Value" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-slate-100">
                            </div>
                        </div>
                    </div>

                    <!-- Voucher fields -->
                    <div id="fields-voucher" class="discount-field-group hidden space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">Voucher Code *</label>
                            <input type="text" name="voucher_code" value="{{ $cartDiscount['voucher_code'] ?? '' }}" placeholder="Enter voucher code" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-4 py-2.5 text-xs text-gray-900 dark:text-slate-100">
                        </div>
                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-200 dark:border-[#1e2536]">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">Discount Mode</label>
                                <select name="voucher_mode" id="voucherModeSelect" onchange="toggleVoucherMode()" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-slate-100 cursor-pointer">
                                    <option value="percentage" {{ (isset($cartDiscount['mode']) && $cartDiscount['mode'] == 'percentage') ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="amount" {{ (isset($cartDiscount['mode']) && $cartDiscount['mode'] == 'amount') ? 'selected' : '' }}>Fixed Amount (₱)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1" id="voucherValueLabel">Value</label>
                                <input type="number" step="0.01" min="0" name="voucher_value" id="voucherValueInput" value="{{ $cartDiscount['value'] ?? '' }}" placeholder="Value" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-slate-100">
                            </div>
                        </div>
                    </div>

                    <!-- Percentage field -->
                    <div id="fields-percentage" class="discount-field-group hidden space-y-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">PERCENTAGE DISCOUNT *</label>
                        <p class="text-[11px] text-gray-400">Enter a percentage value between 0 and 100.</p>
                        <input type="number" step="0.01" min="0" max="100" name="percentage_value" value="{{ $cartDiscount['value'] ?? '' }}" placeholder="Enter percentage" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-4 py-2.5 text-xs text-gray-900 dark:text-slate-100">
                    </div>

                    <!-- Amount field -->
                    <div id="fields-amount" class="discount-field-group hidden space-y-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-slate-300 mb-1">AMOUNT DISCOUNT *</label>
                        <p class="text-[11px] text-gray-400">Enter a fixed peso amount to deduct.</p>
                        <input type="number" step="0.01" min="0" name="amount_value" value="{{ $cartDiscount['value'] ?? '' }}" placeholder="Enter amount" class="w-full bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-xl px-4 py-2.5 text-xs text-gray-900 dark:text-slate-100">
                    </div>

                </div>

                <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                    <a href="{{ route('pos.discount') }}" onclick="event.preventDefault(); document.getElementById('removeDiscountForm').submit();" class="text-xs font-bold text-rose-600 hover:underline cursor-pointer">Remove Discount</a>
                    
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="closeDiscountModal()" class="bg-white border-2 border-slate-300 text-slate-700 dark:bg-[#10141D] dark:border-[#1e2536] dark:text-slate-300 text-xs font-semibold px-4 py-2.5 rounded-xl cursor-pointer">Cancel</button>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-5 py-2.5 rounded-xl cursor-pointer shadow-xs">Apply Discount</button>
                    </div>
                </div>
            </form>

            <form id="removeDiscountForm" action="{{ route('pos.discount') }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="discount_type" value="none">
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function openParkedModal() {
        document.getElementById('parkedModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeParkedModal() {
        document.getElementById('parkedModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openClearModal() {
        document.getElementById('clearModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeClearModal() {
        document.getElementById('clearModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openDiscountModal() {
        document.getElementById('discountModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        const currentType = document.getElementById('selectedDiscountType').value || 'none';
        selectDiscountCard(currentType);
    }

    function closeDiscountModal() {
        document.getElementById('discountModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function selectDiscountCard(type) {
        document.getElementById('selectedDiscountType').value = type;

        // Highlight active card
        document.querySelectorAll('.discount-card').forEach(card => {
            card.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-emerald-50/50', 'dark:bg-emerald-950/30');
        });

        if (type !== 'none') {
            const activeCard = document.getElementById('card-' + type);
            if (activeCard) {
                activeCard.classList.add('border-emerald-500', 'text-emerald-600', 'bg-emerald-50/50', 'dark:bg-emerald-950/30');
            }
        }

        // Toggle field groups
        const dynamicContainer = document.getElementById('dynamicInputContainer');
        document.querySelectorAll('.discount-field-group').forEach(group => group.classList.add('hidden'));

        if (type === 'senior' || type === 'pwd') {
            dynamicContainer.classList.remove('hidden');
            document.getElementById('fields-senior-pwd').classList.remove('hidden');
        } else if (type === 'employee') {
            dynamicContainer.classList.remove('hidden');
            document.getElementById('fields-employee').classList.remove('hidden');
            toggleEmployeeMode();
        } else if (type === 'voucher') {
            dynamicContainer.classList.remove('hidden');
            document.getElementById('fields-voucher').classList.remove('hidden');
            toggleVoucherMode();
        } else if (type === 'percentage') {
            dynamicContainer.classList.remove('hidden');
            document.getElementById('fields-percentage').classList.remove('hidden');
        } else if (type === 'amount') {
            dynamicContainer.classList.remove('hidden');
            document.getElementById('fields-amount').classList.remove('hidden');
        } else {
            dynamicContainer.classList.add('hidden');
        }
    }

    function toggleEmployeeMode() {
        const mode = document.getElementById('employeeModeSelect').value;
        const label = document.getElementById('employeeValueLabel');
        const input = document.getElementById('employeeValueInput');
        if (mode === 'percentage') {
            label.innerText = 'Percentage (%)';
            input.placeholder = 'e.g., 10';
            input.max = '100';
        } else {
            label.innerText = 'Amount (₱)';
            input.placeholder = 'e.g., 100.00';
            input.removeAttribute('max');
        }
    }

    function toggleVoucherMode() {
        const mode = document.getElementById('voucherModeSelect').value;
        const label = document.getElementById('voucherValueLabel');
        const input = document.getElementById('voucherValueInput');
        if (mode === 'percentage') {
            label.innerText = 'Percentage (%)';
            input.placeholder = 'e.g., 15';
            input.max = '100';
        } else {
            label.innerText = 'Amount (₱)';
            input.placeholder = 'e.g., 50.00';
            input.removeAttribute('max');
        }
    }

    // Grid & List View Toggle Switcher Logic
    function switchView(viewType) {
        const gridView = document.getElementById('productGridView');
        const listView = document.getElementById('productListView');
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');

        if (viewType === 'list') {
            gridView.classList.add('hidden');
            listView.classList.remove('hidden');
            listBtn.classList.add('bg-white', 'dark:bg-[#10141D]', 'shadow-xs', 'text-emerald-600');
            gridBtn.classList.remove('bg-white', 'dark:bg-[#10141D]', 'shadow-xs', 'text-emerald-600');
            localStorage.setItem('pos_view_mode', 'list');
        } else {
            listView.classList.add('hidden');
            gridView.classList.remove('hidden');
            gridBtn.classList.add('bg-white', 'dark:bg-[#10141D]', 'shadow-xs', 'text-emerald-600');
            listBtn.classList.remove('bg-white', 'dark:bg-[#10141D]', 'shadow-xs', 'text-emerald-600');
            localStorage.setItem('pos_view_mode', 'grid');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('pos_view_mode') || 'grid';
        switchView(savedMode);
    });
</script>
@endpush