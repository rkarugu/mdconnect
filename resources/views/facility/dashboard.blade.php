@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Facility Dashboard</h1>
        <p class="mt-2 text-gray-600">Welcome back, {{ Auth::user()->name }}!</p>
    </div>

    <!-- Welcome Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-8">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Welcome to Your Dashboard</h2>
        <p class="text-gray-600">
            This is your central hub for managing your facility's operations on MediConnect. From here, you can manage locum shifts, view applications, and keep your facility's information up to date.
        </p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Example Stat Card -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700">Open Shifts</h3>
            <div class="text-3xl font-bold text-blue-600">{{ $openShiftsCount }}</div>
            <p class="text-gray-500 mt-1">Currently active and awaiting applicants.</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700">Pending Applications</h3>
            <div class="text-3xl font-bold text-yellow-600">{{ $pendingApplicationsCount }}</div>
            <p class="text-gray-500 mt-1">Require your review.</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700">Filled Shifts</h3>
            <div class="text-3xl font-bold text-green-600">{{ $filledShiftsCount }}</div>
            <p class="text-gray-500 mt-1">In the last 30 days.</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Quick Actions</h2>
        <div class="flex space-x-4">
            <a href="{{ route('facility.locum-shifts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                <i class="fas fa-plus-circle mr-2"></i>Create New Shift
            </a>
            <a href="{{ route('facility.locum-shifts.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                <i class="fas fa-list-alt mr-2"></i>View All Shifts
            </a>
        </div>
    </div>
</div>
@endsection