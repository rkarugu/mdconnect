@extends('layouts.app')

@section('title', 'Admin - Verify Facility: ' . $medical_facility->facility_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Verify Medical Facility</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.medical_facilities.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Facilities
            </a>
        </div>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-lg shadow-sm mb-6 p-4 border-l-4 
        @if($medical_facility->status == 'approved') border-green-500
        @elseif($medical_facility->status == 'verified') border-blue-500
        @elseif($medical_facility->status == 'pending') border-yellow-500
        @elseif($medical_facility->status == 'rejected') border-red-500
        @else border-gray-500 @endif">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-medium">Current Status</h2>
                <div class="mt-1 flex items-center">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($medical_facility->status == 'approved') bg-green-100 text-green-800
                        @elseif($medical_facility->status == 'verified') bg-blue-100 text-blue-800
                        @elseif($medical_facility->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($medical_facility->status == 'rejected') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($medical_facility->status) }}
                    </span>
                    @if($medical_facility->status_reason)
                        <span class="ml-2 text-sm text-gray-600">- {{ $medical_facility->status_reason }}</span>
                    @endif
                </div>
            </div>
            
            <div class="flex space-x-2">
                @if($medical_facility->status == 'pending')
                    <form action="{{ route('admin.medical_facilities.verify', $medical_facility) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-check-circle mr-2"></i> Verify Facility
                        </button>
                    </form>
                    <form action="{{ route('admin.medical_facilities.verify_status', $medical_facility) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            <i class="fas fa-times-circle mr-2"></i> Reject
                        </button>
                    </form>
                @elseif($medical_facility->status == 'verified')
                    <form action="{{ route('admin.medical_facilities.approve', $medical_facility) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            <i class="fas fa-thumbs-up mr-2"></i> Approve
                        </button>
                    </form>
                    <form action="{{ route('admin.medical_facilities.reject', $medical_facility) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            <i class="fas fa-thumbs-down mr-2"></i> Reject
                        </button>
                    </form>
                @elseif($medical_facility->status == 'approved')
                    <form action="{{ route('admin.medical_facilities.suspend', $medical_facility) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                            <i class="fas fa-ban mr-2"></i> Suspend
                        </button>
                    </form>
                @elseif($medical_facility->status == 'rejected' || $medical_facility->status == 'suspended')
                    <form action="{{ route('admin.medical_facilities.reinstate', $medical_facility) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-redo mr-2"></i> Reinstate
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Update Form -->
    <div class="bg-white rounded-lg shadow-sm mb-6 p-4">
        <h2 class="text-lg font-medium mb-4">Update Status with Comment</h2>
        <form action="{{ route('admin.medical_facilities.verify_status', $medical_facility) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="verified" {{ old('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="status_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason/Comment</label>
                    <input type="text" name="status_reason" id="status_reason" value="{{ old('status_reason') }}" 
                        class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Update Status
                </button>
            </div>
        </form>
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
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->facility_name }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Facility Type</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->facility_type }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">License Number</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->license_number }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Tax ID</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->tax_id ?? 'Not provided' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Bed Capacity</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->bed_capacity ?? 'Not specified' }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Description</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->description ?? 'No description provided' }}</p>
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
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->email }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Phone Number</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->phone }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Website</h3>
                            <p class="mt-1 text-base text-gray-900">
                                @if($medical_facility->website)
                                    <a href="{{ $medical_facility->website }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $medical_facility->website }}
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
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->address }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">City</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->city }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">State/Province</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->state ?? 'Not provided' }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Postal Code</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->postal_code }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Country</h3>
                            <p class="mt-1 text-base text-gray-900">{{ $medical_facility->country }}</p>
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
                    @if($medical_facility->facility_photo)
                        <img src="{{ asset('storage/' . $medical_facility->facility_photo) }}" 
                            alt="{{ $medical_facility->facility_name }}" 
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
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-800">Uploaded Documents</h2>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.medical_facilities.documents', $medical_facility) }}" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                            <i class="fas fa-clipboard-check mr-1"></i> Verify Documents
                        </a>
                        <a href="{{ route('admin.medical_facilities.documents.upload', $medical_facility) }}" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                            <i class="fas fa-upload mr-1"></i> Upload Documents
                        </a>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            {{ $medical_facility->documents->count() }} Documents
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    @if($medical_facility->documents->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($medical_facility->documents as $document)
                                <div class="border rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
                                    <!-- Document Header -->
                                    <div class="flex items-center justify-between bg-gray-50 px-4 py-2 border-b">
                                        <div class="flex items-center">
                                            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                                @if(Str::contains($document->mime_type, 'pdf'))
                                                    <i class="fas fa-file-pdf text-blue-600"></i>
                                                @elseif(Str::contains($document->mime_type, 'image'))
                                                    <i class="fas fa-file-image text-blue-600"></i>
                                                @else
                                                    <i class="fas fa-file-alt text-blue-600"></i>
                                                @endif
                                            </span>
                                            <div>
                                                <h3 class="text-sm font-medium text-gray-900 truncate max-w-xs">{{ $document->title }}</h3>
                                                <p class="text-xs text-gray-500">{{ $document->document_type }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($document->status == 'approved') bg-green-100 text-green-800
                                            @elseif($document->status == 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($document->status) }}
                                        </span>
                                    </div>
                                    
                                    <!-- Document Body -->
                                    <div class="p-4">
                                        @if($document->document_number)
                                            <div class="mb-2">
                                                <p class="text-xs font-medium text-gray-600">Document Number</p>
                                                <p class="text-sm">{{ $document->document_number }}</p>
                                            </div>
                                        @endif
                                        
                                        <div class="mb-2">
                                            <p class="text-xs font-medium text-gray-600">Uploaded On</p>
                                            <p class="text-sm">{{ $document->created_at->format('M d, Y') }}</p>
                                        </div>
                                        
                                        @if($document->status == 'rejected' && $document->rejection_reason)
                                            <div class="mt-2 p-2 bg-red-50 border border-red-100 rounded">
                                                <p class="text-xs font-medium text-red-800">Rejection Reason:</p>
                                                <p class="text-xs text-red-700">{{ $document->rejection_reason }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Document Actions -->
                                    <div class="border-t px-4 py-3 bg-gray-50 flex justify-between">
                                        <a href="{{ route('admin.medical_facilities.documents.preview', [$medical_facility, $document]) }}" 
                                           target="_blank" 
                                           class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                            <i class="fas fa-eye mr-1"></i> View Document
                                        </a>
                                        
                                        @if($document->status == 'pending')
                                            <div class="flex space-x-2">
                                                <form action="{{ route('admin.medical_facilities.documents.verify', [$medical_facility, $document]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">
                                                        <i class="fas fa-check-circle mr-1"></i> Approve
                                                    </button>
                                                </form>
                                                
                                                <button type="button" 
                                                        onclick="document.getElementById('reject-document-{{ $document->id }}').classList.toggle('hidden')" 
                                                        class="text-red-600 hover:text-red-800 text-xs font-medium">
                                                    <i class="fas fa-times-circle mr-1"></i> Reject
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Rejection Form (Hidden by default) -->
                                    <div id="reject-document-{{ $document->id }}" class="hidden border-t p-4 bg-gray-50">
                                        <form action="{{ route('admin.medical_facilities.documents.verify', [$medical_facility, $document]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <div class="mb-2">
                                                <label for="rejection_reason_{{ $document->id }}" class="block text-xs font-medium text-gray-700 mb-1">Reason for Rejection</label>
                                                <textarea name="rejection_reason" id="rejection_reason_{{ $document->id }}" rows="2" 
                                                    class="w-full text-sm rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                                    required placeholder="Explain why this document is being rejected"></textarea>
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="button" 
                                                        onclick="document.getElementById('reject-document-{{ $document->id }}').classList.toggle('hidden')" 
                                                        class="px-3 py-1 border border-gray-300 text-gray-700 text-xs rounded mr-2 hover:bg-gray-100">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                    Confirm Rejection
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500">No documents have been uploaded for this facility</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Verification History -->
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-800">Verification History</h2>
                </div>
                <div class="p-4">
                    <ul class="divide-y divide-gray-200">
                        <!-- Status changes history -->
                        @if($medical_facility->last_status_change)
                            <li class="py-3">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full
                                            @if($medical_facility->status == 'approved') bg-green-500
                                            @elseif($medical_facility->status == 'verified') bg-blue-500
                                            @elseif($medical_facility->status == 'rejected') bg-red-500
                                            @elseif($medical_facility->status == 'suspended') bg-yellow-500
                                            @else bg-gray-500 @endif">
                                            <i class="fas fa-history text-white"></i>
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            Status changed to <strong>{{ ucfirst($medical_facility->status) }}</strong>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $medical_facility->last_status_change->format('M d, Y \a\t h:i A') }}</p>
                                        @if($medical_facility->status_reason)
                                            <p class="mt-1 text-xs text-gray-600">{{ $medical_facility->status_reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endif
                        
                        @if($medical_facility->approved_at && $medical_facility->status != 'approved')
                            <li class="py-3">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-green-500">
                                            <i class="fas fa-check-circle text-white"></i>
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            Status changed to <strong>Approved</strong>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $medical_facility->approved_at->format('M d, Y \a\t h:i A') }}</p>
                                    </div>
                                </div>
                            </li>
                        @endif
                        
                        @if($medical_facility->verified_at && $medical_facility->status != 'verified' && $medical_facility->status != 'approved')
                            <li class="py-3">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-500">
                                            <i class="fas fa-check text-white"></i>
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            Status changed to <strong>Verified</strong>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $medical_facility->verified_at->format('M d, Y \a\t h:i A') }}</p>
                                    </div>
                                </div>
                            </li>
                        @endif
                        
                        <li class="py-3">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-500">
                                        <i class="fas fa-plus-circle text-white"></i>
                                    </span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">
                                        <strong>Facility Registered</strong>
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $medical_facility->created_at->format('M d, Y \a\t h:i A') }}</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
