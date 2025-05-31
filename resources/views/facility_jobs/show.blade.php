@extends('layouts.app')

@section('title', $job->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('facility_jobs.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i> Back to Job Listings
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">{{ $job->title }}</h1>
            <div class="text-sm text-gray-500">Posted {{ $job->posted_at->diffForHumans() }}</div>
        </div>
        
        <div class="flex space-x-2">
            @if($job->status === 'open')
                <a href="{{ route('facility_jobs.edit', $job) }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Edit Job
                </a>
                
                <form action="{{ route('facility_jobs.update-status', $job) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded" 
                        onclick="return confirm('Are you sure you want to cancel this job posting?')">
                        <i class="fas fa-times-circle mr-2"></i> Cancel Job
                    </button>
                </form>
            @elseif($job->status === 'cancelled')
                <form action="{{ route('facility_jobs.update-status', $job) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="open">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">
                        <i class="fas fa-redo mr-2"></i> Reopen Job
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Status Banner -->
    <div class="mb-6 rounded-lg p-4 {{ $job->status === 'open' ? 'bg-green-100 text-green-800' : ($job->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
        <div class="flex items-center">
            <i class="fas {{ $job->status === 'open' ? 'fa-check-circle' : ($job->status === 'cancelled' ? 'fa-times-circle' : 'fa-info-circle') }} mr-2"></i>
            <div>
                <span class="font-semibold">Status: {{ ucfirst(str_replace('_', ' ', $job->status)) }}</span>
                @if($job->status === 'filled')
                    <span class="ml-2">- Filled on {{ $job->filled_at->format('M d, Y') }}</span>
                @elseif($job->status === 'cancelled')
                    <span class="ml-2">- Cancelled on {{ $job->cancelled_at->format('M d, Y') }}</span>
                @elseif($job->status === 'completed')
                    <span class="ml-2">- Completed on {{ $job->completed_at->format('M d, Y') }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column - Job Details -->
        <div class="md:col-span-2 space-y-6">
            <!-- Job Overview -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Job Overview</h2>
                
                <div class="mb-4">
                    <div class="text-sm text-gray-500 mb-1">Specialty</div>
                    <div class="font-medium">{{ $job->specialty->name }}</div>
                </div>
                
                <div class="mb-4">
                    <div class="text-sm text-gray-500 mb-1">Description</div>
                    <div class="prose max-w-none">{{ $job->description }}</div>
                </div>
                
                <div class="mb-4">
                    <div class="text-sm text-gray-500 mb-1">Responsibilities</div>
                    <div class="prose max-w-none">{{ $job->responsibilities }}</div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Required Experience</div>
                        <div class="font-medium">{{ $job->required_experience_years }} {{ Str::plural('year', $job->required_experience_years) }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Hourly Rate</div>
                        <div class="font-medium">${{ number_format($job->hourly_rate, 2) }}/hour</div>
                    </div>
                </div>
                
                @if($job->required_qualifications && count($job->required_qualifications) > 0)
                    <div class="mt-4">
                        <div class="text-sm text-gray-500 mb-1">Required Qualifications</div>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($job->required_qualifications as $qualification)
                                <li>{{ ucfirst(str_replace('_', ' ', $qualification)) }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @if($job->benefits && count($job->benefits) > 0)
                    <div class="mt-4">
                        <div class="text-sm text-gray-500 mb-1">Benefits</div>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($job->benefits as $benefit)
                                <li>{{ ucfirst(str_replace('_', ' ', $benefit)) }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            
            <!-- Schedule Details -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Schedule Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Schedule Type</div>
                        <div class="font-medium">{{ $job->is_recurring ? 'Recurring Shift' : 'Single Shift' }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Shift Type</div>
                        <div class="font-medium">{{ ucfirst($job->shift_type) }} Shift</div>
                    </div>
                </div>
                
                @if($job->is_recurring)
                    <div class="mb-4">
                        <div class="text-sm text-gray-500 mb-1">Recurring Days</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($job->recurring_pattern['days'] ?? [] as $day)
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                                    {{ ucfirst($day) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Start Time</div>
                            <div class="font-medium">{{ $job->recurring_pattern['times']['start'] ?? 'N/A' }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-500 mb-1">End Time</div>
                            <div class="font-medium">{{ $job->recurring_pattern['times']['end'] ?? 'N/A' }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Duration</div>
                            <div class="font-medium">{{ $job->recurring_duration_days }} {{ Str::plural('day', $job->recurring_duration_days) }}</div>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Start Date & Time</div>
                            <div class="font-medium">{{ $job->shift_start ? $job->shift_start->format('M d, Y g:i A') : 'N/A' }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-500 mb-1">End Date & Time</div>
                            <div class="font-medium">{{ $job->shift_end ? $job->shift_end->format('M d, Y g:i A') : 'N/A' }}</div>
                        </div>
                    </div>
                @endif
                
                <div class="mt-4">
                    <div class="text-sm text-gray-500 mb-1">Application Deadline</div>
                    <div class="font-medium">
                        {{ $job->deadline ? $job->deadline->format('M d, Y') : 'No deadline (open until filled)' }}
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Applications -->
        <div class="space-y-6">
            <!-- Application Stats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Application Stats</h2>
                
                <div class="space-y-4">
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Available Positions</div>
                        <div class="text-2xl font-bold">{{ $job->slots_available }}</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500 mb-1">Total Applications</div>
                        <div class="text-2xl font-bold">{{ $job->applications->count() }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Pending</div>
                            <div class="font-semibold">{{ $job->applications->where('status', 'pending')->count() }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Accepted</div>
                            <div class="font-semibold">{{ $job->applications->where('status', 'accepted')->count() }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Rejected</div>
                            <div class="font-semibold">{{ $job->applications->where('status', 'rejected')->count() }}</div>
                        </div>
                        
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Withdrawn</div>
                            <div class="font-semibold">{{ $job->applications->where('status', 'withdrawn')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Applications -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Applications</h2>
                
                @if($job->applications->count() > 0)
                    <div class="space-y-4">
                        @foreach($job->applications->sortByDesc('created_at')->take(5) as $application)
                            <div class="border rounded p-3 {{ $application->status === 'pending' ? 'border-yellow-300 bg-yellow-50' : ($application->status === 'accepted' ? 'border-green-300 bg-green-50' : 'border-gray-200') }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium">{{ $application->medicalWorker->user->name }}</div>
                                        <div class="text-sm text-gray-500">Applied {{ $application->created_at->diffForHumans() }}</div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded 
                                        {{ $application->status === 'pending' ? 'bg-yellow-200 text-yellow-800' : 
                                           ($application->status === 'accepted' ? 'bg-green-200 text-green-800' : 
                                           ($application->status === 'rejected' ? 'bg-red-200 text-red-800' : 'bg-gray-200 text-gray-800')) }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </div>
                                
                                @if($application->status === 'pending')
                                    <div class="mt-3 flex space-x-2">
                                        <form action="{{ route('facility_jobs.process-application', ['job' => $job->id, 'application' => $application->id]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="accept">
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs px-2 py-1 rounded">
                                                Accept
                                            </button>
                                        </form>
                                        
                                        <button type="button" class="bg-red-600 hover:bg-red-700 text-white text-xs px-2 py-1 rounded" 
                                            onclick="showRejectForm('{{ $application->id }}')">
                                            Reject
                                        </button>
                                        
                                        <div id="reject-form-{{ $application->id }}" class="hidden mt-2">
                                            <form action="{{ route('facility_jobs.process-application', ['job' => $job->id, 'application' => $application->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="action" value="reject">
                                                <textarea name="rejection_reason" rows="2" class="border rounded w-full px-2 py-1 mb-1" 
                                                    placeholder="Reason for rejection (required)"></textarea>
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs px-2 py-1 rounded">
                                                    Confirm Rejection
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    @if($job->applications->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                                View All Applications ({{ $job->applications->count() }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-user-clock text-4xl mb-2"></i>
                        <p>No applications received yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function showRejectForm(applicationId) {
    const rejectForm = document.getElementById(`reject-form-${applicationId}`);
    rejectForm.classList.toggle('hidden');
}
</script>
@endsection
