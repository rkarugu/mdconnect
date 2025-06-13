@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Add Medical Specialty
                </h2>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('medical_specialties.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('medical_specialties.store') }}" method="POST" class="space-y-6 p-6">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Specialty Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="e.g., Cardiology">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Icon -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700">
                            Icon (FontAwesome class)
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                fa-
                            </span>
                            <input type="text" name="icon" id="icon" value="{{ old('icon') }}"
                                class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="e.g., heart">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Enter FontAwesome icon name without the 'fa-' prefix</p>
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="3" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Provide a detailed description of this medical specialty">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Qualification Requirements -->
                    <div>
                        <label for="qualification_requirements" class="block text-sm font-medium text-gray-700">
                            Qualification Requirements
                        </label>
                        <textarea name="qualification_requirements" id="qualification_requirements" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="List required qualifications, certifications, or special training">{{ old('qualification_requirements') }}</textarea>
                        @error('qualification_requirements')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Minimum Experience Years -->
                    <div>
                        <label for="minimum_experience_years" class="block text-sm font-medium text-gray-700">
                            Minimum Years of Experience
                        </label>
                        <input type="number" name="minimum_experience_years" id="minimum_experience_years" 
                            value="{{ old('minimum_experience_years', 0) }}" min="0" max="50"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('minimum_experience_years')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                Active Status
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Only active specialties can be assigned to medical workers</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('medical_specialties.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Specialty
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
