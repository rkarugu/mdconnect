<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MediConnect') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Scripts -->
    <!-- Using CDN links instead of Vite to fix manifest error -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom styles -->
    <style>
        /* Add any custom styles here */
        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>
<body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen bg-gray-50">
        <!-- Page Wrapper -->
        <div class="flex">
            <!-- Sidebar -->
            @include('admin.sidebar')

            <!-- Main Content -->
            <div class="flex-1 min-w-0 flex flex-col lg:pl-64">
                <!-- Top Navigation -->
                <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 bg-white shadow">
                    <div class="flex flex-1 justify-between px-4">
                        <div class="flex flex-1">
                            <!-- Page Title -->
                            <h1 class="text-2xl font-semibold text-gray-900 my-auto">
                                @yield('title', 'Dashboard')
                            </h1>
                        </div>
                        <div class="ml-4 flex items-center md:ml-6">
                            <!-- User Dropdown -->
                            <div class="relative ml-3" x-data="{ open: false }">
                                <div>
                                    <button type="button" @click="open = !open" class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" id="user-menu-button">
                                        <span class="sr-only">Open user menu</span>
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gray-500">
                                            <span class="text-sm font-medium leading-none text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                        </span>
                                    </button>
                                </div>
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                                    <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <main class="flex-1 p-4">
                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="rounded-md bg-green-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button @click="show = false" class="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100">
                                        <span class="sr-only">Dismiss</span>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="rounded-md bg-red-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button @click="show = false" class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100">
                                        <span class="sr-only">Dismiss</span>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
