@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-semibold text-gray-900 mb-6">My Profile</h1>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Picture Section -->
                <div class="md:col-span-1">
                    <div class="bg-gray-50 p-4 rounded-lg text-center">
                        <div class="mb-4">
                            @if($user->profile_picture)
                                <div class="w-40 h-40 rounded-full mx-auto overflow-hidden border-4 border-white shadow-lg">
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-40 h-40 rounded-full mx-auto bg-blue-100 flex items-center justify-center border-4 border-white shadow-lg">
                                    <span class="text-4xl font-bold text-blue-500">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-gray-500">{{ optional($user->role)->name ?? 'No Role Assigned' }}</p>
                        
                        <div class="mt-4">
                            <a href="{{ route('admin.change-password') }}" class="text-blue-600 hover:underline block mt-2">
                                <i class="fas fa-key mr-1"></i> Change Password
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Profile Information Form -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h3>
                    
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Role (Display Only) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <div class="shadow-sm block w-full sm:text-sm py-2 px-3 border border-gray-300 bg-gray-50 rounded-md">
                                    {{ optional($user->role)->name ?? 'No Role Assigned' }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Picture Upload -->
                        <div class="mb-4">
                            <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" 
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <p class="text-sm text-gray-500 mt-1">Accepted formats: JPG, PNG, GIF. Max size: 2MB.</p>
                            @error('profile_picture')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Information -->
    <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Account Information</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded">
                    <p class="text-sm font-medium text-gray-500">Account Created</p>
                    <p class="text-sm text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded">
                    <p class="text-sm font-medium text-gray-500">Last Updated</p>
                    <p class="text-sm text-gray-900">{{ $user->updated_at->format('F d, Y') }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded">
                    <p class="text-sm font-medium text-gray-500">Email Verified</p>
                    <p class="text-sm text-gray-900">
                        @if($user->email_verified_at)
                            <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i> {{ $user->email_verified_at->format('F d, Y') }}</span>
                        @else
                            <span class="text-red-600"><i class="fas fa-times-circle mr-1"></i> Not verified</span>
                        @endif
                    </p>
                </div>
                <div class="p-3 bg-gray-50 rounded">
                    <p class="text-sm font-medium text-gray-500">User ID</p>
                    <p class="text-sm text-gray-900">{{ $user->id }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
