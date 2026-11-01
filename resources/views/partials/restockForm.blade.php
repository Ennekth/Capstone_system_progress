@php $role = auth()->user()->role; @endphp

<form action="{{ route('inventory.restock', $product) }}" method="POST" class="flex flex-wrap items-end gap-3">
    @csrf
    <input type="hidden" name="type" value="{{ $role === 'warehouse_staff' ? 'restock_warehouse' : 'restock_store' }}">

    @if (!in_array($role, ['warehouse_staff', 'staff']))
        <div>
            <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Location</label>
            <select name="location" class="rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
                <option value="store">Store</option>
                <option value="warehouse">Warehouse</option>
            </select>
        </div>
    @else
        <input type="hidden" name="location" value="{{ $role === 'warehouse_staff' ? 'warehouse' : 'store' }}">
        <p class="text-xs text-gray-500 dark:text-slate-400">
            Restocking: <strong class="text-gray-900 dark:text-white">{{ $role === 'warehouse_staff' ? 'Warehouse' : 'Store' }}</strong>
        </p>
    @endif

    <div>
        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Quantity</label>
        <input type="number" name="quantity" min="1" required
               class="w-24 rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
    </div>

    <div class="flex-1 min-w-[140px]">
        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Note (optional)</label>
        <input type="text" name="note" class="w-full rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
    </div>

    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
        Submit
    </button>
</form>

@if ($role === 'staff')
    <p class="text-xs text-amber-600 dark:text-amber-400 mt-2">This request will need admin/manager approval before stock updates.</p>
@endif