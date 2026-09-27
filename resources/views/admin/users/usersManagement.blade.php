@extends('layouts.default')

@section('title', 'Users Management')

@section('content')
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
            <div>
                <h1 class="text-xl font-bold">User Management</h1>
                <p class="text-sm text-gray-500 mt-1">Manage admin, manager, and staff accounts</p>
            </div>

            <a href="{{ route('admin.users.create') }}"
               class="bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-800">
                + Add User
            </a>
        </div>

        <!-- Flash message -->
        @if (session('success'))
            <div class="mx-6 mt-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Username</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $user->firstname }} {{ $user->lastname }} {{ $user->suffix }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->username }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $roleColors = [
                                        'admin' => 'bg-purple-100 text-purple-700',
                                        'manager' => 'bg-blue-100 text-blue-700',
                                        'staff' => 'bg-gray-100 text-gray-700',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                                        Edit
                                    </a>

                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection