@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-semibold text-gray-900 mb-6">
        <i class="fas fa-users-medical mr-3"></i> Patient Management Dashboard
    </h1>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Total Patients Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-blue-500 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Patients</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_patients']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.patients.list') }}" class="font-medium text-blue-600 hover:text-blue-500">View all patients</a>
                </div>
            </div>
        </div>

        <!-- Active Patients Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-check text-green-500 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Patients</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['active_patients']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.patients.list', ['active' => 1]) }}" class="font-medium text-blue-600 hover:text-blue-500">View active patients</a>
                </div>
            </div>
        </div>

        <!-- New Today Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-plus text-yellow-500 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">New Today</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['new_patients_today']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.patients.list', ['today' => 1]) }}" class="font-medium text-blue-600 hover:text-blue-500">View new patients</a>
                </div>
            </div>
        </div>

        <!-- Unverified Patients Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-times text-red-500 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Unverified</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ number_format($stats['unverified_patients']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.patients.list', ['verified' => 0]) }}" class="font-medium text-blue-600 hover:text-blue-500">View unverified</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Patients -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                    <i class="fas fa-clock mr-2"></i> Recent Patients
                </h3>
                <a href="{{ route('admin.patients.list') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View all
                </a>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($recent_patients as $patient)
                <li class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($patient->profile_picture)
                                <img src="{{ $patient->profile_picture }}" 
                                     class="h-10 w-10 rounded-full">
                            @else
                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium text-sm">
                                    {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $patient->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $patient->email }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex items-center space-x-2">
                            @if($patient->email_verified_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Unverified
                                </span>
                            @endif
                            <a href="{{ route('admin.patients.show', $patient) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </li>
                @empty
                <li class="px-6 py-6 text-center text-gray-500">
                    No patients found
                </li>
                @endforelse
            </ul>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                    <i class="fas fa-bolt mr-2"></i> Quick Actions
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <a href="{{ route('admin.patients.create') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-user-plus mr-2"></i> Add New Patient
                    </a>
                    <a href="{{ route('admin.patients.list') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-list mr-2"></i> View All Patients
                    </a>
                    <a href="{{ route('admin.patients.analytics') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        <i class="fas fa-chart-bar mr-2"></i> Analytics
                    </a>
                    <a href="{{ route('admin.patients.export') }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                        <i class="fas fa-download mr-2"></i> Export Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
