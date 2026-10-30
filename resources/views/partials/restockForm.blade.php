@php $role = auth()->user()->role; @endphp

<form action="{{ route('inventory.restock', $product) }}" method="POST" class="flex flex-wrap items-end gap-3">
    @csrf
    <input type="hidden" name="type" value="{{ $role === 'warehouse_staff' ? 'restock_warehouse' : 'restock_store' }}">

    @if (!in_array($role, ['warehouse_staff', 'staff']))
        <div>
            <label class="block text-xs text-gray-600 mb-1">Location</label>
            <select name="location" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="store">Store</option>
                <option value="warehouse">Warehouse</option>
            </select>
        </div>
    @else
        <input type="hidden" name="location" value="{{ $role === 'warehouse_staff' ? 'warehouse' : 'store' }}">
        <p class="text-xs text-gray-500">
            Restocking: <strong>{{ $role === 'warehouse_staff' ? 'Warehouse' : 'Store' }}</strong>
        </p>
    @endif

    <div>
        <label class="block text-xs text-gray-600 mb-1">Quantity</label>
        <input type="number" name="quantity" min="1" required
               class="w-24 rounded-lg border border-gray-300 px-3 py-2 text-sm">
    </div>

    <div class="flex-1 min-w-[140px]">
        <label class="block text-xs text-gray-600 mb-1">Note (optional)</label>
        <input type="text" name="note" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
    </div>

    <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-800">
        Submit
    </button>
</form>

@if ($role === 'staff')
    <p class="text-xs text-amber-600 mt-2">This request will need admin/manager approval before stock updates.</p>
@endif