@extends('layouts.app')

@section('title', 'Create Job Posting')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('facility_jobs.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> Back to Job Listings
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-blue-600 p-4">
            <h1 class="text-2xl font-bold text-white">Create New Job Posting</h1>
        </div>
        
        <form action="{{ route('facility_jobs.store') }}" method="POST" class="p-6">
            @csrf
            
            <!-- Job Details Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Job Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Job Title<span class="text-red-600">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" 
                            class="border border-gray-300 rounded px-3 py-2 w-full @error('title') border-red-500 @enderror" 
                            required>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="specialty_id" class="block text-sm font-medium text-gray-700 mb-1">Medical Specialty<span class="text-red-600">*</span></label>
                        <select name="specialty_id" id="specialty_id" 
                            class="border border-gray-300 rounded px-3 py-2 w-full @error('specialty_id') border-red-500 @enderror" 
                            required>
                            <option value="">Select Specialty</option>
                            @foreach($specialties as $specialty)
                                <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                    {{ $specialty->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialty_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Job Description<span class="text-red-600">*</span></label>
                <textarea name="description" id="description" rows="4" 
                    class="border border-gray-300 rounded px-3 py-2 w-full @error('description') border-red-500 @enderror" 
                    required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="responsibilities" class="block text-sm font-medium text-gray-700 mb-1">Responsibilities<span class="text-red-600">*</span></label>
                <textarea name="responsibilities" id="responsibilities" rows="4" 
                    class="border border-gray-300 rounded px-3 py-2 w-full @error('responsibilities') border-red-500 @enderror" 
                    required>{{ old('responsibilities') }}</textarea>
                @error('responsibilities')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="required_experience_years" class="block text-sm font-medium text-gray-700 mb-1">Required Experience (years)<span class="text-red-600">*</span></label>
                    <input type="number" name="required_experience_years" id="required_experience_years" 
                        value="{{ old('required_experience_years', 0) }}" min="0" step="1" 
                        class="border border-gray-300 rounded px-3 py-2 w-full @error('required_experience_years') border-red-500 @enderror" 
                        required>
                    @error('required_experience_years')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-1">Hourly Rate ($)<span class="text-red-600">*</span></label>
                    <input type="number" name="hourly_rate" id="hourly_rate" 
                        value="{{ old('hourly_rate') }}" min="0" step="0.01" 
                        class="border border-gray-300 rounded px-3 py-2 w-full @error('hourly_rate') border-red-500 @enderror" 
                        required>
                    @error('hourly_rate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="border-t pt-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Schedule</h2>
                
                <div class="mb-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_recurring" id="is_recurring" value="1" 
                            {{ old('is_recurring') ? 'checked' : '' }} class="mr-2" onchange="toggleShiftType()">
                        <label for="is_recurring" class="text-sm font-medium text-gray-700">This is a recurring job</label>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="shift_type" class="block text-sm font-medium text-gray-700 mb-1">Shift Type<span class="text-red-600">*</span></label>
                        <select name="shift_type" id="shift_type" 
                            class="border border-gray-300 rounded px-3 py-2 w-full @error('shift_type') border-red-500 @enderror" 
                            required>
                            <option value="day" {{ old('shift_type') == 'day' ? 'selected' : '' }}>Day Shift</option>
                            <option value="night" {{ old('shift_type') == 'night' ? 'selected' : '' }}>Night Shift</option>
                            <option value="evening" {{ old('shift_type') == 'evening' ? 'selected' : '' }}>Evening Shift</option>
                            <option value="custom" {{ old('shift_type') == 'custom' ? 'selected' : '' }}>Custom Shift</option>
                        </select>
                        @error('shift_type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="slots_available" class="block text-sm font-medium text-gray-700 mb-1">Number of Positions<span class="text-red-600">*</span></label>
                        <input type="number" name="slots_available" id="slots_available" 
                            value="{{ old('slots_available', 1) }}" min="1" step="1" 
                            class="border border-gray-300 rounded px-3 py-2 w-full @error('slots_available') border-red-500 @enderror" 
                            required>
                        @error('slots_available')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Job Posting
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleShiftType() {
    const isRecurring = document.getElementById('is_recurring').checked;
    const singleShiftFields = document.getElementById('single-shift-fields');
    const recurringShiftFields = document.getElementById('recurring-shift-fields');
    
    if (isRecurring) {
        singleShiftFields.style.display = 'none';
        recurringShiftFields.style.display = 'block';
    } else {
        singleShiftFields.style.display = 'block';
        recurringShiftFields.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleShiftType();
});
</script>
@endsection
