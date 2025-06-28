@extends('layouts.app')

@section('title', 'Edit Locum Shift')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Locum Shift</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <ul class="mt-3 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('facility.locum-shifts.update', $locumShift) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="shift_date" class="block text-sm font-medium text-gray-700 mb-1">Shift Date</label>
                    <input type="date" name="shift_date" id="shift_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('shift_date', $locumShift->shift_date) }}" required>
                </div>
                <div>
                    <label for="worker_type" class="block text-sm font-medium text-gray-700 mb-1">Role / Worker Type</label>
                    <select name="worker_type" id="worker_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        <option value="General Practitioner" {{ old('worker_type', $locumShift->worker_type) == 'General Practitioner' ? 'selected' : '' }}>General Practitioner</option>
                        <option value="Registered Nurse" {{ old('worker_type', $locumShift->worker_type) == 'Registered Nurse' ? 'selected' : '' }}>Registered Nurse</option>
                        <option value="Pharmacist" {{ old('worker_type', $locumShift->worker_type) == 'Pharmacist' ? 'selected' : '' }}>Pharmacist</option>
                        <option value="Radiographer" {{ old('worker_type', $locumShift->worker_type) == 'Radiographer' ? 'selected' : '' }}>Radiographer</option>
                        <option value="Care Assistant" {{ old('worker_type', $locumShift->worker_type) == 'Care Assistant' ? 'selected' : '' }}>Care Assistant</option>
                    </select>
                </div>
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('start_time', $locumShift->start_time) }}" required>
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('end_time', $locumShift->end_time) }}" required>
                </div>
                <div>
                    <label for="pay_rate" class="block text-sm font-medium text-gray-700 mb-1">Pay Rate (per hour)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" name="pay_rate" id="pay_rate" class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0.00" step="0.01" value="{{ old('pay_rate', $locumShift->pay_rate) }}" required>
                    </div>
                </div>
                <div>
                    <label for="total_slots" class="block text-sm font-medium text-gray-700 mb-1">Number of Slots</label>
                    <input type="number" name="total_slots" id="total_slots" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" min="1" value="{{ old('total_slots', $locumShift->total_slots) }}" required>
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $locumShift->description) }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('facility.locum-shifts.show', $locumShift) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg mr-4 transition duration-300">Cancel</a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">Update Shift</button>
            </div>
        </form>
    </div>
</div>
@endsection
