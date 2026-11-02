@extends('layouts.default')

@section('title', 'Category Management')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Flash messages -->
        @if (session('success'))
            <div class="rounded-lg bg-green-100 dark:bg-emerald-950/85 text-green-800 dark:text-emerald-300 border border-green-200 dark:border-emerald-900 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg bg-red-100 dark:bg-rose-950/85 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Add category form -->
        <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm p-6">
            <h1 class="text-lg font-bold mb-4 text-gray-900 dark:text-white">Add Category</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-100 dark:bg-rose-950/85 text-red-700 dark:text-rose-300 border border-red-200 dark:border-rose-900 px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-col sm:flex-row gap-3">
                @csrf

                <input type="text" name="name" placeholder="Category name (e.g. Water Filters)" value="{{ old('name') }}"
                       class="flex-1 rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500
                              focus:outline-none focus:ring-1 focus:ring-slate-400">

                <select name="type"
                        class="rounded-lg bg-white dark:bg-[#161B26] border border-gray-300 dark:border-[#1e2536] px-3 py-2 text-sm text-gray-900 dark:text-slate-100
                               focus:outline-none focus:ring-1 focus:ring-slate-400">
                    <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>Product</option>
                    <option value="service" {{ old('type') === 'service' ? 'selected' : '' }}>Service</option>
                </select>

                <!-- Add Category Button (Option B Style) -->
                <button type="submit"
                        class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-300 ease-in-out whitespace-nowrap shadow-xs cursor-pointer">
                    Add Category
                </button>
            </form>
        </div>

        <!-- Category list -->
        <div class="bg-white dark:bg-[#10141D] border border-gray-200 dark:border-[#1e2536] rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-[#1e2536]">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Existing Categories</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-[#161B26] text-gray-600 dark:text-slate-400 uppercase text-xs border-b border-gray-200 dark:border-[#1e2536]">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Products Using It</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-[#1e2536]">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50 dark:hover:bg-[#1e2536]/40 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $category->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $category->type === 'service' ? 'bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900' : 'bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border border-gray-200 dark:border-slate-700' }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-slate-400">
                                    {{ $category->products()->count() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                          onsubmit="return confirm('Delete this category?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <!-- Delete Button (Destructive Rose Style) -->
                                        <button type="submit" 
                                                class="bg-white border-2 border-rose-600 text-rose-700 hover:bg-rose-600 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-rose-600/80 dark:text-rose-400 dark:hover:bg-rose-600 dark:hover:text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-all duration-300 ease-in-out shadow-xs cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-400 dark:text-slate-500">
                                    No categories yet. Add one above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection