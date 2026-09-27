@extends('layouts.default')

@section('title', 'Add Product')

@section('content')
    <div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-8">

        <h1 class="text-xl font-bold mb-6">Add New Product</h1>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Product Name
                </label>
                <input type="text" name="product_name" id="product_name" value="{{ old('product_name') }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
            </div>

            <div>
                <label for="product_description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea name="product_description" id="product_description" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">{{ old('product_description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Category
                    </label>
                    <select name="category_id" id="category_id"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        <option value="">Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ ucfirst($category->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="approx_volume" class="block text-sm font-medium text-gray-700 mb-1">
                        Approx. Volume <span class="text-gray-400">(optional)</span>
                    </label>
                    <input type="text" name="approx_volume" id="approx_volume" value="{{ old('approx_volume') }}"
                           placeholder="e.g. 20L, 5 gallons"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="purchase_cost" class="block text-sm font-medium text-gray-700 mb-1">
                        Purchase Cost
                    </label>
                    <input type="number" step="0.01" min="0" name="purchase_cost" id="purchase_cost" value="{{ old('purchase_cost') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>

                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Selling Price
                    </label>
                    <input type="number" step="0.01" min="0" name="selling_price" id="selling_price" value="{{ old('selling_price') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                        Initial Quantity <span class="text-gray-400">(optional, defaults to 0)</span>
                    </label>
                    <input type="number" min="0" name="quantity" id="quantity" value="{{ old('quantity', 0) }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>

                <div>
                    <label for="reorder_point" class="block text-sm font-medium text-gray-700 mb-1">
                        Reorder Point <span class="text-gray-400">(optional)</span>
                    </label>
                    <input type="number" min="0" name="reorder_point" id="reorder_point" value="{{ old('reorder_point') }}"
                           placeholder="e.g. 10"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>
            </div>

            <div>
                <label for="product_image" class="block text-sm font-medium text-gray-700 mb-1">
                    Product Image <span class="text-gray-400">(optional)</span>
                </label>
                <input type="file" name="product_image" id="product_image" accept="image/*"
                       class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0 file:text-sm file:font-medium
                              file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.products.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                    Add Product
                </button>
            </div>

        </form>
    </div>
@endsection