@extends('layouts.default')

@section('title', 'Category Management')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Flash messages -->
        @if (session('success'))
            <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- Add category form -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h1 class="text-lg font-bold mb-4">Add Category</h1>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
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
                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">

                <select name="type"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>Product</option>
                    <option value="service" {{ old('type') === 'service' ? 'selected' : '' }}>Service</option>
                </select>

                <button type="submit"
                        class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800 whitespace-nowrap">
                    Add Category
                </button>
            </form>
        </div>

        <!-- Category list -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold">Existing Categories</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Products Using It</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $category->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $category->type === 'service' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    {{ $category->products()->count() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                          onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-400">
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