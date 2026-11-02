@php
    $linkBase   = 'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200';
    $linkActive = 'bg-[#161B26] text-white border border-[#1e2536] shadow-xs font-semibold';
    $linkIdle   = 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white';
    
    $resolvedRole = Auth::user()->email === 'admin@example.com' ? 'admin' : Auth::user()->role;
    $friendlySubtitles = [
        'admin' => 'System Administrator',
        'manager' => 'Operations Manager',
        'warehouse_staff' => 'Inventory Specialist',
        'staff' => 'System Operator'
    ];
    $userSubtitle = $friendlySubtitles[$resolvedRole] ?? 'Authorized User';
@endphp

<aside id="sidebar" class="w-68 bg-white text-gray-700 border-r border-gray-200 dark:bg-[#10141D] dark:text-slate-100 dark:border-[#1e2536] flex flex-col flex-shrink-0">

    <!-- Sidebar Header -->
    <div class="px-5 py-5 border-b border-gray-200 dark:border-[#1e2536] whitespace-nowrap flex items-center justify-between h-18">
        <div class="flex items-center gap-3 overflow-hidden">
            <img src="{{ asset('images/logo.svg') }}" alt="Company Logo" class="h-8 w-auto flex-shrink-0">
            <div class="overflow-hidden sidebar-header-text">
                <h1 class="text-xs font-bold tracking-tight text-gray-900 dark:text-white truncate">Hydrotech Predictive</h1>
                <span class="block text-[10px] text-gray-500 dark:text-slate-400 uppercase tracking-wider font-semibold">Inventory System</span>
            </div>
        </div>

        <button type="button" data-sidebar-toggle class="p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-[#1e2536] cursor-pointer lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

@if (auth()->user()->role === 'warehouse_staff')

    <a href="{{ route('inventory.view') }}" title="Inventory Management"
       class="{{ $linkBase }} {{ request()->routeIs('inventory.view') ? $linkActive : $linkIdle }}">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        <span class="sidebar-text">Inventory Management</span>
    </a>

@else

    <nav class="flex-1 px-3 py-5 space-y-1.5 whitespace-nowrap overflow-y-auto">

        <!-- Dashboard -->
        <a href="{{ route('home') }}" title="Dashboard"
           class="{{ $linkBase }} {{ request()->routeIs('home') ? $linkActive : $linkIdle }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="sidebar-text">Dashboard</span>
        </a>

        <!-- Point of Sale -->
        <a href="{{ route('pos') }}" title="Point of Sale"
           class="{{ $linkBase }} {{ request()->routeIs('pos') || request()->routeIs('checkout*') ? $linkActive : $linkIdle }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <span class="sidebar-text">Point of Sale</span>
        </a>

        <!-- Transaction History -->
        <a href="{{ route('transaction.history') }}" title="Transaction History"
           class="{{ $linkBase }} {{ request()->routeIs('transaction.history') ? $linkActive : $linkIdle }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            <span class="sidebar-text">Transaction History</span>
        </a>

        @if (in_array(auth()->user()->role, ['admin', 'manager']))
            <!-- Product Management -->
            <a href="{{ route('admin.products.index') }}" title="Product Management"
               class="{{ $linkBase }} {{ request()->routeIs('admin.products.*') ? $linkActive : $linkIdle }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                <span class="sidebar-text">Product Management</span>
            </a>

            <!-- EOQ Calibration -->
            <a href="{{ route('eoq.calibration') }}" title="EOQ Calibration"
                class="{{ $linkBase }} {{ request()->routeIs('eoq.calibration') ? $linkActive : $linkIdle }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span class="sidebar-text">EOQ Calibration</span>
            </a>

            <!-- Sales Records -->
            <a href="{{ route('sales.records') }}" title="Sales Records"
            class="{{ $linkBase }} {{ request()->routeIs('sales.*') ? $linkActive : $linkIdle }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="sidebar-text">Sales Records</span>
            </a>

            <!-- Categories -->
            <a href="{{ route('admin.categories.index') }}" title="Categories"
               class="{{ $linkBase }} {{ request()->routeIs('admin.categories.*') ? $linkActive : $linkIdle }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                <span class="sidebar-text">Categories</span>
            </a>
        @endif

        @if (auth()->user()->role === 'admin')
            <!-- User Management -->
            <a href="{{ route('admin.users.index') }}" title="User Management"
               class="{{ $linkBase }} {{ request()->routeIs('admin.users.*') ? $linkActive : $linkIdle }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span class="sidebar-text">User Management</span>
            </a>
        @endif

        @if (in_array(auth()->user()->role, ['admin', 'manager']))
            @php
                $pendingCount = \App\Models\StockMovement::where('status', 'pending')->count();
            @endphp

            <!-- Inventory Management -->
            <a href="{{ route('inventory.index') }}" title="Inventory Management"
               class="{{ $linkBase }} {{ request()->routeIs('inventory.index') ? $linkActive : $linkIdle }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span class="sidebar-text">Inventory Management</span>
            </a>

            <!-- Pending Approvals -->
            <a href="{{ route('inventory.approvals') }}" title="Pending Approvals"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 {{ request()->routeIs('inventory.approvals') ? $linkActive : $linkIdle }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="sidebar-text">Pending Approvals</span>
                </div>
                @if ($pendingCount > 0)
                    <span class="badge-count bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>

            <!-- Stock Logs -->
            <a href="{{ route('inventory.logs') }}" title="Stock Logs"
               class="{{ $linkBase }} {{ request()->routeIs('inventory.logs') ? $linkActive : $linkIdle }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="sidebar-text">Stock Logs</span>
            </a>

        @elseif (in_array(auth()->user()->role, ['warehouse_staff', 'staff']))
            <a href="{{ route('inventory.view') }}" title="Inventory Management"
               class="{{ $linkBase }} {{ request()->routeIs('inventory.view') ? $linkActive : $linkIdle }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span class="sidebar-text">Inventory Management</span>
            </a>

            <a href="{{ route('inventory.myRequests') }}" title="My Restock Requests"
            class="{{ $linkBase }} {{ request()->routeIs('inventory.myRequests') ? $linkActive : $linkIdle }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="sidebar-text">My Restock Requests</span>
            </a>
        @endif

    </nav>

@endif

    <!-- Sidebar Footer -->
    <div class="px-3 py-5 border-t border-gray-200 dark:border-[#1e2536] whitespace-nowrap space-y-2.5">
        <div class="px-2 sidebar-text">
            <p class="text-xs font-semibold text-gray-800 dark:text-slate-200 truncate">
                Hello, {{ auth()->user()->full_name ?? auth()->user()->name }}
            </p>
            <p class="text-[10px] text-blue-600 dark:text-blue-400 font-medium truncate">
                {{ $userSubtitle }}
            </p>
        </div>

        <button type="button" id="themeToggle" title="Toggle Theme"
                class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-slate-300 dark:hover:bg-[#161B26] dark:hover:text-white cursor-pointer">
            <svg class="h-5 w-5 flex-shrink-0 dark:hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg class="h-5 w-5 flex-shrink-0 hidden dark:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="sidebar-text dark:hidden">Dark Mode</span>
            <span class="sidebar-text hidden dark:inline">Light Mode</span>
        </button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Logout"
                    class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40 cursor-pointer">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="sidebar-text">Logout</span>
            </button>
        </form>
    </div>
</aside>