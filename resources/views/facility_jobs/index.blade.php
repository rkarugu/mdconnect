@extends('layouts.app')

@section('title', 'Manage Job Postings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Job Postings</h1>
        <a href="{{ route('facility_jobs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded shadow">
            <i class="fas fa-plus mr-2"></i> Post New Job
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('facility_jobs.index') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="w-full md:w-auto">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    class="border border-gray-300 rounded px-3 py-2 w-full md:w-64" 
                    placeholder="Search by title or description">
            </div>
            
            <div class="w-full md:w-auto">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="border border-gray-300 rounded px-3 py-2 w-full md:w-48">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="filled" {{ request('status') == 'filled' ? 'selected' : '' }}>Filled</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            
            <div class="w-full md:w-auto">
                <label for="specialty_id" class="block text-sm font-medium text-gray-700 mb-1">Specialty</label>
                <select name="specialty_id" id="specialty_id" class="border border-gray-300 rounded px-3 py-2 w-full md:w-48">
                    <option value="">All Specialties</option>
                    @foreach($specialties as $specialty)
                        <option value="{{ $specialty->id }}" {{ request('specialty_id') == $specialty->id ? 'selected' : '' }}>
                            {{ $specialty->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-auto flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('facility_jobs.index') }}" class="ml-2 text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Job Listings -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($jobs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applications</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($jobs as $job)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $job->title }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $job->is_recurring ? 'Recurring' : 'Single Shift' }} • 
                                        ${{ number_format($job->hourly_rate, 2) }}/hr
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $job->specialty->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'open' => 'bg-green-100 text-green-800',
                                            'in_progress' => 'bg-yellow-100 text-yellow-800',
                                            'filled' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-purple-100 text-purple-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusColor = $statusColors[$job->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $job->applications->count() }} / {{ $job->slots_available }} slots
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $job->posted_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('facility_jobs.show', $job) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($job->status === 'open')
                                        <a href="{{ route('facility_jobs.edit', $job) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $jobs->appends(request()->except('page'))->links() }}
            </div>
        @else
            <div class="p-6 text-center">
                <p class="text-gray-600">No job postings found.</p>
                <a href="{{ route('facility_jobs.create') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded shadow">
                    <i class="fas fa-plus mr-2"></i> Post Your First Job
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
