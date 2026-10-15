@extends('layouts.default')

@section('title', 'Receipt')

@section('content')

    <div class="max-w-lg mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-8">

        <div class="text-center mb-6">
            <h1 class="text-xl font-bold">Receipt</h1>
            <p class="text-sm text-gray-500">Transaction #{{ $transaction->transaction_id }}</p>
            <p class="text-xs text-gray-400">{{ $transaction->created_at->format('M d, Y - h:i A') }}</p>
        </div>

        <div class="divide-y divide-gray-100 border-t border-b border-gray-200">
            @foreach ($transaction->transactionItems as $item)
                <div class="flex justify-between py-3 text-sm">
                    <div>
                        <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                        <p class="text-gray-500">{{ $item->quantity }} × ₱{{ number_format($item->selling_price, 2) }}</p>
                    </div>
                    <p class="font-medium text-gray-900">₱{{ number_format($item->subtotal, 2) }}</p>
                </div>
            @endforeach
        </div>

        <div class="space-y-2 mt-4 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Total</span>
                <span class="font-bold text-gray-900">₱{{ number_format($transaction->total_amount, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Amount Paid</span>
                <span class="text-gray-900">₱{{ number_format($transaction->amount_paid, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Change</span>
                <span class="text-gray-900">₱{{ number_format($transaction->amount_change, 2) }}</span>
            </div>
        </div>

        <div class="flex gap-3 mt-8">
            <a href="{{ route('pos') }}"
               class="flex-1 text-center bg-gray-900 text-white font-medium py-2.5 rounded-lg hover:bg-gray-800">
                New Sale
            </a>
            <button onclick="window.print()"
                    class="flex-1 bg-gray-100 text-gray-700 font-medium py-2.5 rounded-lg hover:bg-gray-200">
                Print
            </button>
        </div>

    </div>

@endsection