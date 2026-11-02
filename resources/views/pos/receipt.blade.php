@extends('layouts.default')

@section('title', 'Receipt')

@section('content')

    <div class="flex justify-center items-center min-h-[70vh] py-6">
        <div class="w-full flex flex-col items-center">
            
            <!-- Success Checkmark Banner & Title (Hidden on Print) -->
            <div class="text-center mb-6 print:hidden">
                <div class="flex justify-center mb-2">
                    <span class="p-2 rounded-full bg-emerald-50 dark:bg-emerald-950/65 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </span>
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Order Created!</h2>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">
                    Transaction #{{ $transaction->transaction_id ?? $transaction->id }} has been successfully created.
                </p>
            </div>

            <!-- ON-SCREEN THERMAL RECEIPT PREVIEW CONTAINER -->
            <div id="printableReceipt" class="w-full max-w-[320px] bg-white text-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-left shadow-md font-mono text-[11px] leading-tight">
                
                <!-- Business Name Header -->
                <div class="text-center pb-3 border-b border-dashed border-gray-300">
                    <h1 class="text-xs font-bold tracking-wider uppercase">
                        STEVEN HYDROTECH EXPONENT
                    </h1>
                    <p class="text-[9px] text-gray-500 uppercase tracking-widest mt-0.5">
                        Water Treatment & Supply Services
                    </p>
                </div>

                <!-- Transaction Metadata -->
                <div class="py-2.5 border-b border-dashed border-gray-300 space-y-1 text-[10px] text-gray-600">
                    <div class="flex justify-between">
                        <span>Date:</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y - h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Order #:</span>
                        <span class="font-medium">{{ $transaction->transaction_id ?? 'POS-' . $transaction->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Cashier:</span>
                        <span class="font-medium">{{ $transaction->user->name ?? 'System Administrator' }}</span>
                    </div>
                    @if(!empty($transaction->customer_name))
                        <div class="flex justify-between">
                            <span>Customer:</span>
                            <span class="font-medium">{{ $transaction->customer_name }}</span>
                        </div>
                    @endif
                </div>

                <!-- ITEMS LIST -->
                <div class="py-2.5 border-b border-dashed border-gray-300 space-y-2.5">
                    @php
                        $receiptItems = $transaction->items ?? $transaction->transactionItems ?? [];
                        $totalItemCount = 0;
                        $rawSubtotal = 0;
                        $totalItemDiscounts = 0;
                    @endphp

                    @forelse ($receiptItems as $item)
                        @php
                            $totalItemCount += $item->quantity;
                            $unitPrice = $item->selling_price ?? $item->price ?? 0;
                            $baseItemTotal = $unitPrice * $item->quantity;
                            $rawSubtotal += $baseItemTotal;
                            
                            $itemSubtotal = $item->subtotal ?? $baseItemTotal;
                            $itemDiscount = max(0, $baseItemTotal - $itemSubtotal);
                            $totalItemDiscounts += $itemDiscount;

                            $discType = $item->item_discount_type ?? 'none';
                            $discScope = $item->item_discount_scope ?? 'whole';
                            $scopeLabel = (in_array(strtolower($discScope), ['per_item', 'unit', 'piece'])) ? 'Unit' : 'Whole Row';
                        @endphp
                        <div class="space-y-0.5">
                            <div class="flex justify-between items-start font-bold">
                                <span>{{ $item->quantity }}x {{ $item->product->product_name ?? $item->product_name ?? 'Item' }}</span>
                                <span>₱{{ number_format($baseItemTotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-[10px] text-gray-500 pl-2">
                                <span>@ ₱{{ number_format($unitPrice, 2) }}/unit</span>
                            </div>
                            @if(strtolower($discType) !== 'none' && $discType != '' && $itemDiscount > 0)
                                <div class="flex justify-between text-[10px] text-rose-600 pl-2">
                                    <span>Item Disc ({{ $scopeLabel }}):</span>
                                    <span>-₱{{ number_format($itemDiscount, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-2">No items found for this transaction.</p>
                    @endforelse
                </div>

                <!-- SUMMARY TOTALS -->
                <div class="pt-2.5 space-y-1 text-[10px] text-gray-700">
                    <div class="flex justify-between">
                        <span>Items:</span>
                        <span class="font-medium">{{ $totalItemCount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span class="font-medium">₱{{ number_format($rawSubtotal, 2) }}</span>
                    </div>

                    @if($totalItemDiscounts > 0)
                        <div class="flex justify-between text-rose-600">
                            <span>Item Discounts:</span>
                            <span class="font-medium">-₱{{ number_format($totalItemDiscounts, 2) }}</span>
                        </div>
                    @endif

                    @php
                        $cartDiscountAmount = (float)($transaction->cart_discount_value ?? 0);
                        if ($cartDiscountAmount <= 0) {
                            $subAfterItemDisc = $rawSubtotal - $totalItemDiscounts;
                            $cartDiscountAmount = max(0, $subAfterItemDisc - $transaction->total_amount);
                        }
                        
                        $dbCartDiscType = strtolower(trim($transaction->cart_discount_type ?? 'none'));
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

                    <div class="flex justify-between font-bold text-xs pt-1.5 border-t border-dashed border-gray-300 text-gray-900">
                        <span>Total Amount:</span>
                        <span>₱{{ number_format($transaction->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span>Payment:</span>
                        <span class="font-medium">₱{{ number_format($transaction->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Change:</span>
                        <span class="font-medium">₱{{ number_format($transaction->amount_change ?? ($transaction->amount_paid - $transaction->total_amount), 2) }}</span>
                    </div>
                </div>

                <!-- Footer Note -->
                <div class="text-center pt-4 pb-1 text-[9px] text-gray-400 border-t border-dashed border-gray-300 mt-3">
                    <p>Thank you for your purchase!</p>
                </div>

            </div>

            <!-- ACTIONS (Hidden when printing) -->
            <div class="flex items-center justify-center gap-3 pt-6 w-full max-w-[320px] print:hidden">
                <!-- New Sale -->
                <a href="{{ route('pos') }}"
                   class="w-1/2 text-center bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white font-semibold py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-sm text-xs">
                    New Sale
                </a>

                <!-- Print -->
                <button type="button" onclick="window.print()"
                        class="w-1/2 bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-sm cursor-pointer">
                    Print Receipt
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
@endsection