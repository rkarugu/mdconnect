@extends('layouts.app')

@section('title', 'Shift Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('facility.locum-shifts.index') }}" class="inline-flex items-center text-blue-500 hover:text-blue-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to All Shifts
    </a>

    <!-- Shift Details Card -->
    <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $locumShift->worker_type }} Shift</h1>
                <p class="text-lg text-gray-600">{{ $locumShift->start_datetime->format('l, F j, Y') }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 font-semibold leading-tight text-lg rounded-full {{ $locumShift->status == 'open' ? 'text-green-700 bg-green-100' : ($locumShift->status == 'filled' ? 'text-yellow-700 bg-yellow-100' : 'text-gray-700 bg-gray-100') }}">
                    {{ ucfirst($locumShift->status) }}
                </span>
                <p class="text-sm text-gray-500 mt-1">ID: #{{ $locumShift->id }}</p>
            </div>
        </div>
        <div class="border-t border-gray-200 mt-6 pt-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Shift Time</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $locumShift->start_datetime->format('g:i A') }} - {{ $locumShift->end_datetime->format('g:i A') }} ({{ $locumShift->start_datetime->diffInHours($locumShift->end_datetime) }} hrs)</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Hourly Rate</h3>
                    <p class="mt-1 text-lg text-gray-900">${{ number_format($locumShift->pay_rate, 2) }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Slots</h3>
                    <p class="mt-1 text-lg text-gray-900">{{ $locumShift->slots_available }} available / {{ $locumShift->slots_available + ($locumShift->applications->where('status','accepted')->count()) }} total</p>
                </div>
            </div>
            @if($locumShift->description)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-500">Description</h3>
                <p class="mt-1 text-gray-700">{{ $locumShift->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Applicants Section -->
    <div class="bg-white shadow-lg rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Applicants ({{ $locumShift->applications->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            @if($locumShift->applications->count() > 0)
            <table class="min-w-full leading-normal">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Applicant Name</th>
                        <th class="py-3 px-6 text-left">Applied At</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @foreach ($locumShift->applications as $application)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-4 px-6 text-left">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $application->medicalWorker->profile_picture ?? asset('img/avatar.png') }}" class="h-8 w-8 rounded-full" alt="">
                                    <span>{{ $application->medicalWorker->name }}</span>
                                </div>
                            </td>
                        <td class="py-4 px-6 text-left">{{ \Illuminate\Support\Carbon::parse($application->applied_at)->format('M j, Y, g:i a') }}</td>
                        <td class="py-4 px-6 text-center">
                            <span class="px-2 py-1 font-semibold leading-tight text-xs rounded-full 
                                @switch($application->status)
                                    @case('accepted') text-green-700 bg-green-100 @break
                                    @case('approved') text-green-700 bg-green-100 @break
                                    @case('rejected') text-red-700 bg-red-100 @break
                                    @case('waiting') text-yellow-700 bg-yellow-100 @break
                                @endswitch">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if ($application->status == 'waiting' && $locumShift->status == 'open')
                            <div class="flex item-center justify-center">
                                <form action="{{ route('facility.locum-shifts.applications.accept', ['locum_shift' => $locumShift->id, 'medical_worker' => $application->medicalWorker->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1 px-3 rounded-full transition duration-300">Accept</button>
                                </form>
                            </div>
                            @elseif($application->status == 'approved')
                                <span class="text-green-600 font-semibold">Accepted on {{ $application->selected_at ? \Illuminate\Support\Carbon::parse($application->selected_at)->format('M j, Y') : '' }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-center text-gray-500 py-10">No applications received for this shift yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
