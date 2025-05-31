{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h3 class="text-base font-semibold leading-6 text-gray-900">Create New User</h3>
            <p class="mt-1 text-sm text-gray-500">Add a new user to the system.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                <i class="fas fa-arrow-left -ml-0.5 mr-1.5"></i>
                Back to Users
            </a>
        </div>
    </div>

    @if ($errors->any())
    <div class="rounded-md bg-red-50 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6 p-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                <div class="mt-2">
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                <div class="mt-2">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">Phone Number</label>
                <div class="mt-2">
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required 
                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                </div>
            </div>

            <div>
                <label for="role_id" class="block text-sm font-medium leading-6 text-gray-900">Role</label>
                <div class="mt-2">
                    <select name="role_id" id="role_id" required 
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-x-4">
                <button type="button" onclick="window.location='{{ route('admin.users.index') }}'"
                        class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
