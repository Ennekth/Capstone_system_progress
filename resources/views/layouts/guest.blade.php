<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full m-0 p-0 bg-white dark:bg-[#10141D] antialiased overflow-hidden"
      style="background-image: url('{{ asset('images/bg_design.svg') }}'); background-size: cover; background-position: center;">
    @yield('content')
    @stack('scripts')
</body>
</html>