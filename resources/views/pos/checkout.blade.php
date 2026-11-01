@extends('layouts.default')

@section('title', 'Checkout')

@section('content')

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 dark:bg-rose-950/85 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- ORDER SUMMARY -->
        <div class="flex-[2] bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm p-6 h-fit">
            <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Order Summary</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-slate-300">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-xs uppercase text-gray-500 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Product</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3 rounded-r-lg text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @foreach ($cart as $item)
                            @php
                                $subtotal = $item['subtotal'] ?? ($item['selling_price'] * $item['quantity']);
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $item['product_name'] }}</td>
                                <td class="px-4 py-3">₱{{ number_format($item['selling_price'], 2) }}</td>
                                <td class="px-4 py-3">{{ $item['quantity'] }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-slate-200">₱{{ number_format($subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200 dark:border-[#1e2536]">
                <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">₱{{ number_format($total, 2) }}</span>
            </div>
        </div>

        <!-- PAYMENT -->
        <div class="flex-1 bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm p-6 h-fit">
            <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Payment</h2>

            <form id="checkoutForm" action="{{ route('checkout.process') ?? '#' }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="amount_paid" class="block text-xs font-medium text-gray-600 dark:text-slate-400 mb-1">
                        Amount Paid
                    </label>
                    <input type="number" step="0.01" min="{{ $total }}" name="amount_paid" id="amount_paid" required
                           placeholder="Must be at least ₱{{ number_format($total, 2) }}"
                           class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                                  focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <span class="text-[11px] text-gray-400 dark:text-slate-500 mt-1 block">
                        Must be at least ₱{{ number_format($total, 2) }}
                    </span>
                </div>

                <div class="space-y-3 pt-2">
                    <!-- Confirm Payment -->
                    <button type="submit"
                            class="w-full bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-emerald-600/80 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white font-semibold py-3 rounded-xl transition-all duration-300 ease-in-out shadow-sm cursor-pointer">
                        Confirm Payment
                    </button>

                    <!-- Back to Cart -->
                    <a href="{{ route('pos') }}"
                       class="block w-full text-center bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-sm">
                        Back to Cart
                    </a>
                </div>
            </form>
        </div>

    </div>

    <!-- Receipt Modal Window Popup -->
    <div id="receiptModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div id="receiptModalContent" class="relative w-full max-w-md transform transition-all text-left">
            <!-- Dynamic Receipt Content Injected Here -->
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Create a temporary element to parse the returned HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Look for receipt container or extract content block
            const receiptBody = doc.querySelector('.min-h-\\[70vh\\]') || doc.body;

            document.getElementById('receiptModalContent').innerHTML = receiptBody.innerHTML;
            document.getElementById('receiptModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        })
        .catch(error => {
            console.error('Error processing payment:', error);
            // Fallback to normal submission if fetch fails
            form.submit();
        });
    });
</script>
@endpush