@extends('layouts.default')

@section('title', 'Import Sales')

@section('content')

    <div class="max-w-xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-8">

        <h1 class="text-xl font-bold mb-1">Import Sales Records</h1>
        <p class="text-sm text-gray-500 mb-6">Upload a CSV file of historical sales to backfill demand data.</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
        @endif

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6 text-sm">
            <p class="font-medium text-gray-700 mb-2">Required CSV columns:</p>
            <ul class="list-disc list-inside text-gray-600 space-y-1">
                <li><code class="bg-gray-200 px-1 rounded">product_name</code></li>
                <li><code class="bg-gray-200 px-1 rounded">quantity</code></li>
                <li><code class="bg-gray-200 px-1 rounded">date</code> — format: YYYY-MM-DD</li>
                <li><code class="bg-gray-200 px-1 rounded">price</code> — optional</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('sales.import.process') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
                <input type="file" name="csv_file" accept=".csv,.txt" required
                       class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0 file:text-sm file:font-medium
                              file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="auto_create_products" value="1" id="autoCreateCheckbox"
                           onchange="document.getElementById('categoryField').classList.toggle('hidden', !this.checked)"
                           class="rounded border-gray-300">
                    <span class="text-sm font-medium text-gray-700">
                        Auto-create products not found in the system
                    </span>
                </label>
                <p class="text-xs text-gray-500 mt-1 ml-6">
                    Unmatched product names will be created automatically with placeholder cost/price. You can edit them later in Product Management.
                </p>

                <div id="categoryField" class="hidden mt-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Category for New Products <span class="text-gray-400">(optional)</span>
                    </label>
                    <select name="default_category_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">Uncategorized (default)</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('sales.records') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                    Cancel
                </a>
                <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                    Upload & Import
                </button>
            </div>
        </form>

    </div>

@endsection