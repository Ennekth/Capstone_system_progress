@if (($product->store_quantity ?? 0) === 0)
    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-600">Out of Stock</span>
@elseif ($product->isLowStock())
    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Low Stock</span>
@else
    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">In Stock</span>
@endif