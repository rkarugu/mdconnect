<!-- Main Navigation -->
<li>
    <div class="space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-semibold leading-6 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-tachometer-alt flex-shrink-0 h-6 w-6"></i>
            Dashboard
        </a>

        <!-- Medical Workers -->
        <a href="{{ route('admin.medical_workers') }}" 
           class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-semibold leading-6 {{ request()->routeIs('admin.medical_workers') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-user-md flex-shrink-0 h-6 w-6"></i>
            Medical Workers
        </a>
    </div>
</li>

<!-- Settings Section -->
<li>
    <div class="space-y-1" x-data="{ settingsOpen: {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.settings.*') ? 'true' : 'false' }} }">
        <!-- Settings Header -->
        <button type="button" 
                class="flex items-center w-full gap-x-3 rounded-lg px-3 py-2 text-sm font-semibold leading-6 {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.settings.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                @click="settingsOpen = !settingsOpen">
            <i class="fas fa-cogs flex-shrink-0 h-6 w-6"></i>
            Settings
            <i class="fas fa-chevron-down ml-auto h-5 w-5 shrink-0" :class="{ 'transform rotate-180': settingsOpen }"></i>
        </button>

        <!-- Settings Submenu -->
        <div class="mt-1 space-y-1" x-show="settingsOpen" x-collapse>
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-semibold leading-6 pl-11 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-users flex-shrink-0 h-5 w-5"></i>
                Users
            </a>
            <a href="{{ route('admin.roles.index') }}"
               class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-semibold leading-6 pl-11 {{ request()->routeIs('admin.roles.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-shield-alt flex-shrink-0 h-5 w-5"></i>
                Roles
            </a>
            <a href="{{ route('admin.settings') }}"
               class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-semibold leading-6 pl-11 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-sliders-h flex-shrink-0 h-5 w-5"></i>
                System Settings
            </a>
        </div>
    </div>
</li>

<!-- Account Section -->
<li>
    <div class="space-y-1" x-data="{ accountOpen: {{ request()->routeIs('admin.profile', 'admin.change-password') ? 'true' : 'false' }} }">
        <!-- Account Header -->
        <button type="button"
                class="flex items-center w-full gap-x-3 rounded-lg px-3 py-2 text-sm font-semibold leading-6 {{ request()->routeIs('admin.profile', 'admin.change-password') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}"
                @click="accountOpen = !accountOpen">
            <i class="fas fa-user flex-shrink-0 h-6 w-6"></i>
            Account
            <i class="fas fa-chevron-down ml-auto h-5 w-5 shrink-0" :class="{ 'transform rotate-180': accountOpen }"></i>
        </button>

        <!-- Account Submenu -->
        <div class="mt-1 space-y-1" x-show="accountOpen" x-collapse>
            <a href="{{ route('admin.profile') }}"
               class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-semibold leading-6 pl-11 {{ request()->routeIs('admin.profile') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-id-card flex-shrink-0 h-5 w-5"></i>
                Profile
            </a>
            <a href="{{ route('admin.change-password') }}"
               class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-semibold leading-6 pl-11 {{ request()->routeIs('admin.change-password') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-key flex-shrink-0 h-5 w-5"></i>
                Change Password
            </a>
        </div>
    </div>
</li>

<!-- User Info -->
<li class="mt-auto">
    <div class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-400">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-800">
            <span class="text-sm font-medium text-gray-400">{{ substr(auth()->user()->name, 0, 1) }}</span>
        </div>
        <span class="sr-only">Your profile</span>
        <span aria-hidden="true">{{ auth()->user()->name }}</span>
    </div>
</li>
