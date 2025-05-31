<!-- Tailwind UI Admin Navbar -->
<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <img class="h-8 w-8 rounded-full" src="{{ asset('path-to-your-logo/logo.png') }}" alt="MediConnect Logo">
                    <span class="text-lg font-semibold text-gray-800">MediConnect</span>
                </a>
            </div>

            <!-- Main Links -->
            <div class="hidden md:flex space-x-6 items-center">
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary {{ request()->is('dashboard') ? 'text-primary font-semibold' : '' }}">
                    Dashboard
                </a>
            </div>

            <!-- Right Side: Authenticated User Dropdown -->
            <div class="flex items-center space-x-4">
                @auth
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-700 hover:text-primary focus:outline-none">
                        <i class="fas fa-user mr-1"></i> {{ Auth::user()->name }}
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50 py-1">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-cog mr-2"></i> Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
