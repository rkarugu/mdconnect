@extends('layouts.app')

@section('title', 'Users Management')

@section('content')
<div class="sm:flex sm:items-center sm:justify-between mb-6">
    <div>
        <h3 class="text-base font-semibold leading-6 text-gray-900">Users</h3>
        <p class="mt-1 text-sm text-gray-500">A categorized list of all users in the system.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="{{ route('users.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            <i class="fas fa-plus -ml-0.5 mr-1.5"></i>
            Add User
        </a>
    </div>
</div>

<div x-data="{ tab: 'system' }">
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="#" @click.prevent="tab = 'system'" :class="{ 'border-blue-500 text-blue-600': tab === 'system', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'system' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                System Users
            </a>
            <a href="#" @click.prevent="tab = 'facility'" :class="{ 'border-blue-500 text-blue-600': tab === 'facility', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'facility' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Facility Admins
            </a>
            <a href="#" @click.prevent="tab = 'medical'" :class="{ 'border-blue-500 text-blue-600': tab === 'medical', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'medical' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Medical Workers
            </a>
            <a href="#" @click.prevent="tab = 'other'" :class="{ 'border-blue-500 text-blue-600': tab === 'other', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'other' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Other Users
            </a>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="mt-8">
        {{-- System Users Table --}}
        <div x-show="tab === 'system'" x-cloak>
            @include('admin.users.partials.user_table', ['users' => $systemUsers, 'title' => 'System Users'])
        </div>

        {{-- Facility Admins Table --}}
        <div x-show="tab === 'facility'" x-cloak>
            @include('admin.users.partials.user_table', ['users' => $facilityAdmins, 'title' => 'Facility Admins'])
        </div>

        {{-- Medical Workers Table --}}
        <div x-show="tab === 'medical'" x-cloak>
            @include('admin.users.partials.user_table', ['users' => $medicalWorkers, 'title' => 'Medical Workers'])
        </div>

        {{-- Other Users Table --}}
        <div x-show="tab === 'other'" x-cloak>
            @include('admin.users.partials.user_table', ['users' => $otherUsers, 'title' => 'Other Users'])
        </div>
    </div>
</div>
@endsection
