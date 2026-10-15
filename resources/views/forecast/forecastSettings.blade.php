@extends('layouts.default')

@section('title', 'Forecast Settings')

@section('content')

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-6">

        <!-- Global default -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold mb-1">Global Default</h2>
            <p class="text-sm text-gray-500 mb-4">Applies to every product unless overridden below.</p>

            <form method="POST" action="{{ route('forecast.settings.global') }}" class="flex items-end gap-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMA Window (days)</label>
                    <input type="number" name="sma_window_days" min="1" max="365"
                           value="{{ old('sma_window_days', $global->sma_window_days ?? 30) }}"
                           class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                    Save
                </button>
            </form>
        </div>

        <!-- Add override -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold mb-4">Add Override</h2>

            <form method="POST" action="{{ route('forecast.settings.store') }}"
                  class="flex flex-col sm:flex-row flex-wrap items-end gap-3" x-data="{ scope: 'category' }">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apply to</label>
                    <select name="scope" id="scopeSelect" onchange="toggleScopeFields(this.value)"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="category">Product Group (Category)</option>
                        <option value="product">Specific Product</option>
                    </select>
                </div>

                <div id="categoryField">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm min-w-[180px]">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="productField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                    <select name="product_id" class="rounded-lg border border-gray-300 px-3 py-2 text-sm min-w-[180px]">
                        <option value="">Select product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMA Window (days)</label>
                    <input type="number" name="sma_window_days" min="1" max="365" value="30"
                           class="w-28 rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>

                <button type="submit" class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                    Add Override
                </button>
            </form>
        </div>

        <!-- Category overrides list -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="font-bold">Category Overrides</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">SMA Window</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($categorySettings as $setting)
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $setting->category->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $setting->sma_window_days }} days</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('forecast.settings.destroy', $setting) }}" method="POST"
                                          onsubmit="return confirm('Remove this override?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-6 text-center text-gray-400">No category overrides set.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Product overrides list -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="font-bold">Product Overrides</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">SMA Window</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($productSettings as $setting)
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $setting->product->product_name ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $setting->sma_window_days }} days</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('forecast.settings.destroy', $setting) }}" method="POST"
                                          onsubmit="return confirm('Remove this override?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-6 text-center text-gray-400">No product overrides set.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        function toggleScopeFields(scope) {
            document.getElementById('categoryField').classList.toggle('hidden', scope !== 'category');
            document.getElementById('productField').classList.toggle('hidden', scope !== 'product');
        }
    </script>

@endsection