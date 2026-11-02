@if (($product->store_quantity ?? 0) === 0)
    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 dark:bg-slate-800 text-gray-600 dark:text-slate-300 border border-gray-300 dark:border-slate-700">Out of Stock</span>
@elseif ($product->isLowStock())
    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-rose-950/80 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900">Low Stock</span>
@else
    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-emerald-950/80 text-green-700 dark:text-emerald-300 border border-green-200 dark:border-emerald-900">In Stock</span>
@endif