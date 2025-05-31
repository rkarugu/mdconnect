@extends('layouts.app')

@section('title', $facility->facility_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Facility Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('medical_facilities.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Facilities
            </a>
            <a href="{{ route('medical_facilities.edit', $facility) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-edit mr-2"></i> Edit Facility
            </a>
        </div>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-lg shadow-sm mb-6 p-4 border-l-4 
        @if($facility->status == 'approved') border-green-500
        @elseif($facility->status == 'verified') border-blue-500
        @elseif($facility->status == 'pending') border-yellow-500
        @elseif($facility->status == 'rejected') border-red-500
        @else border-gray-500 @endif">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-medium">Registration Status</h2>
                <div class="mt-1 flex items-center">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($facility->status == 'approved') bg-green-100 text-green-800
                        @elseif($facility->status == 'verified') bg-blue-100 text-blue-800
                        @elseif($facility->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($facility->status == 'rejected') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($facility->status) }}
                    </span>
                    @if($facility->status_reason)
                        <span class="ml-2 text-sm text-gray-600">- {{ $facility->status_reason }}</span>
                    @endif
                </div>
            </div>
            @if($facility->status == 'approved')
                <div class="bg-green-100 text-green-800 p-2 rounded-lg">
                    <i class="fas fa-check-circle mr-1"></i> Your facility is approved and can post locum job requests.
                </div>
            @elseif($facility->status == 'verified')
                <div class="bg-blue-100 text-blue-800 p-2 rounded-lg">
                    <i class="fas fa-info-circle mr-1"></i> Your documents are verified. Waiting for final approval.
                </div>
            @elseif($facility->status == 'pending')
                <div class="bg-yellow-100 text-yellow-800 p-2 rounded-lg">
                    <i class="fas fa-clock mr-1"></i> Your registration is under review.
                </div>
            @elseif($facility->status == 'rejected')
                <div class="bg-red-100 text-red-800 p-2 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-1"></i> Your registration was rejected. Please contact support.
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Facility Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-800">Facility Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Facility Name</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->facility_name }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Facility Type</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->facility_type }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">License Number</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->license_number }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tax ID</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->tax_id ?? 'Not provided' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Bed Capacity</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->bed_capacity ?? 'Not specified' }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Description</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->description ?? 'No description provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-800">Contact Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Email Address</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->email }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Phone Number</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->phone }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Website</h3>
                            <p class="mt-1 text-base text-gray-900">
                                @if($facility->website)
                                    <a href="{{ $facility->website }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $facility->website }}
                                    </a>
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-800">Address Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Street Address</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->address }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">City</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->city }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">State/Province</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->state ?? 'Not provided' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Postal Code</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->postal_code }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Country</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $facility->country }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Facility Photo -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-800">Facility Photo</h2>
                </div>
                <div class="p-6 text-center">
                    @if($facility->facility_photo)
                        <img src="{{ asset('storage/' . $facility->facility_photo) }}" 
                            alt="{{ $facility->facility_name }}" 
                            class="w-full h-48 object-cover rounded">
                    @else
                        <div class="w-full h-48 bg-gray-200 rounded flex items-center justify-center">
                            <i class="fas fa-hospital text-4xl text-gray-400"></i>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">No facility photo uploaded</p>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-800">Uploaded Documents</h2>
                </div>
                <div class="p-4">
                    <ul class="divide-y divide-gray-200">
                        @forelse($facility->documents as $document)
                            <li class="py-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $document->title }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $document->document_number ? 'Number: ' . $document->document_number : '' }}
                                        </p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($document->status == 'approved') bg-green-100 text-green-800
                                            @elseif($document->status == 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($document->status) }}
                                        </span>
                                    </div>
                                    <a href="{{ route('medical_facilities.documents.preview', [$facility, $document]) }}" target="_blank" 
                                        class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                </div>
                                @if($document->status == 'rejected' && $document->rejection_reason)
                                    <div class="mt-2 text-xs text-red-500">
                                        <p><strong>Rejection reason:</strong> {{ $document->rejection_reason }}</p>
                                    </div>
                                @endif
                            </li>
                        @empty
                            <li class="py-3 text-center text-gray-500">No documents uploaded</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-800">Actions</h2>
                </div>
                <div class="p-4">
                    <div class="space-y-3">
                        <a href="{{ route('medical_facilities.edit', $facility) }}" 
                            class="w-full block text-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-edit mr-2"></i> Edit Facility
                        </a>
                        
                        @if($facility->status == 'approved')
                            <a href="{{ route('locum_jobs.create') }}" 
                                class="w-full block text-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                <i class="fas fa-plus mr-2"></i> Post New Job
                            </a>
                            
                            <a href="{{ route('locum_jobs.index', ['facility' => $facility->id]) }}" 
                                class="w-full block text-center px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                <i class="fas fa-briefcase mr-2"></i> View Job Listings
                            </a>
                        @endif
                        
                        <form action="{{ route('medical_facilities.destroy', $facility) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this facility? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                <i class="fas fa-trash mr-2"></i> Delete Facility
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
