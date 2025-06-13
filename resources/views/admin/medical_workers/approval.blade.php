@extends('layouts.app')

@section('title', 'Medical Workers Approval')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-900">Medical Workers Approval</h1>
        <div class="flex space-x-3">
            <a href="{{ route('medical_workers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to All Workers
            </a>
            <a href="{{ route('medical_workers.verification') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-check-circle mr-2"></i>
                Go to Verification
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialty</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($workers as $worker)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $worker->name }}</div>
                            <div class="text-sm text-gray-500">{{ $worker->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $worker->specialty->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $worker->license_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $worker->documents->where('status', 'approved')->count() }} approved
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $worker->documents->count() }} total
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('medical_workers.show', $worker) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <form action="{{ route('medical_workers.approve', $worker) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-thumbs-up"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('medical_workers.reject', $worker) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to reject this application?')">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            No medical workers pending approval.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $workers->links() }}
    </div>
</div>
@endsection 