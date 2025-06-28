<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-30">
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b bg-white">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <img src="{{ asset('images/mediconnect_logo.svg') }}" class="h-10" alt="MediConnect Logo">
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden">
            <i class="fas fa-times text-gray-500"></i>
        </button>
    </div>
    
    <!-- Nav Links -->
    <div class="overflow-y-auto h-full pb-16">
        <nav class="mt-4 px-2 space-y-1">
            @if (Auth::user()->hasAnyRole(['super-admin', 'admin']))
                <!-- ================================= -->
                <!-- == ADMIN & SUPER ADMIN Sidebar == -->
                <!-- ================================= -->

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-tachometer-alt w-6 h-6 mr-3 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    Dashboard
                </a>

                <!-- Medical Workers Section -->
                <div x-data="{ open: {{ request()->routeIs('medical_workers.*', 'medical_specialties.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_workers.*', 'medical_specialties.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-user-md w-6 h-6 mr-3 {{ request()->routeIs('medical_workers.*', 'medical_specialties.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        <span class="flex-1">Medical Workers</span>
                        <i class="fas fa-chevron-down w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" class="mt-1 pl-6">
                        <a href="{{ route('medical_workers.index') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_workers.index') && !request()->has('status') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fas fa-list w-6 h-6 mr-3 {{ request()->routeIs('medical_workers.index') && !request()->has('status') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            All Workers
                        </a>
                        <!-- Add other worker links here -->
                    </div>
                </div>

                <!-- Medical Facilities Section -->
                <div x-data="{ open: {{ request()->routeIs('medical_facilities.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_facilities.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-hospital w-6 h-6 mr-3 {{ request()->routeIs('medical_facilities.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        <span class="flex-1">Medical Facilities</span>
                        <i class="fas fa-chevron-down w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" class="mt-1 pl-6">
                        <a href="{{ route('medical_facilities.index') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_facilities.index') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="fas fa-list-alt w-6 h-6 mr-3 {{ request()->routeIs('medical_facilities.index') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            All Facilities
                        </a>
                        <!-- Add other facility links here -->
                    </div>
                </div>

                <!-- Settings Menu -->
                <div x-data="{ open: {{ request()->routeIs('users.*', 'admin.roles.*', 'admin.settings.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('users.*', 'admin.roles.*', 'admin.settings.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-cogs w-6 h-6 mr-3 {{ request()->routeIs('users.*', 'admin.roles.*', 'admin.settings.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        <span class="flex-1">Settings</span>
                        <i class="fas fa-chevron-down w-5 h-5" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" class="mt-1 pl-6">
                        <!-- Add settings links here -->
                    </div>
                </div>

            @elseif (Auth::user()->hasRole('facility-admin'))
                <!-- ================================= -->
                <!-- ===== FACILITY ADMIN Sidebar ==== -->
                <!-- ================================= -->

                <!-- Facility Dashboard -->
                <a href="{{ route('facility.dashboard') }}" 
                   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('facility.dashboard') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-tachometer-alt w-6 h-6 mr-3 {{ request()->routeIs('facility.dashboard') ? 'text-blue-600' : 'text-gray-400' }}"></i>
                    Dashboard
                </a>

                <!-- Locum Shift Management -->
                <div x-data="{ open: {{ request()->routeIs('facility.locum-shifts.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" 
                            class="group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('facility.locum-shifts.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-calendar-alt w-6 h-6 mr-3 {{ request()->routeIs('facility.locum-shifts.*') ? 'text-blue-600' : 'text-gray-400' }}"></i>
                        <span class="flex-1">Locum Shifts</span>
                        <i class="fas fa-chevron-down w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" class="mt-1 pl-6 space-y-1">
                        <a href="{{ route('facility.locum-shifts.index') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('facility.locum-shifts.index') ? 'text-blue-600' : 'text-gray-600' }}">
                            <i class="fas fa-list-ul w-6 h-6 mr-3"></i> View Shifts
                        </a>
                        <a href="{{ route('facility.locum-shifts.create') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('facility.locum-shifts.create') ? 'text-blue-600' : 'text-gray-600' }}">
                            <i class="fas fa-plus-circle w-6 h-6 mr-3"></i> Create Shift
                        </a>
                    </div>
                </div>
            @endif

            <!-- ================================= -->
            <!-- ======== SHARED Sidebar ========= -->
            <!-- ================================= -->

            <!-- Account Menu -->
            <div x-data="{ open: {{ request()->routeIs('admin.profile', 'admin.change-password') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.profile', 'admin.change-password') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-user w-6 h-6 mr-3 {{ request()->routeIs('admin.profile', 'admin.change-password') ? 'text-blue-600' : 'text-gray-400' }}"></i>
                    <span class="flex-1">Account</span>
                    <i class="fas fa-chevron-down w-5 h-5" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" class="mt-1 pl-6 space-y-1">
                    <a href="{{ route('admin.profile') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.profile') ? 'text-blue-600' : 'text-gray-600' }}">
                        <i class="fas fa-id-card w-6 h-6 mr-3"></i> My Profile
                    </a>
                    <a href="{{ route('admin.change-password') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.change-password') ? 'text-blue-600' : 'text-gray-600' }}">
                        <i class="fas fa-key w-6 h-6 mr-3"></i> Change Password
                    </a>
                </div>
            </div>
        </nav>
    </div>
</aside>

<!-- Mobile menu button -->
<div class="lg:hidden fixed top-0 left-0 m-2 z-40">
    <button type="button" @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
        <span class="sr-only">Open sidebar</span>
        <i class="fas fa-bars"></i>
    </button>
</div>
