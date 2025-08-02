@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-900">
            <i class="fas fa-users mr-3"></i> Patient Management
        </h1>
        <a href="{{ route('admin.patients.create') }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Add New Patient
        </a>
    </div>

    <!-- Patient List -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium leading-6 text-gray-900">All Patients</h3>
        </div>
        <div class="overflow-hidden">
            <ul class="divide-y divide-gray-200">
                @forelse($patients ?? [] as $patient)
                <li class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($patient->profile_picture ?? false)
                                    <img src="{{ $patient->profile_picture }}" 
                                         class="h-10 w-10 rounded-full">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium text-sm">
                                        {{ strtoupper(substr($patient->first_name ?? 'P', 0, 1) . substr($patient->last_name ?? 'P', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">{{ $patient->full_name ?? ($patient->first_name . ' ' . $patient->last_name) }}</p>
                                <p class="text-sm text-gray-500">{{ $patient->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($patient->email_verified_at ?? false)
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
                <li class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-users fa-3x mb-4 text-gray-300"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No patients found</h3>
                    <p class="text-gray-500 mb-4">Get started by adding your first patient.</p>
                    <a href="{{ route('admin.patients.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i> Add First Patient
                    </a>
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
