@extends('layouts.default')

@section('title', 'Category Management')

@section('content')
    <div class="max-w-4xl mx-auto space-y-8">

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Category Management</h1>

        <!-- Flash messages -->
        @if (session('success'))
            <div class="rounded-2xl bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-2 border-emerald-200 dark:border-emerald-900 px-6 py-4 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- Add category form -->
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm p-8">
            <h2 class="text-xl font-bold mb-6 text-gray-900 dark:text-white">Add Category</h2>

            @if ($errors->any())
                <div class="mb-6 rounded-2xl bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-rose-300 border-2 border-red-200 dark:border-rose-900 px-6 py-4 text-sm font-medium">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-col sm:flex-row gap-4">
                @csrf

                <input type="text" name="name" placeholder="Category name (e.g. Water Filters)" value="{{ old('name') }}"
                       class="flex-1 rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                              focus:outline-none focus:border-slate-400">

                <select name="type"
                        class="rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-3 text-sm text-gray-900 dark:text-slate-100
                               focus:outline-none focus:border-slate-400 cursor-pointer">
                    <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>Product</option>
                    <option value="service" {{ old('type') === 'service' ? 'selected' : '' }}>Service</option>
                </select>

                <!-- Add Category Button -->
                <button type="submit"
                        class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-3 rounded-xl transition-all duration-300 ease-in-out whitespace-nowrap shadow-xs cursor-pointer">
                    Add Category
                </button>
            </form>
        </div>

        <!-- Category list -->
        <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536] flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Existing Categories</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Browse and manage product/service categories</p>
                </div>
                
                <!-- Live Search Input -->
                <div class="w-full sm:w-72">
                    <input type="text" id="searchInput" placeholder="Search categories..."
                           class="w-full rounded-xl bg-white dark:bg-[#161B26] border-2 border-gray-200 dark:border-[#1e2536] px-4 py-2.5 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 focus:outline-none focus:border-slate-400" autocomplete="off">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b-2 border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-8 py-4">Name</th>
                            <th class="px-8 py-4">Type</th>
                            <th class="px-8 py-4">Products Using It</th>
                            <th class="px-8 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody" class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($categories as $category)
                            <tr class="category-row hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                                <td class="category-name px-8 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $category->name }}
                                </td>
                                <td class="px-8 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        {{ $category->type === 'service' ? 'bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700' }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-gray-500 dark:text-slate-400 font-medium">
                                    {{ $category->products()->count() }}
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                          onsubmit="return confirm('Delete this category?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-4 py-2 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr id="noCategoriesRow">
                                <td colspan="4" class="px-8 py-12 text-center text-gray-400 dark:text-slate-500 font-medium">
                                    No categories found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Pill Pagination Footer (Auto-hides if items <= 4) -->
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('.category-row');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.toLowerCase().trim();

                rows.forEach(row => {
                    const nameCell = row.querySelector('.category-name');
                    if (nameCell) {
                        const text = nameCell.textContent.toLowerCase();
                        if (text.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        }
    });
</script>
@endpush