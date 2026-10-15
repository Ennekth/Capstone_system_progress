@extends('layouts.default')

@section('title', 'Checkout')

@section('content')

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- ORDER SUMMARY -->
        <div class="flex-[2] bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold mb-4">Order Summary</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($cart as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item['product_name'] }}</td>
                                <td class="px-4 py-3 text-gray-600">₱{{ number_format($item['selling_price'], 2) }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $item['quantity'] }}</td>
                                <td class="px-4 py-3 text-right text-gray-900 font-medium">
                                    ₱{{ number_format($item['selling_price'] * $item['quantity'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
                <span class="text-lg font-bold text-gray-900">Total</span>
                <span class="text-2xl font-bold text-gray-900">₱{{ number_format($total, 2) }}</span>
            </div>
        </div>

        <!-- PAYMENT -->
        <div class="flex-1 bg-white border border-gray-200 rounded-xl shadow-sm p-6 h-fit">
            <h2 class="text-lg font-bold mb-4">Payment</h2>

            <form method="POST" action="{{ route('checkout.process') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">
                        Amount Paid
                    </label>
                    <input type="number" step="0.01" min="{{ $total }}" name="amount_paid" id="amount_paid"
                           value="{{ old('amount_paid') }}" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    <p class="text-xs text-gray-400 mt-1">Must be at least ₱{{ number_format($total, 2) }}</p>
                </div>

                <button type="submit"
                        class="w-full bg-green-600 text-white font-medium py-3 rounded-lg hover:bg-green-700">
                    Confirm Payment
                </button>

                <a href="{{ route('pos') }}"
                   class="block w-full text-center text-sm font-medium text-gray-600 hover:bg-gray-100 py-2 rounded-lg">
                    Back to Cart
                </a>
            </form>
        </div>

    </div>

@endsection