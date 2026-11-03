@extends('layouts.default')

@section('title', 'Category Management')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Category Management</h1>

    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden mb-8">

        <!-- Header -->
        <div class="flex items-center justify-between px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Main & Sub-Categories Hierarchy</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Manage top-level categories and their corresponding brand subcategories</p>
            </div>

            <div class="flex items-center gap-3">
                @include('partials.viewToggle')
                <!-- Add Product Button (Triggers Modal) -->
                <button type="button" onclick="toggleAddProductModal(true)"
                   class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer whitespace-nowrap">
                    + Add Product
                </button>
                <!-- Create Category Button -->
                <button type="button" onclick="toggleAddCategoryModal(true)"
                   class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer whitespace-nowrap">
                    + Create Category
                </button>
            </div>
        </div>

        <!-- Search / Filter / Sort Form -->
        <div class="px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536] bg-gray-50/50 dark:bg-[#161B26]/50">
            <form id="filterForm" method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-4">
                <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Search by name or code..." autocomplete="off"
                       class="flex-1 min-w-[200px] rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                              focus:outline-none focus:border-slate-400">

                <select name="main_group" onchange="this.form.submit()" class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                    <option value="all">Filter by Main Group (All)</option>
                    @foreach ($mainCategories as $mainCat)
                        <option value="{{ $mainCat->id }}" {{ request('main_group') == $mainCat->id ? 'selected' : '' }}>
                            {{ $mainCat->name }}
                        </option>
                    @endforeach
                </select>

                <select name="type" onchange="this.form.submit()" class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                    <option value="all" {{ request('type') === 'all' ? 'selected' : '' }}>All Types</option>
                    <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Product</option>
                    <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Service</option>
                </select>
            </form>
        </div>

        {{-- ================= LIST VIEW ================= --}}
        <div id="categoryResultsContainer">
            <div class="view-list overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-8 py-4">Main Category & Brand Subcategories</th>
                            <th class="px-8 py-4">Code</th>
                            <th class="px-8 py-4">Type</th>
                            <th class="px-8 py-4">Products</th>
                            <th class="px-8 py-4">Status</th>
                            <th class="px-8 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors align-top">
                                <!-- Entire cell area clickable to go to subcategories -->
                                <td class="px-8 py-4 space-y-2 cursor-pointer hover:bg-gray-100/60 dark:hover:bg-[#1e2536]/60 transition-colors" onclick="window.location='{{ route('admin.categories.show', $category) }}'">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900 dark:text-white uppercase tracking-wider hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $category->name }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900">MAIN CATEGORY</span>
                                    </div>

                                    @if($category->children->count() > 0)
                                        <div class="flex flex-wrap items-center gap-1.5 pt-1" onclick="event.stopPropagation()">
                                            <span class="text-xs font-semibold text-gray-400 dark:text-slate-500 mr-1">BRANDS:</span>
                                            @foreach($category->children as $child)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-50 dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] text-gray-700 dark:text-slate-300 shadow-2xs">
                                                    {{ $child->name }} 
                                                    @if($child->code)
                                                        <span class="text-[10px] text-gray-400 dark:text-slate-500 font-mono">({{ $child->code }})</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-8 py-4 font-mono text-xs text-gray-600 dark:text-slate-400">
                                    {{ $category->code ?? '—' }}
                                </td>
                                <td class="px-8 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $category->type === 'service' ? 'bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700' }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-gray-600 dark:text-slate-400 font-semibold">
                                    {{ $category->products->count() + $category->children->sum(fn($child) => $child->products->count()) }}
                                </td>
                                <td class="px-8 py-4">
                                    <span class="inline-block bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider border border-emerald-200 dark:border-emerald-900">
                                        {{ $category->status ?? 'ACTIVE' }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        <!-- Add Brand Button -->
                                        <button type="button" onclick="toggleAddBrandModal({{ $category->id }}, true)"
                                           class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-3 py-1.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                            + Brand
                                        </button>
                                        <!-- Edit Button -->
                                        <button type="button" onclick="toggleEditCategoryModal({{ $category->id }}, true)"
                                           class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-xs font-semibold px-3.5 py-1.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                            Edit
                                        </button>
                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-3.5 py-1.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">No categories found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================= GRID VIEW ================= --}}
            <div class="view-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 p-6 justify-items-center sm:justify-items-stretch">
                @forelse ($categories as $category)
                    <!-- Entire card clickable -->
                    <div class="w-full border-2 border-gray-200 dark:border-[#1e2536] rounded-2xl overflow-hidden bg-white dark:bg-[#161B26] flex flex-col shadow-xs p-5 justify-between cursor-pointer hover:border-blue-500/50 transition-all" onclick="window.location='{{ route('admin.categories.show', $category) }}'">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider">
                                    {{ $category->name }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900">MAIN</span>
                            </div>

                            <p class="text-xs text-gray-400 dark:text-slate-400 font-mono mb-2">Code: {{ $category->code ?? '—' }}</p>
                            
                            <div class="mb-4 flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $category->type === 'service' ? 'bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700' }}">
                                    {{ ucfirst($category->type) }}
                                </span>
                                <span class="text-xs font-semibold text-gray-500 dark:text-slate-400">
                                    Products: {{ $category->products->count() + $category->children->sum(fn($child) => $child->products->count()) }}
                                </span>
                            </div>

                            @if($category->children->count() > 0)
                                <div class="flex flex-wrap items-center gap-1 mb-4" onclick="event.stopPropagation()">
                                    <span class="text-[11px] font-semibold text-gray-400 dark:text-slate-500 mr-1">BRANDS:</span>
                                    @foreach($category->children as $child)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-gray-50 dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] text-gray-700 dark:text-slate-300">
                                            {{ $child->name }} 
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Grid Card Action Buttons -->
                        <div class="pt-3 border-t-2 border-gray-100 dark:border-[#1e2536] flex items-center gap-1.5" onclick="event.stopPropagation()">
                            <button type="button" onclick="toggleAddBrandModal({{ $category->id }}, true)"
                               class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-[10px] font-semibold px-2 py-1.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer text-center">
                                + Brand
                            </button>
                            <button type="button" onclick="toggleEditCategoryModal({{ $category->id }}, true)"
                               class="flex-1 bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-[10px] font-semibold px-2 py-1.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer text-center">
                                Edit
                            </button>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="flex-1 inline-block" onsubmit="return confirm('Delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-[10px] font-semibold px-2 py-1.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer text-center">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-400 dark:text-slate-500 py-12 font-medium">No categories found.</p>
                @endforelse
            </div>

            {{-- ================= RENDER ADD BRAND MODALS FOR ALL CATEGORIES ================= --}}
            @foreach ($categories as $category)
                <div id="addBrandModal-{{ $category->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
                    <div class="relative w-full max-w-2xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left" onclick="event.stopPropagation()">
                        
                        <div class="flex items-center justify-between pb-6 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add Brand to {{ strtoupper($category->name) }}</h3>
                            <button type="button" onclick="toggleAddBrandModal({{ $category->id }}, false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors text-xl font-bold">
                                &times;
                            </button>
                        </div>

                        <form action="{{ route('admin.categories.subcategories.store', $category) }}" method="POST" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Name</label>
                                    <input type="text" name="name" required placeholder="e.g. Aqualife"
                                           class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Code <span class="text-gray-400 font-normal">(Auto-generated)</span></label>
                                    <input type="text" readonly value="Auto-generated"
                                           class="w-full rounded-xl bg-gray-100 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-400 dark:text-slate-500 cursor-not-allowed focus:outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Parent Group</label>
                                    <input type="text" readonly value="{{ $category->name }}"
                                           class="w-full rounded-xl bg-gray-100 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-600 dark:text-slate-300 cursor-not-allowed focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Type</label>
                                    <input type="text" readonly value="{{ ucfirst($category->type) }}"
                                           class="w-full rounded-xl bg-gray-100 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-600 dark:text-slate-300 cursor-not-allowed focus:outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Description <span class="text-gray-400 font-normal">(optional)</span></label>
                                <textarea name="description" rows="3" placeholder="Description of category or subcategory..."
                                          class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400"></textarea>
                            </div>

                            <div class="flex items-center justify-end space-x-3 pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                                <button type="button" onclick="toggleAddBrandModal({{ $category->id }}, false)" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                    Save Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            {{-- ================= RENDER EDIT MODALS FOR ALL CATEGORIES ================= --}}
            @foreach ($categories as $category)
                <div id="editCategoryModal-{{ $category->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
                    <div class="relative w-full max-w-2xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left" onclick="event.stopPropagation()">
                        
                        <div class="flex items-center justify-between pb-6 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Category: {{ $category->name }}</h3>
                            <button type="button" onclick="toggleEditCategoryModal({{ $category->id }}, false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors text-xl font-bold">
                                &times;
                            </button>
                        </div>

                        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Name</label>
                                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required 
                                           class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Code <span class="text-gray-400 font-normal">(Locked)</span></label>
                                    <input type="text" readonly value="{{ $category->code ?? 'N/A' }}" 
                                           class="w-full rounded-xl bg-gray-100 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-500 dark:text-slate-400 cursor-not-allowed focus:outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Parent Group</label>
                                    <input type="text" readonly value="None (Top-Level Category)"
                                           class="w-full rounded-xl bg-gray-100 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-600 dark:text-slate-300 cursor-not-allowed focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Type</label>
                                    <select name="type" class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                                        <option value="product" {{ old('type', $category->type) === 'product' ? 'selected' : '' }}>Product</option>
                                        <option value="service" {{ old('type', $category->type) === 'service' ? 'selected' : '' }}>Service</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Description <span class="text-gray-400 font-normal">(optional)</span></label>
                                <textarea name="description" rows="3" placeholder="Description of category or subcategory..."
                                          class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">{{ old('description', $category->description ?? '') }}</textarea>
                            </div>

                            <div class="flex items-center justify-end space-x-3 pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                                <button type="button" onclick="toggleEditCategoryModal({{ $category->id }}, false)" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                    Save Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach

            <!-- Custom Pill Pagination Footer -->
            @if ($categories->hasPages())
                <div class="px-8 py-5 border-t-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-gray-500 dark:text-slate-400">
                    <div>
                        Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $categories->firstItem() ?? 0 }}</span> to <span class="font-semibold text-gray-900 dark:text-white">{{ $categories->lastItem() ?? 0 }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ $categories->total() }}</span> results
                    </div>

                    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1.5">
                        @if ($categories->onFirstPage())
                            <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                                Previous
                            </span>
                        @else
                            <a href="{{ $categories->previousPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                Previous
                            </a>
                        @endif

                        @foreach ($categories->getUrlRange(max(1, $categories->currentPage() - 2), min($categories->lastPage(), $categories->currentPage() + 2)) as $page => $url)
                            @if ($page == $categories->currentPage())
                                <span class="px-4 py-2 text-xs font-bold text-white bg-slate-900 dark:bg-[#1E293B] rounded-full shadow-xs border border-transparent">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if ($categories->hasMorePages())
                            <a href="{{ $categories->nextPageUrl() }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-slate-200 bg-white dark:bg-[#161B26] border border-gray-200 dark:border-[#1e2536] rounded-full hover:bg-gray-100 dark:hover:bg-[#1e2536] transition-colors cursor-pointer">
                                Next
                            </a>
                        @else
                            <span class="px-4 py-2 text-xs font-semibold text-gray-300 dark:text-slate-600 bg-gray-50 dark:bg-[#161B26]/50 rounded-full cursor-not-allowed border border-gray-200 dark:border-[#1e2536]">
                                Next
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        </div>

    </div>

    <!-- Create Category or Subcategory Modal Popup -->
    <div id="addCategoryModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-2xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left">
            
            <div class="flex items-center justify-between pb-6 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Create Category or Subcategory</h3>
                <button type="button" onclick="toggleAddCategoryModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors text-xl font-bold">
                    &times;
                </button>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-5">
                @csrf

                @if ($errors->any() && old('name'))
                    <div class="rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
                        Please check the fields below and try again.
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Electric / Liquids"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Code <span class="text-gray-400 font-normal">(Auto-generated)</span></label>
                        <input type="text" readonly value="Auto-generated"
                               class="w-full rounded-xl bg-gray-100 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-400 dark:text-slate-500 cursor-not-allowed focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Parent Group <span class="text-gray-400 font-normal">(leave blank if top-level)</span></label>
                        <select name="parent_id" class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                            <option value="">None (Top-Level Category)</option>
                            @foreach ($mainCategories as $mainCat)
                                <option value="{{ $mainCat->id }}" {{ old('parent_id') == $mainCat->id ? 'selected' : '' }}>{{ $mainCat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Type</label>
                        <select name="type" class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                            <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>Product</option>
                            <option value="service" {{ old('type') === 'service' ? 'selected' : '' }}>Service</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Description <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="description" rows="3" placeholder="Description of category or subcategory..."
                              class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">{{ old('description') }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                    <button type="button" onclick="toggleAddCategoryModal(false)" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Product Modal Popup -->
    <div id="addProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative w-full max-w-3xl bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-2xl p-8 transform transition-all text-left">
            
            <div class="flex items-center justify-between pb-6 mb-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add New Product</h3>
                <button type="button" onclick="toggleAddProductModal(false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors text-xl font-bold">
                    &times;
                </button>
            </div>

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Product Name</label>
                        <input type="text" name="product_name" required placeholder="e.g. Purified Drinking Water"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Category <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <select name="category_id" class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400 cursor-pointer">
                            <option value="">Uncategorized (default)</option>
                            @foreach ($mainCategories as $mainCat)
                                <option value="{{ $mainCat->id }}">{{ $mainCat->name }}</option>
                                @foreach($mainCat->children as $child)
                                    <option value="{{ $child->id }}">&nbsp;&nbsp;&mdash; {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Approx. Volume <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="text" name="approx_volume" placeholder="e.g. 20L, 5 gallons"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Volume (Liters) <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="number" step="0.01" name="volume_liters" placeholder="e.g. 20"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Purchase Cost</label>
                        <input type="number" step="0.01" name="purchase_cost" required placeholder="0.00"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Selling Price</label>
                        <input type="number" step="0.01" name="selling_price" required placeholder="0.00"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Store Quantity</label>
                        <input type="number" name="store_quantity" value="0" required
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Warehouse Quantity</label>
                        <input type="number" name="warehouse_quantity" value="0" required
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Reorder Point <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="number" name="reorder_point" placeholder="e.g. 10"
                               class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">SKU / Code <span class="text-gray-400 font-normal">(Auto-generated)</span></label>
                        <input type="text" readonly value="Auto-generated"
                               class="w-full rounded-xl bg-gray-100 dark:bg-[#161B26]/50 border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-400 dark:text-slate-500 cursor-not-allowed focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Product Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <textarea name="product_description" rows="3" placeholder="Enter product details or description..."
                              class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 focus:outline-none focus:border-slate-400"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-slate-400 uppercase mb-1">Product Image <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <div class="flex items-center gap-4">
                        <label class="bg-gray-100 hover:bg-gray-200 dark:bg-[#161B26] dark:hover:bg-[#1e2536] text-gray-700 dark:text-slate-300 text-xs font-semibold px-4 py-2.5 rounded-xl cursor-pointer border-2 border-gray-200 dark:border-[#1e2536] transition-colors">
                            Choose File
                            <input type="file" name="product_image" accept="image/*" class="hidden" onchange="document.getElementById('fileNameLabel').textContent = this.files[0] ? this.files[0].name : 'No file chosen'">
                        </label>
                        <span id="fileNameLabel" class="text-sm text-gray-500 dark:text-slate-400">No file chosen</span>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t-2 border-gray-200 dark:border-[#1e2536]">
                    <button type="button" onclick="toggleAddProductModal(false)" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-[#161B26] transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function toggleAddCategoryModal(show) {
        const modal = document.getElementById('addCategoryModal');
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function toggleAddProductModal(show) {
        const modal = document.getElementById('addProductModal');
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function toggleAddBrandModal(categoryId, show) {
        const modal = document.getElementById(`addBrandModal-${categoryId}`);
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function toggleEditCategoryModal(categoryId, show) {
        const modal = document.getElementById(`editCategoryModal-${categoryId}`);
        if (show) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        } else {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('filterForm');
        let debounceTimer;

        if (searchInput) {
            if (searchInput.value.length > 0) {
                searchInput.focus();
                searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
            }

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    filterForm.submit();
                }, 150);
            });
        }
    });

    @if ($errors->any() && old('name'))
        document.addEventListener('DOMContentLoaded', function () {
            toggleAddCategoryModal(true);
        });
    @endif
</script>
@endpush