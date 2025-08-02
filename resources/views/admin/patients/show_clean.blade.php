@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">
                <i class="fas fa-user-medical mr-3"></i> Patient Details
            </h1>
            <p class="text-gray-600 mt-1">{{ $patient->full_name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.patients.edit', $patient) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i> Edit Patient
            </a>
            <a href="{{ route('admin.patients.list') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>
    </div>
    <!-- Patient Profile Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="text-center">
                @if($patient->profile_picture)
                    <img class="h-24 w-24 rounded-full mx-auto" 
                         src="{{ $patient->profile_picture }}" 
                         alt="Patient profile picture">
                @else
                    <div class="h-24 w-24 rounded-full bg-blue-500 flex items-center justify-center mx-auto text-white text-2xl font-medium">
                        {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h3 class="text-xl font-semibold text-center mt-4">{{ $patient->full_name }}</h3>

            <div class="text-center mt-2">
                @if($patient->email_verified_at)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Verified Patient
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Unverified
                    </span>
                @endif
            </div>

            <div class="mt-6 space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="font-medium text-gray-900">Patient ID</span>
                    <span class="text-gray-600">{{ $patient->id }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="font-medium text-gray-900">Age</span>
                    <span class="text-gray-600">{{ $patient->age ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="font-medium text-gray-900">Gender</span>
                    <span class="text-gray-600">{{ $patient->gender ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="font-medium text-gray-900">Blood Type</span>
                    <span>
                        @if($patient->blood_type)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $patient->blood_type }}
                            </span>
                        @else
                            <span class="text-gray-600">N/A</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-200">
                    <span class="font-medium text-gray-900">Member Since</span>
                    <span class="text-gray-600">{{ $patient->created_at->format('M Y') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="font-medium text-gray-900">Last Login</span>
                    <span class="text-gray-600">
                        {{ $patient->last_login_at ? $patient->last_login_at->diffForHumans() : 'Never' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Patient Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-address-book mr-2"></i> Contact Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->address ?? 'N/A' }}</p>
                    </div>
                </div>
            
            <!-- Medical Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    <i class="fas fa-heartbeat mr-2"></i> Medical Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Medical Conditions</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->medical_conditions ?? 'None reported' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Allergies</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $patient->allergies ?? 'None reported' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Contact</label>
                        <p class="mt-1 text-sm text-gray-900">
                            {{ $patient->emergency_contact_name ?? 'N/A' }}
                            @if($patient->emergency_contact_phone)
                                <br><span class="text-gray-600">{{ $patient->emergency_contact_phone }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
