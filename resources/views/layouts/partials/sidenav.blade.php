@php
    $linkBase   = 'flex items-center px-3 py-2 rounded-lg text-sm font-medium';
    $linkActive = 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white';
    $linkIdle   = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white';
@endphp
<!-- Sidebar -->
<aside id="sidebar" class="bg-white text-gray-700 border-r border-gray-200 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-800 flex flex-col">

    <div class="px-6 py-5 border-b border-gray-800 whitespace-nowrap flex items-center justify-between">
        <img src="{{ asset('images/logo.svg') }}" alt="Company Logo" class="h-8 w-auto">
        <h1 class="text-xl font-bold">Inventory System</h1>

        <button type="button" data-sidebar-toggle class="p-1 rounded hover:bg-gray-800 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

@if (auth()->user()->role === 'warehouse_staff')

    <a href="{{ route('inventory.view') }}"
       class="{{ $linkBase }}
              {{ request()->routeIs('inventory.view') ? $linkActive : $linkIdle }}">
        Inventory Management
    </a>

@else

    <nav class="flex-1 px-3 py-4 space-y-1 whitespace-nowrap overflow-y-auto">

        <a href="{{ route('home') }}"
           class="{{ $linkBase }}
                  {{ request()->routeIs('home') ? $linkActive : $linkIdle }}">
            Dashboard
        </a>

        <a href="{{ route('pos') }}"
           class="{{ $linkBase }}
                  {{ request()->routeIs('pos') || request()->routeIs('checkout*') ? $linkActive : $linkIdle }}">
            Point of Sale
        </a>

        <a href="{{ route('transaction.history') }}"
           class="{{ $linkBase }}
                  {{ request()->routeIs('transaction.history') ? $linkActive : $linkIdle }}">
            Transaction History
        </a>

        @if (in_array(auth()->user()->role, ['admin', 'manager']))
            <a href="{{ route('admin.products.index') }}"
               class="{{ $linkBase }}
                      {{ request()->routeIs('admin.products.*') ? $linkActive : $linkIdle }}">
                Product Management
            </a>

            <a href="{{ route('eoq.calibration') }}"
                class="{{ $linkBase }}
                    {{ request()->routeIs('eoq.calibration') ? $linkActive : $linkIdle }}">
                EOQ Calibration
            </a>

            <a href="{{ route('sales.records') }}"
            class="{{ $linkBase }}
                    {{ request()->routeIs('sales.*') ? $linkActive : $linkIdle }}">
                Sales Records
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="{{ $linkBase }}
                      {{ request()->routeIs('admin.categories.*') ? $linkActive : $linkIdle }}">
                Categories
            </a>
        @endif

        @if (auth()->user()->role === 'admin')
            <a href="{{ route('admin.users.index') }}"
               class="{{ $linkBase }}
                      {{ request()->routeIs('admin.users.*') ? $linkActive : $linkIdle }}">
                User Management
            </a>
        @endif

        @if (in_array(auth()->user()->role, ['admin', 'manager']))
            @php
                $pendingCount = \App\Models\StockMovement::where('status', 'pending')->count();
            @endphp

            <a href="{{ route('inventory.index') }}"
               class="{{ $linkBase }}
                      {{ request()->routeIs('inventory.index') ? $linkActive : $linkIdle }}">
                Inventory Management
            </a>

            <a href="{{ route('inventory.approvals') }}"
               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('inventory.approvals') ? $linkActive : $linkIdle }}">
                <span>Pending Approvals</span>
                @if ($pendingCount > 0)
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('inventory.logs') }}"
               class="{{ $linkBase }}
                      {{ request()->routeIs('inventory.logs') ? $linkActive : $linkIdle }}">
                Stock Logs
            </a>

        @elseif (in_array(auth()->user()->role, ['warehouse_staff', 'staff']))
            <a href="{{ route('inventory.view') }}"
               class="{{ $linkBase }}
                      {{ request()->routeIs('inventory.view') ? $linkActive : $linkIdle }}">
                Inventory Management
            </a>

            <a href="{{ route('inventory.myRequests') }}"
            class="{{ $linkBase }}
                    {{ request()->routeIs('inventory.myRequests') ? $linkActive : $linkIdle }}">
                My Restock Requests
            </a>

        @endif

    </nav>

@endif



    <div class="px-3 py-4 border-t border-gray-800 whitespace-nowrap">
        <p class="px-3 pb-2 text-xs text-gray-400 truncate">
            Hello, {{ auth()->user()->full_name }}
        </p>

            <button type="button" id="themeToggle"
                    class="w-full flex items-center gap-2 text-left px-3 py-2 mb-1 rounded-lg text-sm font-medium
                        text-gray-600 hover:bg-gray-100 hover:text-gray-900
                        dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white">
                <svg class="h-5 w-5 dark:hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <svg class="h-5 w-5 hidden dark:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="dark:hidden">Dark Mode</span>
                <span class="hidden dark:inline">Light Mode</span>
            </button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-gray-300 bg-red-900 hover:bg-red-700 hover:text-white">
                Logout
            </button>
        </form>
    </div>
</aside>
