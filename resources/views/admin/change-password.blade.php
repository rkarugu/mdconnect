@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.profile') }}" class="text-blue-600 hover:text-blue-800 mr-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-semibold text-gray-900">Change Password</h1>
    </div>

    @if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 bg-white border-b border-gray-200">
            <form method="POST" action="{{ route('admin.change-password.update') }}">
                @csrf

                <div class="space-y-6">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" id="current_password" required 
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" id="password" required 
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <p class="text-sm text-gray-500 mt-1">Password must be at least 8 characters.</p>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required 
                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        @error('password_confirmation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Security Note -->
                    <div class="rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Password Security Tips</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Use a combination of letters, numbers, and symbols</li>
                                        <li>Avoid using easily guessable information</li>
                                        <li>Use a different password for each of your accounts</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-lock mr-2"></i> Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
