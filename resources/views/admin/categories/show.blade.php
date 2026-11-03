@extends('layouts.default')

@section('title', $category->name . ' - Subcategories')

@section('content')

    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white uppercase">{{ $category->name }}</h1>

    <div class="bg-white dark:bg-[#10141D] border-2 border-gray-200 dark:border-[#1e2536] rounded-3xl shadow-sm overflow-hidden mb-8">

        <!-- Header -->
        <div class="flex items-center justify-between px-8 py-6 border-b-2 border-gray-200 dark:border-[#1e2536]">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Brand Subcategories Hierarchy</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Viewing brand subcategories belonging to {{ $category->name }}</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.categories.index') }}"
                   class="bg-white border-2 border-slate-300 text-slate-700 hover:bg-slate-700 hover:text-white dark:bg-[#10141D] dark:border-2 dark:border-[#1e2536] dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white text-sm font-semibold px-6 py-2.5 rounded-xl transition-all duration-300 ease-in-out shadow-xs cursor-pointer whitespace-nowrap">
                    &larr; Back to Categories
                </a>
            </div>
        </div>

        <!-- Subcategories Grid Section -->
        <div class="p-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($category->children as $child)
                    <!-- Fully Clickable Brand Subcategory Card -->
                    <div class="w-full border-2 border-gray-200 dark:border-[#1e2536] rounded-2xl overflow-hidden bg-white dark:bg-[#161B26] flex flex-col shadow-xs p-5 justify-between cursor-pointer hover:border-blue-500/50 dark:hover:border-blue-500/50 transition-all" 
                         onclick="window.location='{{ route('admin.categories.subcategories.show', $child) }}'">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider">
                                    {{ $child->name }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-900">BRAND</span>
                            </div>

                            <p class="text-xs text-gray-400 dark:text-slate-400 font-mono mb-4">Code: {{ $child->code ?? '—' }}</p>
                        </div>

                        <div class="pt-3 border-t-2 border-gray-100 dark:border-[#1e2536] flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500 dark:text-slate-400">
                                Products: <span class="text-gray-900 dark:text-white font-bold">{{ $child->products->count() }}</span>
                            </span>
                            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                View &rarr;
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-400 dark:text-slate-500 font-medium">
                        No brand subcategories found under this main category.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

@endsection