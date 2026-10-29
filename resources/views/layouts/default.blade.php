<!DOCTYPE html>
<html lang="en" class="{{ auth()->check() && auth()->user()->theme === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Runs immediately, before the page paints — sets the correct
        // sidebar state with no animation, so there's nothing to "slide."
        (function () {
            var isDesktop = window.innerWidth >= 1024;
            if (isDesktop) {
                document.documentElement.classList.add('sidebar-open');
            }
        })();
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div id="sidebarOverlay"></div>

    <div class="flex min-h-screen">

        @include('layouts.partials.sidenav')

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Topbar -->
            <header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
                <button type="button" data-sidebar-toggle class="p-2 rounded-lg hover:bg-gray-100 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <h2 class="text-base sm:text-lg font-semibold truncate">@yield('title', 'Dashboard')</h2>
            </header>

            <!-- Page content -->
            <main class="flex-1 p-4 sm:p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.documentElement;

            function isDesktop() {
                return window.innerWidth >= 1024;
            }

            document.querySelectorAll('[data-sidebar-toggle]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    root.classList.toggle('sidebar-open');
                });
            });

            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                overlay.addEventListener('click', function () {
                    root.classList.remove('sidebar-open');
                });
            }

            let wasDesktop = isDesktop();
            window.addEventListener('resize', function () {
                const nowDesktop = isDesktop();
                if (nowDesktop !== wasDesktop) {
                    root.classList.toggle('sidebar-open', nowDesktop);
                    wasDesktop = nowDesktop;
                }
            });

            // Enable transitions only after the initial state is set and painted,
            // so page loads never animate — only user-triggered toggles do.
            requestAnimationFrame(function () {
                root.classList.add('sidebar-ready');
            });

            const themeBtn = document.getElementById('themeToggle');
                if (themeBtn) {
                    themeBtn.addEventListener('click', function () {
                        window.dispatchEvent(new Event('themechange')); 
                        const isDark = document.documentElement.classList.toggle('dark');

                        fetch("{{ route('theme.update') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ theme: isDark ? 'dark' : 'light' })
                        }).catch(function () {});
                    });
                }

        });
    </script>

    @stack('scripts')
</body>
</html>