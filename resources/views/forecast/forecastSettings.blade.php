@extends('layouts.default')

@section('title', 'Forecast Settings')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Forecast Settings</h1>

    @if (session('success'))
        <div class="mb-6 rounded-2xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950/65 dark:text-emerald-300 border-2 border-emerald-200 dark:border-emerald-900 px-6 py-4 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/65 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif
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
            <p class="text-sm text-gray-500 dark:text-slate-400 mb-6">Applies to every product unless overridden below.</p>

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
            <h2 class="text-xl font-bold mb-6 text-gray-900 dark:text-white">Add Override</h2>

            <form method="POST" action="{{ route('forecast.settings.store') }}"
                  class="flex flex-col sm:flex-row flex-wrap items-end gap-4" x-data="{ scope: 'category' }">
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
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Category Overrides</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-8 py-4">Category</th>
                            <th class="px-8 py-4">SMA Window</th>
                            <th class="px-8 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($categorySettings as $setting)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                                <td class="px-8 py-4 font-semibold text-gray-900 dark:text-white">{{ $setting->category->name ?? '—' }}</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-medium">{{ $setting->sma_window_days }} days</td>
                                <td class="px-8 py-4 text-right">
                                    <form action="{{ route('forecast.settings.destroy', $setting) }}" method="POST"
                                          onsubmit="return confirm('Remove this override?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">No category overrides set.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Product overrides list -->
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Product Overrides</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-8 py-4">Product</th>
                            <th class="px-8 py-4">SMA Window</th>
                            <th class="px-8 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($productSettings as $setting)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                                <td class="px-8 py-4 font-semibold text-gray-900 dark:text-white">{{ $setting->product->product_name ?? '—' }}</td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-medium">{{ $setting->sma_window_days }} days</td>
                                <td class="px-8 py-4 text-right">
                                    <form action="{{ route('forecast.settings.destroy', $setting) }}" method="POST"
                                          onsubmit="return confirm('Remove this override?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">No product overrides set.</td></tr>
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