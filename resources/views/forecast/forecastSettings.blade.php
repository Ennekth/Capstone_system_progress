@extends('layouts.default')

@section('title', 'Forecast Settings')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Forecast Settings</h1>

    @if ($errors->any())
        <div class="mb-6 rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/65 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-8">

        <!-- Global default -->
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8">
            <h2 class="text-xl font-bold mb-1 text-gray-900 dark:text-white">Global Default</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">Applies to every product unless overridden below.</p>

            <div class="mb-6 p-3.5 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border-2 border-blue-200 dark:border-blue-900 text-xs text-blue-800 dark:text-blue-300">
                <span class="font-bold">What it does & shows:</span> Sets the system-wide Simple Moving Average (SMA) lookback timeframe in days used for product sales predictions.
            </div>

            <form method="POST" action="{{ route('forecast.settings.global') }}" class="flex items-end gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">SMA Window (days)</label>
                    <input type="number" name="sma_window_days" min="1" max="365"
                           value="{{ old('sma_window_days', $global->sma_window_days ?? 30) }}"
                           class="w-36 rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                </div>
                <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-3 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    Save Global
                </button>
            </form>
        </div>

        <!-- Add override -->
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Add Override</h2>

            <div class="mb-6 p-3.5 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border-2 border-blue-200 dark:border-blue-900 text-xs text-blue-800 dark:text-blue-300">
                <span class="font-bold">What it does & shows:</span> Allows you to assign custom forecast periods to specific product categories or individual items, overriding the global default.
            </div>

            <form method="POST" action="{{ route('forecast.settings.store') }}"
                  class="flex flex-col sm:flex-row flex-wrap items-end gap-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Apply to</label>
                    <select name="scope" id="scopeSelect" onchange="toggleScopeFields(this.value)"
                            class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                        <option value="category">Product Group (Category)</option>
                        <option value="product">Specific Product</option>
                    </select>
                </div>

                <div id="categoryField">
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Category</label>
                    <select name="category_id" class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 min-w-[200px] cursor-pointer">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="productField" class="hidden">
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">Product</label>
                    <select name="product_id" class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 min-w-[200px] cursor-pointer">
                        <option value="">Select product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-2">SMA Window (days)</label>
                    <input type="number" name="sma_window_days" min="1" max="365" value="30"
                           class="w-32 rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                </div>

                <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-3 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                    Add Override
                </button>
            </form>
        </div>

        <!-- Category overrides list -->
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Category Overrides</h2>
                <div class="p-3.5 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border-2 border-blue-200 dark:border-blue-900 text-xs text-blue-800 dark:text-blue-300">
                    <span class="font-bold">What it does & shows:</span> Lists all active category-level forecast overrides, including their custom windows, user audits, and last updated timestamps.
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-8 py-4">Scope</th>
                            <th class="px-8 py-4">Category</th>
                            <th class="px-8 py-4">SMA Window</th>
                            <th class="px-8 py-4">Updated By</th>
                            <th class="px-8 py-4">Last Updated</th>
                            <th class="px-8 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($categorySettings as $setting)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                                <td class="px-8 py-4">
                                    <span class="inline-block bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-blue-200 dark:border-blue-900">Category</span>
                                </td>
                                <td class="px-8 py-4 font-semibold text-gray-900 dark:text-white">{{ $setting->category->name ?? '—' }}</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-medium">{{ $setting->sma_window_days }} days</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-medium">{{ $setting->updatedBy->full_name ?? $setting->updatedBy->name ?? 'System' }}</td>
                                <td class="px-8 py-4 text-gray-500 dark:text-slate-400 text-xs">{{ $setting->updated_at ? $setting->updated_at->format('M d, Y h:i A') : '—' }}</td>
                                <td class="px-8 py-4 text-right">
                                    <form action="{{ route('forecast.settings.destroy', $setting) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Remove this override?');" class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">No category overrides set.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Product overrides list -->
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Product Overrides</h2>
                <div class="p-3.5 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border-2 border-blue-200 dark:border-blue-900 text-xs text-blue-800 dark:text-blue-300">
                    <span class="font-bold">What it does & shows:</span> Lists all active product-level forecast overrides, including target items, categories, custom windows, and update metadata.
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-8 py-4">Scope</th>
                            <th class="px-8 py-4">Product</th>
                            <th class="px-8 py-4">Category</th>
                            <th class="px-8 py-4">SMA Window</th>
                            <th class="px-8 py-4">Updated By</th>
                            <th class="px-8 py-4">Last Updated</th>
                            <th class="px-8 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($productSettings as $setting)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                                <td class="px-8 py-4">
                                    <span class="inline-block bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-purple-200 dark:border-purple-900">Product</span>
                                </td>
                                <td class="px-8 py-4 font-semibold text-gray-900 dark:text-white">{{ $setting->product->product_name ?? '—' }}</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-medium">{{ $setting->product->category->name ?? '—' }}</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-medium">{{ $setting->sma_window_days }} days</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-medium">{{ $setting->updatedBy->full_name ?? $setting->updatedBy->name ?? 'System' }}</td>
                                <td class="px-8 py-4 text-gray-500 dark:text-slate-400 text-xs">{{ $setting->updated_at ? $setting->updated_at->format('M d, Y h:i A') : '—' }}</td>
                                <td class="px-8 py-4 text-right">
                                    <form action="{{ route('forecast.settings.destroy', $setting) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Remove this override?');" class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">No product overrides set.</td></tr>
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