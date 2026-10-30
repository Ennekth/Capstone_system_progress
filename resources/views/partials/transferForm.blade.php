<form action="{{ route('inventory.transfer', $product) }}" method="POST" class="flex flex-wrap items-end gap-3">
    @csrf
    <div>
        <label class="block text-xs text-gray-600 mb-1">Direction</label>
        <select name="direction" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="to_store">Warehouse → Store</option>
            <option value="to_warehouse">Store → Warehouse</option>
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-600 mb-1">Quantity</label>
        <input type="number" name="quantity" min="1" required
               class="w-24 rounded-lg border border-gray-300 px-3 py-2 text-sm">
    </div>
    <div class="flex-1 min-w-[140px]">
        <label class="block text-xs text-gray-600 mb-1">Note (optional)</label>
        <input type="text" name="note" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
    </div>
    <button type="submit" class="bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-700">
        Transfer
    </button>
</form>