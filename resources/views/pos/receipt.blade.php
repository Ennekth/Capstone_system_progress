@extends('layouts.default')

@section('title', 'Receipt')

@section('content')

    <div class="flex justify-center items-center min-h-[70vh]">
        <div class="w-full max-w-md bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm p-6 text-center">
            
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Receipt</h2>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                Transaction #{{ $transaction->transaction_number ?? $transaction->id }}
            </p>
            <p class="text-xs text-gray-400 dark:text-slate-500 mb-6">
                {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y - h:i A') }}
            </p>

            <!-- ITEMS LIST -->
            <div class="border-t border-b border-gray-100 dark:border-[#1e2536] py-4 my-4 divide-y divide-gray-100 dark:divide-[#1e2536] text-left">
                @php
                    $receiptItems = $transaction->items ?? $transaction->transactionItems ?? [];
                @endphp
                @forelse ($receiptItems as $item)
                    <div class="py-2.5 flex justify-between items-center text-sm">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $item->product->product_name ?? $item->product_name ?? 'Item' }}</p>
                            <p class="text-xs text-gray-500 dark:text-slate-400">{{ $item->quantity }} × ₱{{ number_format($item->unit_price ?? $item->price, 2) }}</p>
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-slate-200">
                            ₱{{ number_format(($item->quantity) * ($item->unit_price ?? $item->price), 2) }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-2">No items found for this transaction.</p>
                @endforelse
            </div>

            <!-- TOTALS -->
            <div class="space-y-2 text-sm text-gray-600 dark:text-slate-300 text-left mb-6">
                <div class="flex justify-between font-bold text-base text-gray-900 dark:text-white">
                    <span>Total</span>
                    <span>₱{{ number_format($transaction->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Amount Paid</span>
                    <span>₱{{ number_format($transaction->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between font-medium text-gray-900 dark:text-slate-200">
                    <span>Change</span>
                    <span>₱{{ number_format($transaction->change_amount ?? ($transaction->amount_paid - $transaction->total_amount), 2) }}</span>
                </div>
            </div>

            <!-- ACTIONS (Hidden when printing via print:hidden) -->
            <div class="grid grid-cols-2 gap-3 pt-2 print:hidden">
                <!-- New Sale -->
                <a href="{{ route('pos') }}"
                   class="block w-full text-center bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white font-semibold py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-sm">
                    New Sale
                </a>

                <!-- Print -->
                <button type="button" onclick="window.print()"
                        class="w-full bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-sm cursor-pointer">
                    Print
                </button>
            </div>

        </div>
    </div>

@endsection
```[cite: 9]

<FollowUp label="How does the print preview look now without the buttons?" query="How does the print preview look now without the buttons?"/>