@extends('layouts.default')

@section('title', 'Add User')

@section('content')
    <div class="max-w-2xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-8">

        <h1 class="text-xl font-bold mb-6">Add New User</h1>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 text-red-700 px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <!-- Name fields -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="firstname" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name
                    </label>
                    <input type="text" name="firstname" id="firstname" value="{{ old('firstname') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>

                <div>
                    <label for="lastname" class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name
                    </label>
                    <input type="text" name="lastname" id="lastname" value="{{ old('lastname') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>

                <div>
                    <label for="suffix" class="block text-sm font-medium text-gray-700 mb-1">
                        Suffix <span class="text-gray-400">(optional)</span>
                    </label>
                    <input type="text" name="suffix" id="suffix" value="{{ old('suffix') }}"
                           placeholder="Jr., Sr., III"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>
            </div>

            <!-- Username & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                        Username
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input type="password" name="password" id="password"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm Password
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                </div>
            </div>

            <!-- Role & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                        Role
                    </label>
                    <select name="role" id="role"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 mt-6 sm:mt-7">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-gray-800 focus:ring-gray-800">
                    <label for="is_active" class="text-sm font-medium text-gray-700">
                        Active account
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-gray-800">
                    Add User
                </button>
            </div>

        </form>
    </div>
@endsection