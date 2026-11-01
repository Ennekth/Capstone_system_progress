<!-- Product Detail Modal (shared, populated via JS) -->
<div id="productDetailModal" class="fixed inset-0 z-40 hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-xl shadow-lg max-w-lg w-full max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold" id="pdm-name">Product</h3>
            <button type="button" onclick="closeProductDetail()" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <img id="pdm-image" src="" alt="" class="w-full h-48 object-cover rounded-lg border border-gray-200 hidden">

            <div>
                <p class="text-xs text-gray-400 uppercase font-medium">Description</p>
                <p id="pdm-description" class="text-sm text-gray-700 mt-1"></p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase font-medium">Category</p>
                    <p id="pdm-category" class="text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-medium">Approx. Volume</p>
                    <p id="pdm-volume" class="text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-medium">Purchase Cost</p>
                    <p id="pdm-cost" class="text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-medium">Selling Price</p>
                    <p id="pdm-price" class="text-gray-900 mt-1 font-semibold"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-medium">Store Stock</p>
                    <p id="pdm-store-qty" class="text-gray-900 mt-1"></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase font-medium">Warehouse Stock</p>
                    <p id="pdm-warehouse-qty" class="text-gray-900 mt-1"></p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function openProductDetail(productId) {
        fetch(`{{ url('/inventory') }}/${productId}/details`)
            .then(res => {
                if (!res.ok) throw new Error('Request failed');
                return res.json();
            })
            .then(data => {
                document.getElementById('pdm-name').textContent = data.name;
                document.getElementById('pdm-description').textContent = data.description;
                document.getElementById('pdm-category').textContent = data.category;
                document.getElementById('pdm-volume').textContent = data.approx_volume;
                document.getElementById('pdm-cost').textContent = '₱' + data.purchase_cost;
                document.getElementById('pdm-price').textContent = '₱' + data.selling_price;
                document.getElementById('pdm-store-qty').textContent = data.store_quantity;
                document.getElementById('pdm-warehouse-qty').textContent = data.warehouse_quantity;

                const img = document.getElementById('pdm-image');
                if (data.image) {
                    img.src = data.image;
                    img.classList.remove('hidden');
                } else {
                    img.classList.add('hidden');
                }

                const modal = document.getElementById('productDetailModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            })
            .catch(() => {
                alert('Could not load product details.');
            });
    }

    function closeProductDetail() {
        const modal = document.getElementById('productDetailModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('click', function (e) {
        const modal = document.getElementById('productDetailModal');
        if (e.target === modal) {
            closeProductDetail();
        }
    });
</script>