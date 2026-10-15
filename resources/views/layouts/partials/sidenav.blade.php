<!-- Sidebar -->
<aside id="sidebar" class="bg-gray-900 text-gray-100 flex flex-col ">

    <div class="px-6 py-5 border-b border-gray-800 whitespace-nowrap flex items-center justify-between">
        <h1 class="text-xl font-bold">Inventory System</h1>

        <button type="button" data-sidebar-toggle class="p-1 rounded hover:bg-gray-800 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

@if (auth()->user()->role === 'warehouse_staff')

    <a href="{{ route('inventory.view') }}"
       class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
              {{ request()->routeIs('inventory.view') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
        Inventory Management
    </a>

@else

    <nav class="flex-1 px-3 py-4 space-y-1 whitespace-nowrap overflow-y-auto">

        <a href="{{ route('home') }}"
           class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                  {{ request()->routeIs('home') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            Dashboard
        </a>

        <a href="{{ route('pos') }}"
           class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                  {{ request()->routeIs('pos') || request()->routeIs('checkout*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            Point of Sale
        </a>

        <a href="{{ route('transaction.history') }}"
           class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                  {{ request()->routeIs('transaction.history') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            Transaction History
        </a>

        @if (in_array(auth()->user()->role, ['admin', 'manager']))
            <a href="{{ route('admin.products.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.products.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                Product Management
            </a>

            <a href="{{ route('eoq.calibration') }}"
                class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                    {{ request()->routeIs('eoq.calibration') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                EOQ Calibration
            </a>

            <a href="{{ route('sales.records') }}"
            class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                    {{ request()->routeIs('sales.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                Sales Records
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.categories.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                Categories
            </a>
        @endif

        @if (auth()->user()->role === 'admin')
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                User Management
            </a>
        @endif

        @if (in_array(auth()->user()->role, ['admin', 'manager']))
            @php
                $pendingCount = \App\Models\StockMovement::where('status', 'pending')->count();
            @endphp

            <a href="{{ route('inventory.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('inventory.index') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                Inventory Management
            </a>

            <a href="{{ route('inventory.approvals') }}"
               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('inventory.approvals') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <span>Pending Approvals</span>
                @if ($pendingCount > 0)
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('inventory.logs') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('inventory.logs') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                Stock Logs
            </a>

        @elseif (in_array(auth()->user()->role, ['warehouse_staff', 'staff']))
            <a href="{{ route('inventory.view') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                      {{ request()->routeIs('inventory.view') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                Inventory Management
            </a>

            <a href="{{ route('inventory.myRequests') }}"
            class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                    {{ request()->routeIs('inventory.myRequests') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                My Restock Requests
            </a>

        @endif

    </nav>

@endif



    <div class="px-3 py-4 border-t border-gray-800 whitespace-nowrap">
        <p class="px-3 pb-2 text-xs text-gray-400 truncate">
            Hello, {{ auth()->user()->full_name }}
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-gray-300 bg-red-900 hover:bg-red-700 hover:text-white">
                Logout
            </button>
        </form>
    </div>
</aside>
