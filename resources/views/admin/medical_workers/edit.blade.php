@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-900">Edit Medical Worker</h1>
        <a href="{{ route('admin.medical_workers.show', $medical_worker) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Details
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center">
                @if($medical_worker->user->profile_picture)
                    <img src="{{ asset('storage/' . $medical_worker->user->profile_picture) }}" 
                         alt="{{ $medical_worker->user->name }}" 
                         class="h-16 w-16 rounded-full object-cover"
                         onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}';">
                @else
                    <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-user text-gray-400 text-2xl"></i>
                    </div>
                @endif
                <div class="ml-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $medical_worker->user->name }}</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $medical_worker->specialty->name }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.medical_workers.update', $medical_worker) }}" method="POST" enctype="multipart/form-data" class="divide-y divide-gray-200">
            @csrf
            @method('PUT')
            
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Profile Picture -->
                    <div class="col-span-2">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($medical_worker->user->profile_picture)
                                    <img src="{{ Storage::url($medical_worker->user->profile_picture) }}" 
                                         alt="{{ $medical_worker->user->name }}" 
                                         class="h-24 w-24 rounded-full object-cover">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-400 text-3xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="hidden">
                                    <label for="profile_picture" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        <i class="fas fa-upload mr-2"></i>
                                        Upload New Photo
                                    </label>
                                    @if($medical_worker->user->profile_picture)
                                        <button type="button" onclick="document.getElementById('profile_picture').value = ''" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-trash mr-2"></i>
                                            Remove
                                        </button>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                @error('profile_picture')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="col-span-2">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Personal Information</h3>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $medical_worker->user->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $medical_worker->user->email) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $medical_worker->user->phone) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Professional Information -->
                    <div class="col-span-2 mt-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Professional Information</h3>
                    </div>

                    <div>
                        <label for="specialty_id" class="block text-sm font-medium text-gray-700">Medical Specialty</label>
                        <select name="specialty_id" id="specialty_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select a specialty</option>
                            @foreach($specialties as $specialty)
                                <option value="{{ $specialty->id }}" {{ old('specialty_id', $medical_worker->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                    {{ $specialty->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialty_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700">License Number</label>
                        <input type="text" name="license_number" id="license_number" value="{{ old('license_number', $medical_worker->license_number) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('license_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="years_of_experience" class="block text-sm font-medium text-gray-700">Years of Experience</label>
                        <input type="number" name="years_of_experience" id="years_of_experience" value="{{ old('years_of_experience', $medical_worker->years_of_experience) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('years_of_experience')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700">Professional Bio</label>
                        <textarea name="bio" id="bio" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('bio', $medical_worker->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="education" class="block text-sm font-medium text-gray-700">Education</label>
                        <textarea name="education" id="education" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('education', $medical_worker->education) }}</textarea>
                        @error('education')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="certifications" class="block text-sm font-medium text-gray-700">Certifications</label>
                        <textarea name="certifications" id="certifications" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('certifications', $medical_worker->certifications) }}</textarea>
                        @error('certifications')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Existing Documents -->
                    @if($medical_worker->documents->count() > 0)
                    <div class="col-span-2 mt-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Existing Documents</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($medical_worker->documents as $document)
                            <div class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $document->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $document->document_type }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="#" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- New Document Upload -->
                    <div class="col-span-2 mt-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Upload New Documents</h3>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-upload text-gray-400 text-3xl mb-3"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="documents" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload documents</span>
                                        <input id="documents" name="documents[]" type="file" class="sr-only" multiple>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, JPEG, PNG up to 10MB</p>
                            </div>
                        </div>
                        @error('documents')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('documents.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Medical Worker
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add drag and drop functionality for document upload
    const dropzone = document.querySelector('input[type="file"]').closest('div');
    const fileInput = document.querySelector('input[type="file"]');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropzone.classList.add('border-blue-300', 'bg-blue-50');
    }

    function unhighlight(e) {
        dropzone.classList.remove('border-blue-300', 'bg-blue-50');
    }

    dropzone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const profilePictureInput = document.getElementById('profile_picture');
        const profilePicturePreview = document.querySelector('.flex-shrink-0 img');
        const defaultAvatar = document.querySelector('.flex-shrink-0 .fa-user').parentElement;

        if (profilePictureInput) {