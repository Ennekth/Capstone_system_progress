<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="flex min-h-screen">

        @include('layouts.partials.sidenav')

        <!-- Main content -->
        <div class="flex-1 flex flex-col">

            <!-- Topbar with the "open sidebar" button -->
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4">
                <label id="openSidebarBtn" for="sidebarToggle" class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </label>
                <h2 class="text-lg font-semibold">@yield('title', 'Dashboard')</h2>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>

        </div>

    </div>

</body>
</html>