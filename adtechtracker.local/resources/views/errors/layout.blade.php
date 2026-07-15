<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @yield('js')

        <!-- css -->
        @yield('css')
    </head>
    <body class="relative min-h-screen bg-gray-100 dark:bg-gray-900">
    
        <a href="/" class="absolute left-8 top-6 text-gray-600 dark:text-gray-200 hover:text-indigo-500">
            {{ __('Back') }}
        </a>

        <main class="flex min-h-screen flex-col items-center justify-center">
            <div class="text-8xl font-bold text-gray-700 dark:text-gray-200">
                @yield('code')
            </div>

            <div class="mt-6 text-xl font-semibold text-gray-700 dark:text-gray-200">
                @yield('message')
            </div>
        </main>

    </body>
</html>
