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
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-1 pl-6">
                    <a href="{{ route('medical_workers.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_workers.index') && !request()->has('status') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-list w-6 h-6 mr-3 {{ request()->routeIs('medical_workers.index') && !request()->has('status') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        All Workers
                    </a>
                    <a href="{{ route('medical_workers.create') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_workers.create') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-user-plus w-6 h-6 mr-3 {{ request()->routeIs('medical_workers.create') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Add Worker
                    </a>
                    <a href="{{ route('medical_workers.verification') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_workers.verification') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-check-circle w-6 h-6 mr-3 {{ request()->routeIs('medical_workers.verification') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Verification
                    </a>
                    <a href="{{ route('medical_specialties.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_specialties.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-stethoscope w-6 h-6 mr-3 {{ request()->routeIs('medical_specialties.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Specialties
                    </a>
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
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-1 pl-6">
                    <a href="{{ route('medical_facilities.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_facilities.index') && !request()->has('status') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-list w-6 h-6 mr-3 {{ request()->routeIs('medical_facilities.index') && !request()->has('status') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        All Facilities
                    </a>
                    <a href="{{ route('medical_facilities.create') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_facilities.create') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-plus-circle w-6 h-6 mr-3 {{ request()->routeIs('medical_facilities.create') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Register Facility
                    </a>
                    <a href="{{ route('medical_facilities.verification') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_facilities.verification') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-clipboard-check w-6 h-6 mr-3 {{ request()->routeIs('medical_facilities.verification') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Verification
                    </a>
                    <a href="{{ route('medical_facilities.index', ['status' => 'pending']) }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_facilities.index') && request()->get('status') == 'pending' ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-clock w-6 h-6 mr-3 {{ request()->routeIs('medical_facilities.index') && request()->get('status') == 'pending' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Pending
                    </a>
                    <a href="{{ route('medical_facilities.index', ['status' => 'verified']) }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_facilities.index') && request()->get('status') == 'verified' ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-check w-6 h-6 mr-3 {{ request()->routeIs('medical_facilities.index') && request()->get('status') == 'verified' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Verified
                    </a>
                    <a href="{{ route('medical_facilities.index', ['status' => 'approved']) }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('medical_facilities.index') && request()->get('status') == 'approved' ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-thumbs-up w-6 h-6 mr-3 {{ request()->routeIs('medical_facilities.index') && request()->get('status') == 'approved' ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Approved
                    </a>
                </div>
            </div>

            <!-- Settings Menu -->
            <div x-data="{ open: {{ request()->routeIs('users.*', 'admin.roles.*', 'admin.settings.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('users.*', 'admin.roles.*', 'admin.settings.*', 'email.test') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-cogs w-6 h-6 mr-3 {{ request()->routeIs('users.*', 'admin.roles.*', 'admin.settings.*', 'email.test') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span class="flex-1">Settings</span>
                    <i class="fas fa-chevron-down w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-1 pl-6">
                    <a href="{{ route('users.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('users.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-users w-6 h-6 mr-3 {{ request()->routeIs('users.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Users
                    </a>
                    <a href="{{ route('roles.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('roles.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-user-tag w-6 h-6 mr-3 {{ request()->routeIs('roles.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Roles
                    </a>
                    <a href="{{ route('settings') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('settings') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-sliders-h w-6 h-6 mr-3 {{ request()->routeIs('settings') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        System Settings
                    </a>
                    <a href="{{ route('email.test') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('email.test') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-envelope w-6 h-6 mr-3 {{ request()->routeIs('email.test') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Email Settings
                    </a>
                </div>
            </div>

            <!-- Account Menu -->
            <div x-data="{ open: {{ request()->routeIs('admin.profile', 'admin.change-password') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.profile', 'admin.change-password') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-user w-6 h-6 mr-3 {{ request()->routeIs('admin.profile', 'admin.change-password') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span class="flex-1">Account</span>
                    <i class="fas fa-chevron-down w-5 h-5 transform transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="mt-1 pl-6">
                    <a href="{{ route('admin.profile') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.profile') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-id-card w-6 h-6 mr-3 {{ request()->routeIs('admin.profile') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        My Profile
                    </a>
                    <a href="{{ route('admin.change-password') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.change-password') ? 'bg-gray-100 text-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fas fa-key w-6 h-6 mr-3 {{ request()->routeIs('admin.change-password') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                        Change Password
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
