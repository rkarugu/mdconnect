@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-semibold text-gray-900 mb-6">Dashboard</h1>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Users Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-blue-500 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ $totalUsers }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('users.index') }}" class="font-medium text-blue-600 hover:text-blue-500">View all users</a>
                </div>
            </div>
        </div>

        <!-- Medical Workers Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-md text-green-500 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Medical Workers</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ $totalMedicalWorkers }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('medical_workers.index') }}" class="font-medium text-blue-600 hover:text-blue-500">View all medical workers</a>
                </div>
            </div>
        </div>

        <!-- Specialties Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-stethoscope text-purple-500 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Medical Specialties</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ $totalSpecialties }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('medical_specialties.index') }}" class="font-medium text-blue-600 hover:text-blue-500">View all specialties</a>
                </div>
            </div>
        </div>

        <!-- Facility Documents Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-medical text-yellow-500 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Facility Documents</dt>
                            <dd class="text-3xl font-semibold text-gray-900">{{ $pendingDocuments }}</dd>
                            <dd class="text-sm text-gray-500 mt-1">{{ $pendingFacilitiesCount ?? 0 }} facilities need document review</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('medical_facilities.verification') }}" class="font-medium text-blue-600 hover:text-blue-500">Review facility documents</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Recent Users -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Users</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach($recentUsers as $user)
                <li class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $user->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Recent Medical Workers -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Medical Workers</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach($recentMedicalWorkers as $worker)
                <li class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $worker->name ?? ($worker->user->name ?? 'Medical Worker') }}</p>
                            <p class="text-sm text-gray-500">{{ $worker->specialty->name ?? 'No specialty data' }}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $worker->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($worker->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($worker->status) }}
                            </span>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Facilities Needing Document Verification -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Facilities Needing Verification</h3>
                <a href="{{ route('medical_facilities.verification') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View all
                </a>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse($recentFacilities->where('status', 'pending') as $facility)
                    <li class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $facility->facility_name }}</p>
                                <p class="text-sm text-gray-500">{{ $facility->facility_type }} in {{ $facility->city }}</p>
                            </div>
                            <div class="ml-4 flex-shrink-0 space-x-2 flex items-center">
                                @php
                                    $totalDocs = $facility->documents->count();
                                    $pendingDocs = $facility->documents->where('status', 'pending')->count();
                                    $verifiedDocs = $totalDocs - $pendingDocs;
                                @endphp
                                <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $totalDocs > 0 ? ($verifiedDocs / $totalDocs) * 100 : 0 }}%"></div>
                                </div>
                                <a href="{{ route('medical_facilities.documents', $facility) }}" 
                                   class="text-blue-600 hover:text-blue-900 text-xs">
                                    <i class="fas fa-file-alt"></i> {{ $verifiedDocs }}/{{ $totalDocs }}
                                </a>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-6 text-center text-gray-500">
                        No facilities need document verification
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
