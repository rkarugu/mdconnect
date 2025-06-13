@extends('layouts.app')

@section('title', 'Admin - Medical Facilities Verification')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Medical Facilities Verification</h1>
        <a href="{{ route('medical_facilities.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to All Facilities
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

    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="p-4 border-b">
            <form method="GET" action="{{ route('medical_facilities.verification') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[250px]">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        placeholder="Facility name, license number, email..."
                        class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                    <a href="{{ route('medical_facilities.verification') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 ml-2">
                        <i class="fas fa-times mr-2"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        @if($facilities->isEmpty())
            <div class="p-6 text-center">
                <p class="text-gray-500">No pending facilities found for verification.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($facilities as $facility)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($facility->facility_photo)
                                            <div class="flex-shrink-0 h-10 w-10 mr-3">
                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                    src="{{ asset('storage/' . $facility->facility_photo) }}" 
                                                    alt="{{ $facility->facility_name }}">
                                            </div>
                                        @else
                                            <div class="flex-shrink-0 h-10 w-10 mr-3 bg-gray-200 rounded-full flex items-center justify-center">
                                                <i class="fas fa-hospital text-gray-500"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $facility->facility_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                License: {{ $facility->license_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $facility->facility_type }}</div>
                                    @if($facility->bed_capacity)
                                        <div class="text-sm text-gray-500">{{ $facility->bed_capacity }} beds</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $facility->city }}</div>
                                    <div class="text-sm text-gray-500">{{ $facility->country }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $facility->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $facility->phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $facility->documents->where('status', 'pending')->count() > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $facility->documents->where('status', 'approved')->count() }}/{{ $facility->documents->count() }} verified
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($facility->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($facility->status == 'verified') bg-blue-100 text-blue-800
                                        @elseif($facility->status == 'approved') bg-green-100 text-green-800
                                        @elseif($facility->status == 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($facility->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex flex-col space-y-2">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('medical_facilities.show', $facility) }}" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            
                                            <a href="{{ route('medical_facilities.documents', $facility) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-file-alt"></i> Documents
                                            </a>
                                        </div>
                                        
                                        <div class="flex space-x-3">
                                            @if($facility->documents->count() > 0)
                                                @if($facility->documents->where('status', 'pending')->count() > 0)
                                                    <!-- Some documents still need verification -->
                                                    <form action="{{ route('medical_facilities.documents.verify_all', $facility) }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="text-green-600 hover:text-green-900 cursor-pointer bg-transparent border-0 p-0">
                                                            <i class="fas fa-check-double"></i> Verify All Docs
                                                        </button>
                                                    </form>
                                                @else
                                                    <!-- All documents verified, can verify facility -->
                                                    <form action="{{ route('medical_facilities.verify', $facility) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900 cursor-pointer bg-transparent border-0 p-0">
                                                            <i class="fas fa-check-circle"></i> Verify Facility
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <span class="text-gray-500 text-xs italic">No documents to verify</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
