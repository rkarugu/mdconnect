@extends('layouts.medical_registration')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Form Submission Diagnostic</h1>
    
    <div class="bg-white shadow-md rounded p-6">
        <h2 class="text-xl font-semibold mb-4">Test Form Submission</h2>
        
        <form method="POST" action="{{ route('medical_workers.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Basic Information</label>
                <input type="text" name="name" value="Test User" placeholder="Full Name" class="mb-2 block w-full rounded-md border-gray-300" required>
                <input type="email" name="email" value="test@example.com" placeholder="Email Address" class="mb-2 block w-full rounded-md border-gray-300" required>
                <input type="text" name="phone" value="1234567890" placeholder="Phone Number" class="mb-2 block w-full rounded-md border-gray-300" required>
                <input type="text" name="license_number" value="LIC123456" placeholder="License Number" class="mb-2 block w-full rounded-md border-gray-300" required>
                <input type="number" name="years_of_experience" value="5" placeholder="Years of Experience" class="mb-2 block w-full rounded-md border-gray-300" required>
                <textarea name="bio" placeholder="Professional Bio" class="mb-2 block w-full rounded-md border-gray-300" required>Test bio information</textarea>
                <textarea name="education" placeholder="Education" class="mb-2 block w-full rounded-md border-gray-300" required>Test education information</textarea>
                <textarea name="certifications" placeholder="Certifications" class="mb-2 block w-full rounded-md border-gray-300">Test certifications</textarea>
                
                <select name="specialty_id" class="mb-2 block w-full rounded-md border-gray-300" required>
                    @foreach(\App\Models\MedicalSpecialty::where('is_active', true)->get() as $specialty)
                        <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Documents</label>
                
                <div class="mb-2">
                    <p class="text-sm text-gray-600 mb-1">National ID</p>
                    <input type="text" name="document_numbers[national_id]" value="ID123456" placeholder="ID Number" class="mb-1 block w-full rounded-md border-gray-300" required>
                    <input type="file" name="documents[national_id]" class="block w-full" required>
                </div>
                
                <div class="mb-2">
                    <p class="text-sm text-gray-600 mb-1">Passport Photo</p>
                    <input type="file" name="documents[passport_photo]" class="block w-full" required>
                </div>
                
                <div class="mb-2">
                    <p class="text-sm text-gray-600 mb-1">Medical License</p>
                    <input type="text" name="document_numbers[license]" value="MED123456" placeholder="License Number" class="mb-1 block w-full rounded-md border-gray-300" required>
                    <input type="file" name="documents[license]" class="block w-full" required>
                </div>
                
                <div class="mb-2">
                    <p class="text-sm text-gray-600 mb-1">Academic Certificate</p>
                    <input type="text" name="document_numbers[academic_certificate]" value="CERT123456" placeholder="Certificate Number" class="mb-1 block w-full rounded-md border-gray-300" required>
                    <input type="file" name="documents[academic_certificate]" class="block w-full" required>
                </div>
                
                <div class="mb-2">
                    <p class="text-sm text-gray-600 mb-1">Resume/CV</p>
                    <input type="file" name="documents[resume]" class="block w-full" required>
                </div>
            </div>
            
            <div>
                <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Submit Test Form
                </button>
            </div>
        </form>
        
        <div class="mt-6">
            <h3 class="text-lg font-medium mb-2">Session Messages</h3>
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
            
            <h3 class="text-lg font-medium mb-2">Validation Errors</h3>
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
