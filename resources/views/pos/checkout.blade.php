@extends('layouts.default')

@section('title', 'Checkout')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Checkout</h1>

    @if (session('error'))
        <div class="mb-6 rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col xl:flex-row gap-8">

        <!-- ORDER SUMMARY SECTION -->
        <div class="flex-[2] bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Order Summary</h2>
                    <span class="text-xs font-semibold text-gray-500 dark:text-slate-400 bg-gray-100 dark:bg-[#161B26] px-3 py-1 rounded-full border border-gray-200 dark:border-[#1e2536]">
                        {{ array_sum(array_column($cart, 'quantity')) }} {{ array_sum(array_column($cart, 'quantity')) === 1 ? 'item' : 'items' }}
                    </span>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase border-b-2 border-gray-100 dark:border-[#1e2536]">
                                <th class="pb-4">Product</th>
                                <th class="pb-4 text-center">Price</th>
                                <th class="pb-4 text-center">Qty</th>
                                <th class="pb-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                            @foreach ($cart as $id => $item)
                                <tr class="py-4">
                                    <td class="py-4 font-semibold text-gray-900 dark:text-white">
                                        <div>{{ $item['product_name'] }}</div>
                                        <div class="text-[11px] text-gray-400 font-normal space-x-1.5 mt-0.5">
                                            <span>SKU: <strong class="text-slate-600 dark:text-slate-300">{{ $item['product_sku'] ?? '—' }}</strong></span>
                                            <span>•</span>
                                            <span>Item SKU: <strong class="text-slate-600 dark:text-slate-300">{{ $item['item_sku'] ?? '—' }}</strong></span>
                                        </div>
                                        @if(($item['item_discount_type'] ?? 'none') !== 'none' && ($item['item_discount_amount'] ?? 0) > 0)
                                            <div class="text-xs font-normal text-rose-600 dark:text-rose-400 mt-0.5">
                                                Item Disc: -₱{{ number_format($item['item_discount_amount'], 2) }} ({{ (($item['item_discount_scope'] ?? 'whole') === 'per_item') ? 'Per Piece' : 'Whole Row' }})
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 text-center text-gray-600 dark:text-slate-300">₱{{ number_format($item['selling_price'], 2) }}</td>
                                    <td class="py-4 text-center text-gray-600 dark:text-slate-300 font-medium">{{ $item['quantity'] }}</td>
                                    <td class="py-4 text-right font-bold text-gray-900 dark:text-slate-100">₱{{ number_format($item['subtotal'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals & Breakdown Box -->
            <div class="pt-6 border-t-2 border-gray-200 dark:border-[#1e2536] mt-6 space-y-3">
                <div class="flex justify-between text-sm text-gray-600 dark:text-slate-400 font-medium">
                    <span>Subtotal</span>
                    <span>₱{{ number_format($subtotal, 2) }}</span>
                </div>

                <div class="flex justify-between items-center text-sm text-gray-600 dark:text-slate-400">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                        Discounts ({{ isset($cartDiscount) && $cartDiscount['type'] !== 'none' ? ucfirst(str_replace('_', ' ', $cartDiscount['type'])) : 'None' }})
                    </span>
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

                        <!-- Line Item Discounts Stack -->
                        @if(count($discountedItemsList) > 0)
                            <div class="space-y-1.5">
                                <p class="font-bold text-gray-700 dark:text-slate-300 text-[11px] uppercase">Item Discounts:</p>
                                @foreach($discountedItemsList as $discItem)
                                    @php
                                        $scopeLabel = (($discItem['item_discount_scope'] ?? 'whole') === 'per_item') ? 'Per Piece' : 'Whole Row';
                                    @endphp
                                    <div class="flex justify-between items-center text-gray-600 dark:text-slate-400 pl-2 text-[11px]">
                                        <span class="truncate max-w-[240px]">• {{ $discItem['product_name'] }} <span class="text-[10px] text-gray-400">({{ $scopeLabel }})</span></span>
                                        <span class="text-rose-600 dark:text-rose-400 font-semibold">-₱{{ number_format($discItem['item_discount_amount'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Cart Discount -->
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
        </div>

        <!-- PAYMENT SECTION -->
        <div class="flex-1 bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8 flex flex-col justify-between h-fit">
            <form action="{{ route('checkout.process') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-4 border-b-2 border-gray-100 dark:border-[#1e2536]">Payment</h2>
                    
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Amount Paid <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="{{ $total }}" name="amount_paid" value="{{ old('amount_paid', $total) }}" placeholder="Must be at least ₱{{ number_format($total, 2) }}" required
                           class="w-full rounded-xl bg-gray-50 dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    <p class="text-xs text-gray-400 dark:text-slate-500 mt-1.5 font-medium">Must be at least ₱{{ number_format($total, 2) }}</p>
                </div>

                <div class="space-y-3 pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                    <button type="submit"
                            class="w-full text-center bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer text-sm">
                        Confirm Payment
                    </button>
                    <a href="{{ route('pos') }}"
                       class="block w-full text-center bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-300 ease-in-out shadow-xs text-sm">
                        Back to Cart
                    </a>
                </div>
            </form>
        </div>

    </div>
@endsection