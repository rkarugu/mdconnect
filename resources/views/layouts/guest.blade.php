<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MediConnect') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <!-- Using CDN links instead of Vite to fix manifest error -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            /* Add any custom styles here */
            body {
                font-family: 'Figtree', sans-serif;
            }
            
            /* Enhanced login page styles */
            .login-container {
                backdrop-filter: blur(10px);
            }
            
            /* Smooth animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .animate-fadeInUp {
                animation: fadeInUp 0.6s ease-out;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 to-indigo-100">
            <div class="mb-8">
                <a href="/" class="block">
                    <x-application-logo class="w-32 h-32 sm:w-40 sm:h-40 mx-auto drop-shadow-lg hover:scale-105 transition-transform duration-300" />
                </a>
                <div class="text-center mt-4">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">MediConnect</h1>
                    <p class="text-gray-600 text-sm sm:text-base">Healthcare Management Platform</p>
                </div>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-xl overflow-hidden sm:rounded-xl border border-gray-100">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
