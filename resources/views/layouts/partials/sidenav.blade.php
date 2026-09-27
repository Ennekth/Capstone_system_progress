<!-- Hidden checkbox controls sidebar open/collapsed state -->
<input type="checkbox" id="sidebarToggle" class="peer hidden" />

<!-- Sidebar -->
<aside class="w-64 peer-checked:w-0 bg-gray-900 text-gray-100 flex flex-col transition-all duration-300 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-800 whitespace-nowrap flex items-center justify-between">
        <h1 class="text-xl font-bold">Inventory System</h1>

        <!-- Collapse button (inside sidebar) -->
        <label for="sidebarToggle" class="p-1 rounded hover:bg-gray-800 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </label>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 whitespace-nowrap">
        <a href="{{ route('home') }}"
           class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                  {{ request()->routeIs('home') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            Dashboard
        </a>

        @if (in_array(auth()->user()->role, ['admin', 'manager']))
            <a href="{{ route('admin.products.index') }}"
            class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                    {{ request()->routeIs('admin.products.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                Product Management
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
        
        <a href="{{ route('pos') }}"
           class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                  {{ request()->routeIs('pos') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            Point of Sale
        </a>

        {{-- <a href="{{ route('sales.import') }}"
           class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                  {{ request()->routeIs('sales.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            Import Sales
        </a> --}}

        <a href="{{ route('transaction.history') }}"
           class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                  {{ request()->routeIs('transaction.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            Transactions
        </a>

        {{-- <a href="{{ route('forecast') }}"
           class="flex items-center px-3 py-2 rounded-lg text-sm font-medium
                  {{ request()->routeIs('forecast') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            Demand Forecast
        </a> --}}
    </nav>

    <div class="px-3 py-4 border-t border-gray-800 whitespace-nowrap">
        <p class="px-3 pb-2 text-xs text-gray-400 truncate">
            Hello, {{ auth()->user()->name }}
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white">
                Logout
            </button>
        </form>
    </div>
</aside>