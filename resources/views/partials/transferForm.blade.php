<form action="{{ route('inventory.transfer', $product) }}" method="POST" class="flex flex-wrap items-end gap-3">
    @csrf
    <div>
        <label class="block text-xs text-gray-600 dark:text-slate-400 mb-1">Direction</label>
        <select name="direction" class="rounded-lg bg-white dark:bg-[#10141D] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-slate-400">
            <option value="to_store">Warehouse → Store</option>
            <option value="to_warehouse">Store → Warehouse</option>
        </select>
    </div>
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
        Transfer
    </button>
</form>