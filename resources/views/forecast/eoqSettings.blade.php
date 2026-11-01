@extends('layouts.default')

@section('title', 'EOQ Settings')

@section('content')

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-xl bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <h1 class="text-lg font-bold mb-1">EOQ Settings</h1>
        <p class="text-sm text-gray-500 mb-6">
            Default values used when a product hasn't been individually calibrated.
        </p>

        <form method="POST" action="{{ route('eoq.settings.update') }}" class="space-y-5">
            @csrf

            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Ordering Cost</h3>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Flat Default (₱ per order)</label>
                    <input type="number" step="0.01" min="0" name="default_ordering_cost"
                           value="{{ old('default_ordering_cost', $settings->default_ordering_cost ?? '') }}"
                           placeholder="Leave blank to use percentage instead"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">
                        If set, this is used for every uncalibrated product regardless of its price. Most businesses use this, since ordering cost (paperwork, delivery) usually doesn't scale with item price.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fallback: % of Purchase Cost</label>
                    <input type="number" step="0.01" min="0" max="100" name="ordering_cost_percentage"
                           value="{{ old('ordering_cost_percentage', $settings->ordering_cost_percentage ?? 10) }}"
                           class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Only used if the flat default above is left blank.</p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Holding Cost</h3>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">% of Purchase Cost / Year</label>
                    <input type="number" step="0.01" min="0" max="100" name="holding_cost_percentage"
                           value="{{ old('holding_cost_percentage', $settings->holding_cost_percentage ?? 20) }}"
                           class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Used for products without a storage space (cbm) set. 20–30% is a common industry range.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">₱ per Cubic Meter / Year</label>
                    <input type="number" step="0.01" min="0" name="holding_cost_per_cbm"
                           value="{{ old('holding_cost_per_cbm', $settings->holding_cost_per_cbm ?? 0) }}"
                           class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <p class="text-xs text-gray-400 mt-1">Takes priority over the percentage when a product has storage space set.</p>
                </div>
            </div>

            <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                Save Settings
            </button>
        </form>
    </div>

@endsection