@extends('layouts.app')

@section('title', 'Admin - Register Medical Facility')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Register Medical Facility</h1>
        <a href="{{ route('medical_facilities.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Facilities
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form action="{{ route('medical_facilities.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf

            <!-- Facility Information -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Facility Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="facility_name" class="block text-sm font-medium text-gray-700 mb-1">Facility Name *</label>
                        <input type="text" name="facility_name" id="facility_name" value="{{ old('facility_name') }}" 
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
                            <option value="Hospital" {{ old('facility_type') == 'Hospital' ? 'selected' : '' }}>Hospital</option>
                            <option value="Clinic" {{ old('facility_type') == 'Clinic' ? 'selected' : '' }}>Clinic</option>
                            <option value="Laboratory" {{ old('facility_type') == 'Laboratory' ? 'selected' : '' }}>Laboratory</option>
                            <option value="Pharmacy" {{ old('facility_type') == 'Pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                            <option value="Nursing Home" {{ old('facility_type') == 'Nursing Home' ? 'selected' : '' }}>Nursing Home</option>
                            <option value="Rehabilitation Center" {{ old('facility_type') == 'Rehabilitation Center' ? 'selected' : '' }}>Rehabilitation Center</option>
                            <option value="Other" {{ old('facility_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('facility_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1">License Number *</label>
                        <input type="text" name="license_number" id="license_number" value="{{ old('license_number') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('license_number') border-red-500 @enderror" required>
                        @error('license_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">Tax ID / Registration Number</label>
                        <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('tax_id') border-red-500 @enderror">
                        @error('tax_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bed_capacity" class="block text-sm font-medium text-gray-700 mb-1">Bed Capacity</label>
                        <input type="number" name="bed_capacity" id="bed_capacity" value="{{ old('bed_capacity') }}" min="0" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('bed_capacity') border-red-500 @enderror">
                        @error('bed_capacity')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Facility Description</label>
                        <textarea name="description" id="description" rows="3" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
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
                        <input type="email" name="email" id="email" value="{{ old('email') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('email') border-red-500 @enderror" required>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('phone') border-red-500 @enderror" required>
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url" name="website" id="website" value="{{ old('website') }}" 
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
                        <input type="text" name="address" id="address" value="{{ old('address') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('address') border-red-500 @enderror" required>
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('city') border-red-500 @enderror" required>
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State/Province</label>
                        <input type="text" name="state" id="state" value="{{ old('state') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('state') border-red-500 @enderror">
                        @error('state')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code *</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('postal_code') border-red-500 @enderror" required>
                        @error('postal_code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                        <input type="text" name="country" id="country" value="{{ old('country') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('country') border-red-500 @enderror" required>
                        @error('country')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Required Documents -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Required Documents</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="documents.license" class="block text-sm font-medium text-gray-700 mb-1">Facility License *</label>
                        <input type="file" name="documents[license]" id="documents.license" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('documents.license') border-red-500 @enderror" required>
                        <p class="text-xs text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (Max: 10MB)</p>
                        @error('documents.license')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="document_numbers.license" class="block text-sm font-medium text-gray-700 mb-1">License Document Number *</label>
                        <input type="text" name="document_numbers[license]" id="document_numbers.license" value="{{ old('document_numbers.license') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('document_numbers.license') border-red-500 @enderror" required>
                        @error('document_numbers.license')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="documents.tax" class="block text-sm font-medium text-gray-700 mb-1">Tax Certificate</label>
                        <input type="file" name="documents[tax]" id="documents.tax" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('documents.tax') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (Max: 10MB)</p>
                        @error('documents.tax')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="document_numbers.tax" class="block text-sm font-medium text-gray-700 mb-1">Tax Certificate Number</label>
                        <input type="text" name="document_numbers[tax]" id="document_numbers.tax" value="{{ old('document_numbers.tax') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('document_numbers.tax') border-red-500 @enderror">
                        @error('document_numbers.tax')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="documents.registration" class="block text-sm font-medium text-gray-700 mb-1">Registration Certificate *</label>
                        <input type="file" name="documents[registration]" id="documents.registration" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('documents.registration') border-red-500 @enderror" required>
                        <p class="text-xs text-gray-500 mt-1">Accepted formats: PDF, JPG, PNG (Max: 10MB)</p>
                        @error('documents.registration')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="document_numbers.registration" class="block text-sm font-medium text-gray-700 mb-1">Registration Number *</label>
                        <input type="text" name="document_numbers[registration]" id="document_numbers.registration" value="{{ old('document_numbers.registration') }}" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('document_numbers.registration') border-red-500 @enderror" required>
                        @error('document_numbers.registration')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="facility_photo" class="block text-sm font-medium text-gray-700 mb-1">Facility Photo</label>
                        <input type="file" name="facility_photo" id="facility_photo" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('facility_photo') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG (Max: 5MB)</p>
                        @error('facility_photo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Account Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" name="password" id="password" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('password') border-red-500 @enderror" required>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Register Facility
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
