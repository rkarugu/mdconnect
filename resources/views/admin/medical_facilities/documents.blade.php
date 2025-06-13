@extends('layouts.app')

@section('title', 'Admin - Verify Facility Documents')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Verify Facility Documents</h1>
        <div class="flex space-x-2">
            <a href="{{ route('medical_facilities.documents.upload', $medical_facility) }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                <i class="fas fa-upload mr-2"></i> Upload New Documents
            </a>
            <a href="{{ route('medical_facilities.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Facilities
            </a>
        </div>
    </div>

    <!-- Facility Info Card -->
    <div class="bg-white rounded-lg shadow-sm mb-6 p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $medical_facility->facility_name }}</h2>
                <p class="text-gray-600">{{ $medical_facility->facility_type }} in {{ $medical_facility->city }}, {{ $medical_facility->country }}</p>
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

    <!-- Document Verification Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600">Total Documents</p>
            <p class="text-2xl font-bold">{{ $documents->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600">Approved</p>
            <p class="text-2xl font-bold">{{ $documents->where('status', 'approved')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600">Pending</p>
            <p class="text-2xl font-bold">{{ $documents->where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-600">Rejected</p>
            <p class="text-2xl font-bold">{{ $documents->where('status', 'rejected')->count() }}</p>
        </div>
    </div>

    <!-- Document Verification Actions -->
    <div class="bg-white rounded-lg shadow-sm mb-6 p-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <h2 class="text-lg font-medium mb-4 md:mb-0">Document Verification</h2>
            
            <div class="flex flex-wrap gap-2">
                @if($documents->where('status', 'pending')->count() > 0)
                    <form action="{{ route('medical_facilities.documents.verify_all', $medical_facility) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            <i class="fas fa-check-double mr-2"></i> Approve All Pending
                        </button>
                    </form>
                @endif
                
                @if($documents->count() > 0 && $documents->where('status', 'pending')->count() == 0 && $medical_facility->status == 'pending')
                    <form action="{{ route('medical_facilities.verify', $medical_facility) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-check-circle mr-2"></i> Verify Facility
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Documents List -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-800">Required Documents</h2>
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $documents->count() }} Total
                </span>
            </div>
        </div>
        
        <div class="p-4">
            @if($documents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Number</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($documents as $document)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                @if(Str::contains($document->mime_type, 'pdf'))
                                                    <i class="fas fa-file-pdf text-blue-600"></i>
                                                @elseif(Str::contains($document->mime_type, 'image'))
                                                    <i class="fas fa-file-image text-blue-600"></i>
                                                @else
                                                    <i class="fas fa-file-alt text-blue-600"></i>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $document->title }}</div>
                                                <div class="text-xs text-gray-500">{{ $document->document_type }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $document->document_number ?: 'Not provided' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $document->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($document->status == 'approved') bg-green-100 text-green-800
                                            @elseif($document->status == 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($document->status) }}
                                        </span>
                                        @if($document->status == 'rejected' && $document->rejection_reason)
                                            <span class="ml-2 text-xs text-red-600 cursor-pointer" 
                                                  title="{{ $document->rejection_reason }}"
                                                  onclick="alert('Rejection reason: {{ $document->rejection_reason }}')">
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('medical_facilities.documents.preview', [$medical_facility, $document]) }}" 
                                               target="_blank"
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            
                                            @if($document->status == 'pending')
                                                <form action="{{ route('medical_facilities.documents.verify', [$medical_facility, $document]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="text-green-600 hover:text-green-900">
                                                        <i class="fas fa-check-circle"></i> Approve
                                                    </button>
                                                </form>
                                                
                                                <button type="button" 
                                                        onclick="document.getElementById('reject-form-{{ $document->id }}').classList.toggle('hidden')"
                                                        class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-times-circle"></i> Reject
                                                </button>
                                            @endif
                                        </div>
                                        
                                        <!-- Hidden Rejection Form -->
                                        <div id="reject-form-{{ $document->id }}" class="hidden mt-2 bg-gray-50 p-2 rounded">
                                            <form action="{{ route('medical_facilities.documents.verify', [$medical_facility, $document]) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <div class="mb-2">
                                                    <label for="rejection_reason_{{ $document->id }}" class="block text-xs font-medium text-gray-700">Reason for rejection</label>
                                                    <textarea name="rejection_reason" id="rejection_reason_{{ $document->id }}" 
                                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-xs"
                                                              rows="2" required></textarea>
                                                </div>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" 
                                                            onclick="document.getElementById('reject-form-{{ $document->id }}').classList.add('hidden')"
                                                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-gray-700 bg-gray-100 hover:bg-gray-200">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" 
                                                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700">
                                                        Confirm
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-8 text-center">
                    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500">No documents have been uploaded for this facility</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Verification Requirements -->
    <div class="bg-white rounded-lg shadow-sm mt-6 p-4">
        <h2 class="text-lg font-medium mb-4">Verification Requirements</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border rounded p-4">
                <h3 class="font-medium text-gray-800 mb-2">Document Requirements</h3>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                    <li>Business registration/license must be valid and current</li>
                    <li>Tax ID documentation must match facility information</li>
                    <li>Documents must be clearly legible and complete</li>
                    <li>No signs of tampering or alterations</li>
                    <li>All required fields must be visible</li>
                </ul>
            </div>
            <div class="border rounded p-4">
                <h3 class="font-medium text-gray-800 mb-2">Verification Process</h3>
                <ol class="list-decimal list-inside text-sm text-gray-600 space-y-1">
                    <li>Review all uploaded documents</li>
                    <li>Verify authenticity and validity</li>
                    <li>Check consistency with provided information</li>
                    <li>Approve or reject with reason</li>
                    <li>Once all documents are verified, proceed to facility verification</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
