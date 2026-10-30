<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 " 
style="background-image: url('{{ asset('images/bg_design.svg') }}');">

    <div class="min-h-screen flex items-center justify-center px-4">
        @yield('content')
    </div>
@stack('scripts')
</body>
</html>