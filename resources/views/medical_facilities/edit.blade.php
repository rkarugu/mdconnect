@extends('layouts.app')

@section('title', 'Edit ' . $facility->facility_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Medical Facility</h1>
        <div class="flex space-x-2">
            <a href="{{ route('medical_facilities.show', $facility) }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Details
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form action="{{ route('medical_facilities.update', $facility) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <!-- Facility Information -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Facility Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="facility_name" class="block text-sm font-medium text-gray-700 mb-1">Facility Name *</label>
                        <input type="text" name="facility_name" id="facility_name" value="{{ old('facility_name', $facility->facility_name) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('facility_name') border-red-500 @enderror" required>
                        @error('facility_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="facility_type" class="block text-sm font-medium text-gray-700 mb-1">Facility Type *</label>
                        <select name="facility_type" id="facility_type" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('facility_type') border-red-500 @enderror" required>
                            <option value="">Select Facility Type</option>
                            <option value="Hospital" {{ old('facility_type', $facility->facility_type) == 'Hospital' ? 'selected' : '' }}>Hospital</option>
                            <option value="Clinic" {{ old('facility_type', $facility->facility_type) == 'Clinic' ? 'selected' : '' }}>Clinic</option>
                            <option value="Laboratory" {{ old('facility_type', $facility->facility_type) == 'Laboratory' ? 'selected' : '' }}>Laboratory</option>
                            <option value="Pharmacy" {{ old('facility_type', $facility->facility_type) == 'Pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                            <option value="Nursing Home" {{ old('facility_type', $facility->facility_type) == 'Nursing Home' ? 'selected' : '' }}>Nursing Home</option>
                            <option value="Rehabilitation Center" {{ old('facility_type', $facility->facility_type) == 'Rehabilitation Center' ? 'selected' : '' }}>Rehabilitation Center</option>
                            <option value="Other" {{ old('facility_type', $facility->facility_type) == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('facility_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1">License Number *</label>
                        <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $facility->license_number) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('license_number') border-red-500 @enderror" required>
                        @error('license_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">Tax ID / Registration Number</label>
                        <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $facility->tax_id) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('tax_id') border-red-500 @enderror">
                        @error('tax_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bed_capacity" class="block text-sm font-medium text-gray-700 mb-1">Bed Capacity</label>
                        <input type="number" name="bed_capacity" id="bed_capacity" value="{{ old('bed_capacity', $facility->bed_capacity) }}" min="0" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('bed_capacity') border-red-500 @enderror">
                        @error('bed_capacity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Facility Description</label>
                        <textarea name="description" id="description" rows="3" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description', $facility->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Contact Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $facility->email) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $facility->phone) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('phone') border-red-500 @enderror" required>
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url" name="website" id="website" value="{{ old('website', $facility->website) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('website') border-red-500 @enderror">
                        @error('website')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Address Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
                        <input type="text" name="address" id="address" value="{{ old('address', $facility->address) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('address') border-red-500 @enderror" required>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $facility->city) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('city') border-red-500 @enderror" required>
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                        <input type="text" name="state" id="state" value="{{ old('state', $facility->state) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('state') border-red-500 @enderror">
                        @error('state')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code *</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $facility->postal_code) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('postal_code') border-red-500 @enderror" required>
                        @error('postal_code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <input type="text" name="country" id="country" value="{{ old('country', $facility->country) }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('country') border-red-500 @enderror" required>
                        @error('country')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Facility Photo -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Facility Photo</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="facility_photo" class="block text-sm font-medium text-gray-700 mb-1">Update Facility Photo</label>
                        <input type="file" name="facility_photo" id="facility_photo" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('facility_photo') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG (Max: 5MB)</p>
                        @error('facility_photo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        @if($facility->facility_photo)
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Photo</label>
                            <div class="mt-1 relative">
                                <img src="{{ asset('storage/' . $facility->facility_photo) }}" alt="{{ $facility->facility_name }}" class="h-32 w-auto object-cover rounded">
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="remove_photo" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600">Remove current photo</span>
                                    </label>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No photo currently uploaded</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Additional Documents -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Additional Documents</h2>
                <p class="text-sm text-gray-600 mb-4">Upload new documents or replace existing ones. Leave empty to keep current documents.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="documents.additional" class="block text-sm font-medium text-gray-700 mb-1">Upload Additional Document</label>
                        <input type="file" name="documents[additional]" id="documents.additional" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('documents.additional') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (Max: 10MB)</p>
                        @error('documents.additional')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="document_title" class="block text-sm font-medium text-gray-700 mb-1">Document Title</label>
                        <input type="text" name="document_title" id="document_title" value="{{ old('document_title') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('document_title') border-red-500 @enderror">
                        @error('document_title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Current Documents -->
                @if($facility->documents->count() > 0)
                    <div class="mt-4">
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Current Documents</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <ul class="divide-y divide-gray-200">
                                @foreach($facility->documents as $document)
                                    <li class="py-3 flex justify-between items-center">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $document->title }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ $document->document_number ? 'Number: ' . $document->document_number : '' }}
                                                <span class="inline-flex items-center ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($document->status == 'approved') bg-green-100 text-green-800
                                                    @elseif($document->status == 'rejected') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($document->status) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('medical_facilities.documents.preview', [$facility, $document]) }}" target="_blank" 
                                                class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </a>
                                            <button type="button" onclick="if(confirm('Are you sure you want to remove this document?')) document.getElementById('remove_doc_{{ $document->id }}').submit();" 
                                                class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                <i class="fas fa-trash mr-1"></i> Remove
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Facility
                </button>
            </div>
        </form>
    </div>
</div>

@foreach($facility->documents as $document)
    <form id="remove_doc_{{ $document->id }}" action="{{ route('medical_facilities.documents.destroy', [$facility, $document]) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection
