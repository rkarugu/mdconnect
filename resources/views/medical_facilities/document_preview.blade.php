@extends('layouts.app')

@section('title', 'Document Preview - ' . $document->title)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Document Preview</h1>
        <div class="flex space-x-2">
            <a href="{{ route('medical_facilities.show', $facility) }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Facility
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-medium text-gray-800">{{ $document->title }}</h2>
                <p class="text-sm text-gray-600">
                    @if($document->document_number)
                        Document #: {{ $document->document_number }} |
                    @endif
                    Uploaded: {{ $document->created_at->format('M d, Y') }}
                </p>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($document->status == 'approved') bg-green-100 text-green-800
                @elseif($document->status == 'rejected') bg-red-100 text-red-800
                @else bg-yellow-100 text-yellow-800 @endif">
                {{ ucfirst($document->status) }}
            </span>
        </div>

        <div class="p-6">
            <!-- Document Preview -->
            <div class="border rounded-lg overflow-hidden mb-6">
                @php
                    $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                @endphp
                
                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ asset('storage/' . $document->file_path) }}" alt="{{ $document->title }}" class="w-full">
                @elseif(strtolower($extension) === 'pdf')
                    <div class="bg-gray-100 p-4 text-center">
                        <p class="mb-4">PDF document preview is not available. Please download to view.</p>
                        <a href="{{ asset('storage/' . $document->file_path) }}" download class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-download mr-2"></i> Download PDF
                        </a>
                    </div>
                @else
                    <div class="bg-gray-100 p-4 text-center">
                        <p>Preview not available for this file type.</p>
                        <a href="{{ asset('storage/' . $document->file_path) }}" download class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 mt-2 inline-block">
                            <i class="fas fa-download mr-2"></i> Download File
                        </a>
                    </div>
                @endif
            </div>

            <!-- Document Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Document Type</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $document->document_type ?? 'Not specified' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Document Number</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $document->document_number ?? 'Not specified' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Issued Date</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $document->issued_date ? $document->issued_date->format('M d, Y') : 'Not specified' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Expiry Date</h3>
                    <p class="mt-1 text-base text-gray-900">{{ $document->expiry_date ? $document->expiry_date->format('M d, Y') : 'Not specified' }}</p>
                </div>

                @if($document->status === 'rejected' && $document->rejection_reason)
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-red-500">Rejection Reason</h3>
                        <p class="mt-1 text-base text-red-700 bg-red-50 p-2 rounded">{{ $document->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            <!-- Admin Actions (visible only to admins) -->
            @if(auth()->user() && auth()->user()->isAdmin())
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Document Verification</h3>
                
                <form action="{{ route('admin.medical_facilities.documents.verify', [$facility, $document]) }}" method="POST">
                    @csrf
                    
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex items-center">
                            <input id="approve" name="verification_action" type="radio" value="approve" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="approve" class="ml-2 block text-sm text-gray-700">Approve</label>
                        </div>
                        <div class="flex items-center">
                            <input id="reject" name="verification_action" type="radio" value="reject" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="reject" class="ml-2 block text-sm text-gray-700">Reject</label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason (if rejecting)</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Verification
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
