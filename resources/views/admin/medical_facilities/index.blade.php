@extends('layouts.app')

@section('title', 'Admin - Manage Medical Facilities')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Manage Medical Facilities</h1>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form action="{{ route('admin.medical_facilities.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    placeholder="Name, Email, License #..." 
                    class="w-full rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div class="w-full md:w-auto">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="w-full md:w-auto">
                <label for="facility_type" class="block text-sm font-medium text-gray-700 mb-1">Facility Type</label>
                <select name="facility_type" id="facility_type" class="rounded border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">All Types</option>
                    <option value="Hospital" {{ request('facility_type') == 'Hospital' ? 'selected' : '' }}>Hospital</option>
                    <option value="Clinic" {{ request('facility_type') == 'Clinic' ? 'selected' : '' }}>Clinic</option>
                    <option value="Laboratory" {{ request('facility_type') == 'Laboratory' ? 'selected' : '' }}>Laboratory</option>
                    <option value="Pharmacy" {{ request('facility_type') == 'Pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                    <option value="Nursing Home" {{ request('facility_type') == 'Nursing Home' ? 'selected' : '' }}>Nursing Home</option>
                    <option value="Rehabilitation Center" {{ request('facility_type') == 'Rehabilitation Center' ? 'selected' : '' }}>Rehabilitation Center</option>
                    <option value="Other" {{ request('facility_type') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Facilities List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($facilities->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($facilities as $facility)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                                            <i class="fas fa-hospital"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $facility->facility_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $facility->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $facility->facility_type }}</div>
                                <div class="text-sm text-gray-500">License: {{ $facility->license_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $facility->city }}, {{ $facility->country }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'verified' => 'bg-blue-100 text-blue-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'suspended' => 'bg-gray-100 text-gray-800'
                                    ];
                                    $statusColor = $statusColors[$facility->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                    {{ ucfirst($facility->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $totalDocs = $facility->documents->count();
                                    $pendingDocs = $facility->documents->where('status', 'pending')->count();
                                    $approvedDocs = $facility->documents->where('status', 'approved')->count();
                                    $rejectedDocs = $facility->documents->where('status', 'rejected')->count();
                                    $verificationProgress = $totalDocs > 0 ? round(($approvedDocs / $totalDocs) * 100) : 0;
                                @endphp
                                
                                <div class="flex items-center">
                                    @if($totalDocs > 0)
                                        <div class="mr-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $verificationProgress }}%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-xs font-medium text-gray-900">{{ $approvedDocs }}/{{ $totalDocs }}</span>
                                            <a href="{{ route('admin.medical_facilities.documents', $facility) }}" class="ml-2 text-xs text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500">No documents</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $facility->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.medical_facilities.show', $facility) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($facility->status == 'pending')
                                    <form action="{{ route('admin.medical_facilities.verify', $facility) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 mr-3 bg-transparent border-0 p-0 cursor-pointer">
                                            <i class="fas fa-check-circle"></i> Verify
                                        </button>
                                    </form>
                                @endif
                                @if($facility->status == 'verified')
                                    <form action="{{ route('admin.medical_facilities.approve', $facility) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                            <i class="fas fa-thumbs-up"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.medical_facilities.reject', $facility) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-thumbs-down"></i> Reject
                                        </button>
                                    </form>
                                @endif
                                @if($facility->status == 'approved')
                                    <form action="{{ route('admin.medical_facilities.suspend', $facility) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-ban"></i> Suspend
                                        </button>
                                    </form>
                                @endif
                                @if($facility->status == 'suspended' || $facility->status == 'rejected')
                                    <form action="{{ route('admin.medical_facilities.reinstate', $facility) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-redo"></i> Reinstate
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-3">
                {{ $facilities->links() }}
            </div>
        @else
            <div class="p-6 text-center text-gray-500">
                <p>No facilities found.</p>
            </div>
        @endif
    </div>
</div>
@endsection
