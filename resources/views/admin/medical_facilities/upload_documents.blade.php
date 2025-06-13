@extends('layouts.app')

@section('title', 'Upload Documents')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Upload Facility Documents</h1>
        <div class="flex space-x-2">
            <a href="{{ route('medical_facilities.show', $medical_facility) }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Facility
            </a>
            <a href="{{ route('medical_facilities.documents', $medical_facility) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-file-alt mr-2"></i> View All Documents
            </a>
        </div>
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

    <!-- Facility Info Card -->
    <div class="bg-white rounded-lg shadow-sm mb-6 p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $medical_facility->facility_name }}</h2>
                <p class="text-gray-600">{{ $medical_facility->facility_type }} in {{ $medical_facility->city }}, {{ $medical_facility->country }}</p>
                <p class="text-gray-500 text-sm mt-1">License #: {{ $medical_facility->license_number }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($medical_facility->status == 'approved') bg-green-100 text-green-800
                    @elseif($medical_facility->status == 'verified') bg-blue-100 text-blue-800
                    @elseif($medical_facility->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($medical_facility->status == 'rejected') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    Status: {{ ucfirst($medical_facility->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Document Upload Form -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-800">Upload New Document</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('medical_facilities.documents.upload.store', $medical_facility) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                        <select id="document_type" name="document_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            <option value="">-- Select Document Type --</option>
                            @foreach($documentTypes as $value => $label)
                                <option value="{{ $label }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('document_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="document_title" class="block text-sm font-medium text-gray-700 mb-1">Document Title</label>
                        <input type="text" id="document_title" name="document_title" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="e.g. Medical License Certificate" required>
                        @error('document_title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="document_number" class="block text-sm font-medium text-gray-700 mb-1">Document Number (optional)</label>
                    <input type="text" id="document_number" name="document_number" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="e.g. License or Certificate Number">
                    @error('document_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">Document File</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="document_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="document_file" name="document_file" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, JPG, JPEG, PNG up to 10MB</p>
                            <p class="text-xs font-medium mt-2 hidden" id="selected-file"></p>
                        </div>
                    </div>
                    @error('document_file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <i class="fas fa-upload mr-2"></i> Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Current Documents Section -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-800">Current Documents</h3>
            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                {{ $medical_facility->documents->count() }} Total
            </span>
        </div>
        
        <div class="p-6">
            @if($medical_facility->documents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($documentTypes as $type => $label)
                        @php
                            $typeDocuments = $uploadedDocuments->get($label) ?? collect();
                        @endphp
                        
                        <div class="border rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b flex justify-between items-center">
                                <h4 class="font-medium text-gray-700">{{ $label }}</h4>
                                <span class="text-xs bg-gray-200 text-gray-700 rounded-full px-2 py-0.5">
                                    {{ $typeDocuments->count() }}
                                </span>
                            </div>
                            
                            @if($typeDocuments->count() > 0)
                                <ul class="divide-y divide-gray-200">
                                    @foreach($typeDocuments as $document)
                                        <li class="p-4">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $document->title }}</p>
                                                    @if($document->document_number)
                                                        <p class="text-xs text-gray-500">Number: {{ $document->document_number }}</p>
                                                    @endif
                                                    <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium
                                                        @if($document->status == 'approved') bg-green-100 text-green-800
                                                        @elseif($document->status == 'rejected') bg-red-100 text-red-800
                                                        @else bg-yellow-100 text-yellow-800 @endif">
                                                        {{ ucfirst($document->status) }}
                                                    </span>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('medical_facilities.documents.preview', [$medical_facility, $document]) }}" 
                                                       target="_blank" 
                                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    @if($document->status == 'pending')
                                                        <form action="{{ route('medical_facilities.documents.delete', [$medical_facility, $document]) }}" 
                                                              method="POST" 
                                                              onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            @if($document->status == 'rejected' && $document->rejection_reason)
                                                <div class="mt-2 p-2 bg-red-50 border border-red-100 rounded text-xs text-red-700">
                                                    <p><strong>Rejection reason:</strong> {{ $document->rejection_reason }}</p>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="p-4 text-center text-sm text-gray-500">
                                    No {{ strtolower($label) }} uploaded
                                </div>
                            @endif
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
</div>

<script>
    // Show selected file name
    document.getElementById('document_file').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const fileDisplay = document.getElementById('selected-file');
        
        if (fileName) {
            fileDisplay.textContent = 'Selected file: ' + fileName;
            fileDisplay.classList.remove('hidden');
        } else {
            fileDisplay.classList.add('hidden');
        }
    });
</script>
@endsection
