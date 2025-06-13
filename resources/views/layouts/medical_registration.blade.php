<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'MediConnect'))</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            body {
                font-family: 'Figtree', sans-serif;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center pt-6 bg-gray-100">
            <div class="mb-6">
                <a href="/" class="text-3xl font-bold text-indigo-600">
                    {{ config('app.name', 'MediConnect') }}
                </a>
            </div>

            <!-- Page Content -->
            <div class="w-full">
                @yield('content')
            </div>
            
            <!-- Footer -->
            <div class="mt-8 text-center text-gray-600 text-sm">
                <p>&copy; {{ date('Y') }} MediConnect. All rights reserved.</p>
            </div>
        </div>
    </body>
</html>
