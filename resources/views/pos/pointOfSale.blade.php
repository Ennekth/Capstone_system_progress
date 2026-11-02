@extends('layouts.default')

@section('title', 'Point of Sale')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Point of Sale</h1>

    @if (session('error'))
        <div class="mb-6 rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-8">

        <!-- PRODUCTS SECTION -->
        <div class="flex-[2] flex flex-col gap-6">
            
            <!-- Top Navigation: Search (Left) & Clean Category Dropdown (Right) -->
            <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                
                <!-- Search Input Form -->
                <form id="filterForm" method="GET" action="{{ route('pos') }}" class="relative w-full sm:w-80">
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

                <!-- Category Selector Dropdown -->
                <div class="w-full sm:w-auto">
                    <select onchange="window.location.href=this.value"
                            class="w-full sm:w-60 rounded-xl bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                        <option value="{{ route('pos', ['search' => request('search')]) }}">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ route('pos', ['category_id' => $category->id, 'search' => request('search')]) }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Scrollable Product Grid Container -->
            <div class="overflow-y-auto pr-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 pb-4">
                    @forelse ($items as $item)
                        @php
                            $cartQty = isset($cart[$item->id]) ? $cart[$item->id]['quantity'] : 0;
                            $remainingStock = $item->store_quantity - $cartQty;
                        @endphp

                        @if($item->store_quantity > 0)
                            <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-6 flex flex-col justify-between shadow-xs transition-all duration-200">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $item->product_name }}</h3>
                                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1">{{ $item->category->name ?? 'Uncategorized' }}</p>
                                </div>

                                <div class="mt-6 flex items-center justify-between">
                                    <span class="text-lg font-bold text-gray-900 dark:text-slate-100">₱{{ number_format($item->selling_price, 2) }}</span>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 bg-gray-100 dark:bg-[#161B26] px-2.5 py-1 rounded-full border border-gray-200 dark:border-[#1e2536]">Stock: {{ $remainingStock }}</span>
                                </div>

                                <!-- Catalog Card Quantity Control (- / +) -->
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

                                        @if($remainingStock > 0)
                                            <form action="{{ route('pos.add', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="px-3 py-1.5 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-[#1e2536] transition-colors text-xs font-bold cursor-pointer" title="Increase">+</button>
                                            </form>
                                        @else
                                            <button type="button" disabled class="px-3 py-1.5 text-gray-300 dark:text-slate-600 text-xs font-bold cursor-not-allowed">+</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50/50 dark:bg-[#10141D]/50 border-2 border-gray-200/60 dark:border-[#1e2536]/60 rounded-3xl p-6 flex flex-col justify-between opacity-60">
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

                <!-- Custom Pill Pagination Footer (Auto-hides if items <= 24) -->
                @if ($items->hasPages())
                    <div class="px-8 py-5 border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl bg-white dark:bg-[#10141D] flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-slate-400 mt-4">
                        <div>
                            Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $items->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900 dark:text-white">{{ $items->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $items->total() }}</span> results
                        </div>

                        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
                            @if ($items->onFirstPage())
                                <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $items->previousPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                    Previous
                                </a>
                            @endif

                            @foreach ($items->getUrlRange(max(1, $items->currentPage() - 2), min($items->lastPage(), $items->currentPage() + 2)) as $page => $url)
                                @if ($page == $items->currentPage())
                                    <span class="px-4 py-2 text-xs font-bold text-white bg-slate-900 dark:bg-[#1E293B] rounded-full shadow-xs border border-transparent">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            @if ($items->hasMorePages())
                                <a href="{{ $items->nextPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
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
        </div>

        <!-- ORDER SUMMARY / CART SIDEBAR -->
        <div class="flex-1 bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8 flex flex-col justify-between h-fit min-h-[550px]">
            <div>
                <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Order Summary</h2>
                    <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 bg-gray-100 dark:bg-[#161B26] px-3 py-1 rounded-full border border-gray-200 dark:border-[#1e2536]">
                        {{ array_sum(array_column($cart, 'quantity')) }} {{ array_sum(array_column($cart, 'quantity')) === 1 ? 'item' : 'items' }}
                    </span>
                </div>

                @if (count($cart) > 0)
                    <div class="divide-y divide-gray-100 dark:divide-[#1e2536] max-h-[400px] overflow-y-auto pr-1">
                        @foreach ($cart as $id => $item)
                            <div class="py-4 flex flex-col gap-3 group border-b-2 border-gray-100 dark:border-[#1e2536]">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item['product_name'] }}</h3>
                                            <span class="text-sm font-bold text-gray-900 dark:text-slate-100">₱{{ number_format($item['subtotal'], 2) }}</span>
                                        </div>
                                        <p class="text-xs text-gray-400 dark:text-slate-400 mt-0.5">Qty: {{ $item['quantity'] }} × ₱{{ number_format($item['selling_price'], 2) }}</p>
                                        
                                        <!-- Applied Line-Item Discount Badge -->
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

                                <!-- Line Item Discount Configuration Form -->
                                <form action="{{ route('pos.item-discount', $id) }}" method="POST" class="flex flex-wrap items-center gap-2 pt-1 text-xs bg-gray-50 dark:bg-[#161B26] p-2.5 rounded-xl border-2 border-gray-200 dark:border-[#1e2536]">
                                    @csrf
                                    <!-- Discount Unit Type -->
                                    <select name="item_discount_type" class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-lg px-2 py-1 text-gray-600 dark:text-slate-300 text-xs cursor-pointer">
                                        <option value="none" {{ ($item['item_discount_type'] ?? '') == 'none' ? 'selected' : '' }}>No Disc</option>
                                        <option value="amount" {{ ($item['item_discount_type'] ?? '') == 'amount' ? 'selected' : '' }}>Disc (₱)</option>
                                        <option value="percentage" {{ ($item['item_discount_type'] ?? '') == 'percentage' ? 'selected' : '' }}>Disc (%)</option>
                                    </select>

                                    <!-- Discount Scope Mode (Whole vs Per Piece) -->
                                    <select name="item_discount_scope" class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-lg px-2 py-1 text-gray-600 dark:text-slate-300 text-xs cursor-pointer">
                                        <option value="whole" {{ ($item['item_discount_scope'] ?? 'whole') == 'whole' ? 'selected' : '' }}>Whole Row</option>
                                        <option value="per_item" {{ ($item['item_discount_scope'] ?? '') == 'per_item' ? 'selected' : '' }}>Per Piece</option>
                                    </select>

                                    <!-- Value Input -->
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

            <!-- Cart Footer Totals & Checkout -->
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
                    
                    <!-- Clickable Discounts Row -->
                    <div class="flex justify-between items-center text-gray-600 dark:text-slate-400 relative">
                        <button type="button" onclick="openDiscountModal()" class="text-emerald-600 dark:text-emerald-400 hover:underline font-bold text-xs flex items-center gap-1 cursor-pointer z-10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            Discounts ({{ isset($cartDiscount) && $cartDiscount['type'] !== 'none' ? ucfirst(str_replace('_', ' ', $cartDiscount['type'])) : 'Configure' }})
                        </button>
                        <span class="text-rose-600 dark:text-rose-400 font-bold">-₱{{ number_format($totalAllDiscounts, 2) }}</span>
                    </div>

                    <!-- Comprehensive Discount Breakdown Box -->
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

                                    @if($cartDiscount['type'] === 'senior' || $cartDiscount['type'] === 'pwd')
                                        <p class="text-gray-500 dark:text-slate-400 pl-2">• Rate: 20% off (Customer: {{ $cartDiscount['customer_name'] ?? 'N/A' }})</p>
                                    @elseif($cartDiscount['type'] === 'employee')
                                        <p class="text-gray-500 dark:text-slate-400 pl-2">• Rate: {{ ($cartDiscount['mode'] ?? 'percentage') === 'percentage' ? $cartDiscount['value'] . '% off' : '₱' . number_format($cartDiscount['value'], 2) . ' off' }} (Staff: {{ $cartDiscount['employee_name'] ?? 'N/A' }})</p>
                                    @elseif($cartDiscount['type'] === 'voucher')
                                        <p class="text-gray-500 dark:text-slate-400 pl-2">• Code: <span class="uppercase font-bold">{{ $cartDiscount['voucher_code'] ?? 'N/A' }}</span> ({{ ($cartDiscount['mode'] ?? 'percentage') === 'percentage' ? $cartDiscount['value'] . '% off' : '₱' . number_format($cartDiscount['value'], 2) . ' off' }})</p>
                                    @elseif($cartDiscount['type'] === 'percentage')
                                        <p class="text-gray-500 dark:text-slate-400 pl-2">• Rate: {{ $cartDiscount['value'] }}% off cart</p>
                                    @elseif($cartDiscount['type'] === 'amount')
                                        <p class="text-gray-500 dark:text-slate-400 pl-2">• Deduction: ₱{{ number_format($cartDiscount['value'], 2) }} off cart</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="flex justify-between items-center text-lg font-bold text-gray-900 dark:text-white pt-3 border-t-2 border-gray-100 dark:border-[#1e2536]">
                        <span>Total</span>
                        <span class="text-xl">₱{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <!-- Responsive Action Buttons (Clear Order & Proceed to Checkout) -->
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

    <!-- DISCOUNT POPUP MODAL -->
    <div id="discountModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-6 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Apply Cart Discount</h3>
                </div>
                <button type="button" onclick="closeDiscountModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer text-xl font-bold">
                    &times;
                </button>
            </div>

            <!-- Validation Error Alert Banner -->
            <div id="discountErrorAlert" class="hidden mb-6 rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
                Please fill out all required fields before applying the discount.
            </div>

            <!-- Discount Form with JS Validation -->
            <form action="{{ route('pos.discount') }}" method="POST" onsubmit="return validateDiscountForm(event)" class="space-y-6">
                @csrf

                <!-- Government Discounts -->
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-3">Government Discounts</p>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer border-2 border-gray-200 dark:border-[#1e2536] hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl p-4 flex flex-col items-center text-center transition-all bg-gray-50/50 dark:bg-[#161B26]" onclick="toggleDiscountInputs()">
                            <input type="radio" name="discount_type" value="senior" class="sr-only peer">
                            <span class="font-bold text-sm text-gray-900 dark:text-white peer-checked:text-emerald-600">Senior Citizen</span>
                            <span class="text-xs text-gray-400 dark:text-slate-500 mt-1 font-medium">20% off</span>
                        </label>
                        <label class="cursor-pointer border-2 border-gray-200 dark:border-[#1e2536] hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl p-4 flex flex-col items-center text-center transition-all bg-gray-50/50 dark:bg-[#161B26]" onclick="toggleDiscountInputs()">
                            <input type="radio" name="discount_type" value="pwd" class="sr-only peer">
                            <span class="font-bold text-sm text-gray-900 dark:text-white peer-checked:text-emerald-600">PWD</span>
                            <span class="text-xs text-gray-400 dark:text-slate-500 mt-1 font-medium">20% off</span>
                        </label>
                    </div>
                </div>

                <!-- Special & Custom Discretionary Discounts -->
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-3">Special & Custom Discounts</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                        <label class="cursor-pointer border-2 border-gray-200 dark:border-[#1e2536] hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl p-4 flex flex-col items-center text-center transition-all bg-gray-50/50 dark:bg-[#161B26]" onclick="toggleDiscountInputs()">
                            <input type="radio" name="discount_type" value="employee" class="sr-only peer">
                            <span class="font-bold text-sm text-gray-900 dark:text-white peer-checked:text-emerald-600">Employee</span>
                            <span class="text-xs text-gray-400 dark:text-slate-500 mt-1 font-medium">Staff perk</span>
                        </label>
                        <label class="cursor-pointer border-2 border-gray-200 dark:border-[#1e2536] hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl p-4 flex flex-col items-center text-center transition-all bg-gray-50/50 dark:bg-[#161B26]" onclick="toggleDiscountInputs()">
                            <input type="radio" name="discount_type" value="voucher" class="sr-only peer">
                            <span class="font-bold text-sm text-gray-900 dark:text-white peer-checked:text-emerald-600">Voucher</span>
                            <span class="text-xs text-gray-400 dark:text-slate-500 mt-1 font-medium">Promo code</span>
                        </label>
                        <label class="cursor-pointer border-2 border-gray-200 dark:border-[#1e2536] hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl p-4 flex flex-col items-center text-center transition-all bg-gray-50/50 dark:bg-[#161B26]" onclick="toggleDiscountInputs()">
                            <input type="radio" name="discount_type" value="percentage" class="sr-only peer">
                            <span class="font-bold text-sm text-gray-900 dark:text-white peer-checked:text-emerald-600">Percentage</span>
                            <span class="text-xs text-gray-400 dark:text-slate-500 mt-1 font-medium">% off total</span>
                        </label>
                        <label class="cursor-pointer border-2 border-gray-200 dark:border-[#1e2536] hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl p-4 flex flex-col items-center text-center transition-all bg-gray-50/50 dark:bg-[#161B26]" onclick="toggleDiscountInputs()">
                            <input type="radio" name="discount_type" value="amount" class="sr-only peer">
                            <span class="font-bold text-sm text-gray-900 dark:text-white peer-checked:text-emerald-600">Amount</span>
                            <span class="text-xs text-gray-400 dark:text-slate-500 mt-1 font-medium">Fixed peso</span>
                        </label>
                    </div>

                    <!-- Dynamic Input: Senior Citizen & PWD Information Form -->
                    <div id="idInfoInputSection" class="hidden space-y-4 p-6 rounded-2xl bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536]">
                        <h4 id="idFormTitle" class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Customer Information</h4>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Customer Name <span class="text-rose-500">*</span></label>
                            <input type="text" id="inputCustomerName" name="customer_name" placeholder="Full name as on ID"
                                   class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                        </div>
                        <div>
                            <label id="idNumberLabel" class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">ID Number <span class="text-rose-500">*</span></label>
                            <input type="text" id="inputIDNumber" name="id_number" placeholder="Enter ID number"
                                   class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">TIN Number <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="text" name="tin_number" placeholder="Enter TIN number"
                                   class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                        </div>
                    </div>

                    <!-- Dynamic Input: Employee Form with Mode Selection -->
                    <div id="employeeInputSection" class="hidden space-y-4 p-6 rounded-2xl bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536]">
                        <h4 class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Employee Verification & Discount</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Employee Name <span class="text-rose-500">*</span></label>
                                <input type="text" id="inputEmpName" name="employee_name" placeholder="Full name"
                                       class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Employee ID <span class="text-rose-500">*</span></label>
                                <input type="text" id="inputEmpId" name="employee_id" placeholder="ID Number"
                                       class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                            </div>
                        </div>
                        <div class="pt-4 border-t-2 border-gray-200 dark:border-[#1e2536]">
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Discount Type</label>
                            <div class="flex items-center gap-6 mb-4">
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700 dark:text-slate-300">
                                    <input type="radio" name="employee_mode" value="percentage" checked onclick="toggleEmployeeMode()" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4"> Percentage (%)
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700 dark:text-slate-300">
                                    <input type="radio" name="employee_mode" value="amount" onclick="toggleEmployeeMode()" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4"> Fixed Amount (₱)
                                </label>
                            </div>
                            <div id="employeePercentageField">
                                <input type="number" step="0.01" min="1" max="100" id="inputEmpPct" name="employee_percentage" placeholder="Enter percentage (e.g. 10)"
                                       class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                            </div>
                            <div id="employeeAmountField" class="hidden">
                                <input type="number" step="0.01" min="0" id="inputEmpAmt" name="employee_amount" placeholder="Enter fixed amount (e.g. 50)"
                                       class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Input: Voucher Form with Mode Selection -->
                    <div id="voucherInputSection" class="hidden space-y-4 p-6 rounded-2xl bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536]">
                        <h4 class="text-xs font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Voucher / Promo Code</h4>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Voucher Code <span class="text-rose-500">*</span></label>
                            <input type="text" id="inputVoucherCode" name="voucher_code" placeholder="e.g. HYDROTECH50"
                                   class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 uppercase focus:outline-none focus:border-slate-400">
                        </div>
                        <div class="pt-4 border-t-2 border-gray-200 dark:border-[#1e2536]">
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Discount Type</label>
                            <div class="flex items-center gap-6 mb-4">
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700 dark:text-slate-300">
                                    <input type="radio" name="voucher_mode" value="percentage" checked onclick="toggleVoucherMode()" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4"> Percentage (%)
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-gray-700 dark:text-slate-300">
                                    <input type="radio" name="voucher_mode" value="amount" onclick="toggleVoucherMode()" class="text-emerald-600 focus:ring-emerald-500 w-4 h-4"> Fixed Amount (₱)
                                </label>
                            </div>
                            <div id="voucherPercentageField">
                                <input type="number" step="0.01" min="1" max="100" id="inputVoucherPct" name="voucher_percentage" placeholder="Enter percentage (e.g. 20)"
                                       class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                            </div>
                            <div id="voucherAmountField" class="hidden">
                                <input type="number" step="0.01" min="0" id="inputVoucherAmt" name="voucher_amount" placeholder="Enter fixed amount (e.g. 100)"
                                       class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Input: Percentage Option -->
                    <div id="percentageInputSection" class="hidden space-y-3 p-6 rounded-2xl bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536]">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Percentage Discount <span class="text-rose-500">*</span></label>
                            <p class="text-xs text-gray-400 dark:text-slate-500">Enter a value between 1 and 100.</p>
                        </div>
                        <input type="number" step="0.01" min="1" max="100" id="inputPercentageValue" name="percentage_value" placeholder="Enter percentage"
                               class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <!-- Dynamic Input: Amount Option -->
                    <div id="amountInputSection" class="hidden space-y-3 p-6 rounded-2xl bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536]">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Amount Discount <span class="text-rose-500">*</span></label>
                            <p class="text-xs text-gray-400 dark:text-slate-500">Enter a fixed peso amount to deduct.</p>
                        </div>
                        <input type="number" step="0.01" min="0" id="inputAmountValue" name="amount_value" placeholder="Enter amount"
                               class="w-full rounded-xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <!-- Modal Footer Buttons -->
                <div class="flex items-center justify-between pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                    <!-- Remove Discount Option -->
                    <button type="submit" name="discount_type" value="none" class="text-xs font-semibold text-rose-600 dark:text-rose-400 hover:underline cursor-pointer">
                        Remove Discount
                    </button>
                    <div class="flex items-center space-x-3">
                        <button type="button" onclick="closeDiscountModal()"
                                class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                            Apply Discount
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($completedTransaction))
    <!-- RECEIPT POPUP MODAL -->
    <div id="receiptModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl p-8 w-full max-w-sm shadow-2xl text-left relative" onclick="event.stopPropagation()">
            
            <!-- Close Button -->
            <button onclick="document.getElementById('receiptModal').remove()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 dark:hover:text-white print:hidden cursor-pointer text-xl font-bold">
                &times;
            </button>

            <!-- RECEIPT CONTENT -->
            <div id="printableReceipt" class="font-mono text-[11px] leading-tight text-gray-900 dark:text-gray-100 space-y-3 pt-2">
                
                <!-- Business Header -->
                <div class="text-center pb-3 border-b border-dashed border-gray-300 dark:border-gray-700">
                    <h1 class="text-xs font-bold tracking-wider uppercase">STEVEN HYDROTECH EXPONENT</h1>
                    <p class="text-[9px] text-gray-500 uppercase tracking-widest mt-0.5">Water Treatment & Supply Services</p>
                </div>

                <!-- Metadata -->
                <div class="py-2 border-b border-dashed border-gray-300 dark:border-gray-700 space-y-1 text-[10px] text-gray-600 dark:text-gray-400">
                    <div class="flex justify-between"><span>Date:</span> <span class="font-medium">{{ \Carbon\Carbon::parse($completedTransaction->created_at)->format('M d, Y - h:i A') }}</span></div>
                    <div class="flex justify-between"><span>Order #:</span> <span class="font-medium">{{ $completedTransaction->transaction_id }}</span></div>
                    <div class="flex justify-between"><span>Cashier:</span> <span class="font-medium">{{ $completedTransaction->user->name ?? 'System Administrator' }}</span></div>
                    @if(!empty($completedTransaction->customer_name))
                        <div class="flex justify-between"><span>Customer:</span> <span class="font-medium">{{ $completedTransaction->customer_name }}</span></div>
                    @endif
                </div>

                <!-- Items & Item Discounts -->
                <div class="py-2 border-b border-dashed border-gray-300 dark:border-gray-700 space-y-2">
                    @php
                        $totalItemCount = 0;
                        $rawSubtotalReceipt = 0;
                        $totalItemDiscountsReceipt = 0;
                    @endphp
                    @foreach($completedTransaction->transactionItems as $item)
                        @php
                            $totalItemCount += $item->quantity;
                            $unitPrice = $item->selling_price ?? $item->price ?? 0;
                            $baseItemTotal = $unitPrice * $item->quantity;
                            $rawSubtotalReceipt += $baseItemTotal;
                            
                            $itemSubtotal = $item->subtotal ?? $baseItemTotal;
                            $itemDiscount = max(0, $baseItemTotal - $itemSubtotal);
                            $totalItemDiscountsReceipt += $itemDiscount;

                            $discType = $item->item_discount_type ?? 'none';
                            $discScope = $item->item_discount_scope ?? 'whole';
                            $scopeLabel = (in_array(strtolower($discScope), ['per_item', 'unit', 'piece'])) ? 'Per Piece' : 'Whole Row';
                        @endphp
                        <div class="space-y-0.5">
                            <div class="flex justify-between items-start font-bold">
                                <span>{{ $item->quantity }}x {{ $item->product->product_name ?? $item->product_name ?? 'Item' }}</span>
                                <span>₱{{ number_format($itemSubtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-[10px] text-gray-500 pl-2">
                                <span>@ ₱{{ number_format($unitPrice, 2) }}/unit</span>
                            </div>
                            @if(strtolower($discType) !== 'none' && $discType != '' && $itemDiscount > 0)
                                <div class="flex justify-between text-[10px] text-rose-600 pl-2">
                                    <span>Item Disc: -₱{{ number_format($itemDiscount, 2) }} ({{ $scopeLabel }})</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Totals & Cart Discounts -->
                <div class="pt-2 space-y-1 text-[10px] text-gray-700 dark:text-gray-300">
                    <div class="flex justify-between"><span>Items:</span> <span class="font-medium">{{ $totalItemCount }}</span></div>
                    <div class="flex justify-between"><span>Subtotal:</span> <span class="font-medium">₱{{ number_format($rawSubtotalReceipt, 2) }}</span></div>
                    
                    @if($totalItemDiscountsReceipt > 0)
                        <div class="flex justify-between text-rose-600"><span>Item Discounts:</span> <span class="font-medium">-₱{{ number_format($totalItemDiscountsReceipt, 2) }}</span></div>
                    @endif

                    @php
                        $cartDiscountAmount = (float)($completedTransaction->cart_discount_value ?? 0);
                        
                        $dbCartDiscType = strtolower(trim($completedTransaction->cart_discount_type ?? 'none'));
                        $cartDiscLabel = 'Cart Discount';

                        if ($dbCartDiscType !== 'none' && $dbCartDiscType !== '') {
                            if ($dbCartDiscType === 'senior') {
                                $cartDiscLabel = 'Cart Discount (Senior Citizen)';
                            } elseif ($dbCartDiscType === 'pwd') {
                                $cartDiscLabel = 'Cart Discount (PWD)';
                            } elseif ($dbCartDiscType === 'employee') {
                                $cartDiscLabel = 'Cart Discount (Employee)';
                            } elseif ($dbCartDiscType === 'voucher') {
                                $cartDiscLabel = 'Cart Discount (Voucher)';
                            } elseif ($dbCartDiscType === 'percentage') {
                                $cartDiscLabel = 'Cart Discount (Percentage)';
                            } elseif ($dbCartDiscType === 'amount') {
                                $cartDiscLabel = 'Cart Discount (Amount)';
                            } else {
                                $cartDiscLabel = 'Cart Discount (' . ucwords($dbCartDiscType) . ')';
                            }
                        }
                    @endphp

                    @if($cartDiscountAmount > 0)
                        <div class="flex justify-between text-rose-600">
                            <span>{{ $cartDiscLabel }}:</span> 
                            <span class="font-medium">-₱{{ number_format($cartDiscountAmount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between font-bold text-xs pt-1.5 border-t border-dashed border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white">
                        <span>Total Amount:</span> <span>₱{{ number_format($completedTransaction->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between pt-1"><span>Payment:</span> <span class="font-medium">₱{{ number_format($completedTransaction->amount_paid, 2) }}</span></div>
                    <div class="flex justify-between"><span>Change:</span> <span class="font-medium">₱{{ number_format($completedTransaction->amount_change ?? ($completedTransaction->amount_paid - $completedTransaction->total_amount), 2) }}</span></div>
                </div>

                <!-- Footer -->
                <div class="text-center pt-3 text-[9px] text-gray-400 border-t border-dashed border-gray-300 dark:border-gray-700">
                    <p>Thank you for your purchase!</p>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="flex gap-3 pt-6 print:hidden">
                <button onclick="window.print()" class="w-1/2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-xl text-xs shadow-sm transition cursor-pointer">
                    Print Receipt
                </button>
                <button onclick="window.location.href='{{ route('pos') }}'" class="w-1/2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold py-2.5 rounded-xl text-xs transition cursor-pointer">
                    New Sale
                </button>
            </div>

        </div>
    </div>

    <!-- Strict Thermal Printer & Preview Stylesheet -->
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            html, body {
                width: 100%;
                height: 100%;
                background: white !important;
                color: black !important;
                margin: 0 !important;
                padding: 0 !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
            }
            body * {
                visibility: hidden !important;
            }
            #printableReceipt, #printableReceipt * {
                visibility: visible !important;
            }
            #printableReceipt {
                position: relative !important;
                display: block !important;
                width: 78mm !important;
                max-width: 78mm !important;
                margin: auto !important;
                padding: 6mm !important;
                background: white !important;
                color: black !important;
                border: none !important;
                box-shadow: none !important;
                font-size: 13px !important;
                line-height: 1.4 !important;
            }
            #printableReceipt h1 {
                font-size: 15px !important;
            }
        }
    </style>
@endif

@endsection

@push('scripts')
<script>
    function openClearModal() {
        document.getElementById('clearModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeClearModal() {
        document.getElementById('clearModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openDiscountModal() {
        document.getElementById('discountErrorAlert').classList.add('hidden');
        document.getElementById('discountModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeDiscountModal() {
        document.getElementById('discountModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function toggleDiscountInputs() {
        document.getElementById('discountErrorAlert').classList.add('hidden');
        const selectedType = document.querySelector('input[name="discount_type"]:checked')?.value;
        
        const idInfoSection = document.getElementById('idInfoInputSection');
        const employeeSection = document.getElementById('employeeInputSection');
        const voucherSection = document.getElementById('voucherInputSection');
        const percentageSection = document.getElementById('percentageInputSection');
        const amountSection = document.getElementById('amountInputSection');

        idInfoSection.classList.add('hidden');
        employeeSection.classList.add('hidden');
        voucherSection.classList.add('hidden');
        percentageSection.classList.add('hidden');
        amountSection.classList.add('hidden');

        if (selectedType === 'senior' || selectedType === 'pwd') {
            idInfoSection.classList.remove('hidden');
            const idFormTitle = document.getElementById('idFormTitle');
            const idNumberLabel = document.getElementById('idNumberLabel');
            
            if (selectedType === 'senior') {
                idFormTitle.textContent = 'Senior Citizen Information';
                idNumberLabel.innerHTML = 'Senior Citizen ID Number <span class="text-rose-500">*</span>';
            } else {
                idFormTitle.textContent = 'PWD Information';
                idNumberLabel.innerHTML = 'PWD ID Number <span class="text-rose-500">*</span>';
            }
        } else if (selectedType === 'employee') {
            employeeSection.classList.remove('hidden');
        } else if (selectedType === 'voucher') {
            voucherSection.classList.remove('hidden');
        } else if (selectedType === 'percentage') {
            percentageSection.classList.remove('hidden');
        } else if (selectedType === 'amount') {
            amountSection.classList.remove('hidden');
        }
    }

    function toggleEmployeeMode() {
        const mode = document.querySelector('input[name="employee_mode"]:checked')?.value;
        const percentageField = document.getElementById('employeePercentageField');
        const amountField = document.getElementById('employeeAmountField');

        if (mode === 'percentage') {
            percentageField.classList.remove('hidden');
            amountField.classList.add('hidden');
        } else {
            percentageField.classList.add('hidden');
            amountField.classList.remove('hidden');
        }
    }

    function toggleVoucherMode() {
        const mode = document.querySelector('input[name="voucher_mode"]:checked')?.value;
        const percentageField = document.getElementById('voucherPercentageField');
        const amountField = document.getElementById('voucherAmountField');

        if (mode === 'percentage') {
            percentageField.classList.remove('hidden');
            amountField.classList.add('hidden');
        } else {
            percentageField.classList.add('hidden');
            amountField.classList.remove('hidden');
        }
    }

    function validateDiscountForm(event) {
        const discountTypeRadio = document.querySelector('input[name="discount_type"]:checked');
        const errorAlert = document.getElementById('discountErrorAlert');

        if (!discountTypeRadio) {
            errorAlert.textContent = 'Please select a discount type.';
            errorAlert.classList.remove('hidden');
            event.preventDefault();
            return false;
        }

        const type = discountTypeRadio.value;
        if (type === 'none') {
            return true;
        }

        if (type === 'senior' || type === 'pwd') {
            const name = document.getElementById('inputCustomerName').value.trim();
            const idNum = document.getElementById('inputIDNumber').value.trim();
            if (!name || !idNum) {
                errorAlert.textContent = 'Please provide both Customer Name and ID Number.';
                errorAlert.classList.remove('hidden');
                event.preventDefault();
                return false;
            }
        } else if (type === 'employee') {
            const empName = document.getElementById('inputEmpName').value.trim();
            const empId = document.getElementById('inputEmpId').value.trim();
            const mode = document.querySelector('input[name="employee_mode"]:checked')?.value;
            const pct = document.getElementById('inputEmpPct').value.trim();
            const amt = document.getElementById('inputEmpAmt').value.trim();

            if (!empName || !empId || (mode === 'percentage' && !pct) || (mode === 'amount' && !amt)) {
                errorAlert.textContent = 'Please fill out all employee verification fields and discount values.';
                errorAlert.classList.remove('hidden');
                event.preventDefault();
                return false;
            }
        } else if (type === 'voucher') {
            const code = document.getElementById('inputVoucherCode').value.trim();
            const mode = document.querySelector('input[name="voucher_mode"]:checked')?.value;
            const pct = document.getElementById('inputVoucherPct').value.trim();
            const amt = document.getElementById('inputVoucherAmt').value.trim();

            if (!code || (mode === 'percentage' && !pct) || (mode === 'amount' && !amt)) {
                errorAlert.textContent = 'Please provide a Voucher Code and discount value.';
                errorAlert.classList.remove('hidden');
                event.preventDefault();
                return false;
            }
        } else if (type === 'percentage') {
            const val = document.getElementById('inputPercentageValue').value.trim();
            if (!val) {
                errorAlert.textContent = 'Please enter a percentage value.';
                errorAlert.classList.remove('hidden');
                event.preventDefault();
                return false;
            }
        } else if (type === 'amount') {
            const val = document.getElementById('inputAmountValue').value.trim();
            if (!val) {
                errorAlert.textContent = 'Please enter a fixed peso amount.';
                errorAlert.classList.remove('hidden');
                event.preventDefault();
                return false;
            }
        }

        errorAlert.classList.add('hidden');
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('filterForm');
        let debounceTimer;

        if (searchInput) {
            const savedCursorPos = sessionStorage.getItem('posSearchCursorPos');
            if (savedCursorPos !== null) {
                searchInput.focus();
                searchInput.setSelectionRange(parseInt(savedCursorPos), parseInt(savedCursorPos));
                sessionStorage.removeItem('posSearchCursorPos');
            } else if (searchInput.value.length > 0) {
                searchInput.focus();
                searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                sessionStorage.setItem('posSearchCursorPos', searchInput.selectionStart);

                debounceTimer = setTimeout(function () {
                    filterForm.submit();
                }, 300);
            });
        }
    });
</script>
@endpush