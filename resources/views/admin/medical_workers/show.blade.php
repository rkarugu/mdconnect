@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-900">Medical Worker Details</h1>
        <div class="flex space-x-3">
            <a href="{{ route('medical_workers.edit', $medical_worker) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('medical_workers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    @if($medical_worker->profile_picture)
                        <img src="{{ asset('storage/' . $medical_worker->profile_picture) }}" 
                             alt="{{ $medical_worker->name ?? 'Medical Worker' }}" 
                             class="h-16 w-16 rounded-full object-cover"
                             onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.png') }}'">
                    @else
                        <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-gray-400 text-2xl"></i>
                        </div>
                    @endif
                    <div class="ml-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $medical_worker->name ?? 'No name available' }}</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $medical_worker->specialty->name ?? 'No specialty information' }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $medical_worker->status === 'approved' ? 'bg-green-100 text-green-800' : 
                       ($medical_worker->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                       ($medical_worker->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                    {{ ucfirst($medical_worker->status) }}
                </span>
            </div>
        </div>
        <div class="border-t border-gray-200">
            <dl>
                <!-- Personal Information -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $medical_worker->email ?? 'No email available' }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $medical_worker->phone ?? 'No phone available' }}</dd>
                </div>

                <!-- Professional Information -->
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">License Number</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $medical_worker->license_number }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Years of Experience</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $medical_worker->years_of_experience }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Bio</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $medical_worker->bio }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Education</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $medical_worker->education }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Certifications</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $medical_worker->certifications }}</dd>
                </div>

                <!-- Documents -->
                <div class="bg-white px-4 py-5 sm:px-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-medium text-gray-900">Documents</h4>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">{{ $medical_worker->documents->count() }} document(s)</span>
                            @if($medical_worker->status === 'pending' && $medical_worker->documents->count() > 0)
                            <form action="{{ route('medical_workers.verify', $medical_worker) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <i class="fas fa-check-double mr-1"></i> Verify All & Approve Worker
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($medical_worker->documents as $document)
                        <div class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-alt text-gray-400 text-2xl"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $document->title }}</p>
                                <p class="text-sm text-gray-500">{{ $document->document_type }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $document->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($document->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($document->status) }}
                                </span>
                            </div>
                            <div class="flex-shrink-0 space-y-2">
                                <a href="{{ route('medical_workers.preview_document', [$medical_worker, $document]) }}" 
                                   class="text-blue-600 hover:text-blue-900 block"
                                   target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ Storage::url($document->file_path) }}" 
                                   class="text-blue-600 hover:text-blue-900 block"
                                   download>
                                    <i class="fas fa-download"></i>
                                </a>
                                @if($document->status === 'pending')
                                <form action="{{ route('medical_workers.verify_document', [$medical_worker, $document]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="text-green-600 hover:text-green-900 block" title="Approve Document">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('medical_workers.verify_document', [$medical_worker, $document]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <div class="relative">
                                        <button type="button" class="text-red-600 hover:text-red-900 block reject-document" title="Reject Document" data-document-id="{{ $document->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <div id="rejection-reason-{{ $document->id }}" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg z-10 p-4">
                                            <input type="text" name="rejection_reason" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Enter rejection reason" required>
                                            <button type="submit" class="mt-2 w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                                Confirm Rejection
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full text-center text-gray-500 py-4">
                            No documents uploaded yet.
                        </div>
                        @endforelse
                    </div>
                </div>
            </dl>
        </div>
    </div>

    <!-- Status Update Form -->
    <div class="mt-6 bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Update Status</h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
                <p>Change the status of this medical worker. This will affect their ability to use the platform.</p>
            </div>
            <form action="{{ route('medical_workers.update_status', $medical_worker) }}" method="POST" class="mt-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <select name="status" id="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="pending" {{ $medical_worker->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $medical_worker->status === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $medical_worker->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="suspended" {{ $medical_worker->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" name="status_reason" id="status_reason" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Reason for status change (required for reject/suspend)">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rejectButtons = document.querySelectorAll('.reject-document');
    
    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const documentId = this.dataset.documentId;
            const reasonDiv = document.getElementById(`rejection-reason-${documentId}`);
            
            // Hide all other rejection reason divs
            document.querySelectorAll('[id^="rejection-reason-"]').forEach(div => {
                if (div.id !== `rejection-reason-${documentId}`) {
                    div.classList.add('hidden');
                }
            });
            
            // Toggle the current rejection reason div
            reasonDiv.classList.toggle('hidden');
        });
    });
});
</script>
@endpush
@endsection
