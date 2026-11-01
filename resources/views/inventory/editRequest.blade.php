@extends('layouts.default')

@section('title', 'Edit Restock Request')

@section('content')

    <div class="max-w-lg mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-8">

        <h1 class="text-xl font-bold mb-1">Edit Restock Request</h1>
        <p class="text-sm text-gray-500 mb-6">{{ $movement->product->product_name ?? 'Deleted Product' }}</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('inventory.updateRequest', $movement) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', $movement->quantity) }}" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Note (optional)</label>
                <input type="text" name="note" value="{{ old('note', $movement->note) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('inventory.myRequests') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                    Cancel
                </a>
                <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                    Save Changes
                </button>
            </div>
        </form>

    </div>

@endsection