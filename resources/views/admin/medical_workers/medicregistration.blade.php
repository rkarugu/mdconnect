@extends('layouts.medical_registration')

@section('title', 'Medical Worker Registration - MediConnect')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
    <div class="w-full max-w-4xl px-6 py-8 bg-white shadow-md rounded-lg">
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-bold text-indigo-600">Medical Worker Registration</h1>
            <p class="text-gray-600 mt-2">Join MediConnect as a verified healthcare professional</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(session('errorDetails'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 overflow-auto max-h-60">
                <h3 class="font-bold mb-2">Technical Details (For Debugging):</h3>
                <div class="text-xs">{!! session('errorDetails') !!}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('medical_workers.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="bg-indigo-50 p-4 rounded-lg mb-6">
                <h2 class="text-xl font-semibold text-indigo-700 mb-2">Important Information</h2>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <li>All submitted information will be verified by our administration team</li>
                    <li>Please ensure all documents are clear, legible and valid</li>
                    <li>The verification process typically takes 1-3 business days</li>
                    <li>You will receive email notifications about your application status</li>
                    <li>Upon approval, you will receive login credentials for the MediConnect app</li>
                </ul>
            </div>

            <!-- Personal Information -->
            <fieldset class="border border-gray-300 rounded-md p-4">
                <legend class="text-lg font-medium text-gray-700 px-2">Personal Information</legend>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div>
                        <label for="medical_specialty_id" class="block text-sm font-medium text-gray-700">Medical Specialty</label>
                        <select name="medical_specialty_id" id="medical_specialty_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select a specialty</option>
                            @foreach($specialties as $specialty)
                                <option value="{{ $specialty->id }}" {{ old('medical_specialty_id') == $specialty->id ? 'selected' : '' }}>
                                    {{ $specialty->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('medical_specialty_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </fieldset>
            <!-- Professional Information -->
            <fieldset class="border border-gray-300 rounded-md p-4">
                <legend class="text-lg font-medium text-gray-700 px-2">Professional Information</legend>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700">Medical License Number</label>
                        <input type="text" name="license_number" id="license_number" value="{{ old('license_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('license_number')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div>
                        <label for="years_of_experience" class="block text-sm font-medium text-gray-700">Years of Experience</label>
                        <input type="number" name="years_of_experience" id="years_of_experience" value="{{ old('years_of_experience') }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('years_of_experience')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="education" class="block text-sm font-medium text-gray-700">Education</label>
                    <textarea name="education" id="education" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('education') }}</textarea>
                    <p class="text-gray-500 text-xs mt-1">Include your degrees, institutions, and graduation years</p>
                    @error('education')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
                
                <div class="mt-4">
                    <label for="certifications" class="block text-sm font-medium text-gray-700">Certifications (Optional)</label>
                    <textarea name="certifications" id="certifications" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('certifications') }}</textarea>
                    <p class="text-gray-500 text-xs mt-1">List any additional certifications or specialized training</p>
                    @error('certifications')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
                
                <div class="mt-4">
                    <label for="bio" class="block text-sm font-medium text-gray-700">Professional Bio</label>
                    <textarea name="bio" id="bio" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('bio') }}</textarea>
                    <p class="text-gray-500 text-xs mt-1">Brief description of your professional experience and specializations</p>
                    @error('bio')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </fieldset>

            <!-- Document Upload -->
            <fieldset class="border border-gray-300 rounded-md p-4">
                <legend class="text-lg font-medium text-gray-700 px-2">Required Documents</legend>
                
                <div class="space-y-4">
                    <div class="border p-3 rounded-md bg-gray-50">
                        <label class="block text-sm font-medium text-gray-700 mb-2">National ID / Passport</label>
                        <div class="mt-1 flex flex-col space-y-2">
                            <input type="text" name="document_numbers[national_id]" value="{{ old('document_numbers.national_id') }}" placeholder="ID Number" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <input type="file" name="documents[national_id]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                        </div>
                        @error('document_numbers.national_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        @error('documents.national_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="border p-3 rounded-md bg-gray-50">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medical License</label>
                        <div class="mt-1 flex flex-col space-y-2">
                            <input type="text" name="document_numbers[license]" value="{{ old('document_numbers.license') }}" placeholder="License Number" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <input type="file" name="documents[license]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                        </div>
                        @error('document_numbers.license')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        @error('documents.license')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="border p-3 rounded-md bg-gray-50">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Academic Certificate</label>
                        <div class="mt-1 flex flex-col space-y-2">
                            <input type="text" name="document_numbers[academic_certificate]" value="{{ old('document_numbers.academic_certificate') }}" placeholder="Certificate Number" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <input type="file" name="documents[academic_certificate]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                        </div>
                        @error('document_numbers.academic_certificate')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        @error('documents.academic_certificate')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="border p-3 rounded-md bg-gray-50">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Passport Photo</label>
                        <div class="mt-1">
                            <input type="file" name="documents[passport_photo]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                            <p class="text-gray-500 text-xs mt-1">Professional passport-sized photo with white background</p>
                        </div>
                        @error('documents.passport_photo')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    
                    <div class="border p-3 rounded-md bg-gray-50">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Resume/CV</label>
                        <div class="mt-1">
                            <input type="file" name="documents[resume]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                            <p class="text-gray-500 text-xs mt-1">Your professional resume or CV (PDF, DOC or DOCX format)</p>
                        </div>
                        @error('documents.resume')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </fieldset>

            <!-- Terms and Conditions -->
            <div class="mt-6">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" required>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="font-medium text-gray-700">I agree to the MediConnect <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a> and <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a></label>
                    </div>
                </div>
                @error('terms')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Submit Application
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
