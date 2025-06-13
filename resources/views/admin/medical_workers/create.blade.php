@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-900">Add Medical Worker</h1>
        <a href="{{ route('medical_workers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('medical_workers.store') }}" method="POST" enctype="multipart/form-data" class="divide-y divide-gray-200">
            @csrf
            
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Personal Information -->
                    <div class="col-span-2">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Personal Information</h3>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Professional Information -->
                    <div class="col-span-2 mt-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Professional Information</h3>
                    </div>

                    <div>
                        <label for="specialty_id" class="block text-sm font-medium text-gray-700">Medical Specialty</label>
                        <select name="specialty_id" id="specialty_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select a specialty</option>
                            @foreach($specialties as $specialty)
                                <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                    {{ $specialty->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialty_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700">License Number</label>
                        <input type="text" name="license_number" id="license_number" value="{{ old('license_number') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('license_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="years_of_experience" class="block text-sm font-medium text-gray-700">Years of Experience</label>
                        <input type="number" name="years_of_experience" id="years_of_experience" value="{{ old('years_of_experience') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('years_of_experience')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700">Professional Bio</label>
                        <textarea name="bio" id="bio" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('bio') }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="education" class="block text-sm font-medium text-gray-700">Education</label>
                        <textarea name="education" id="education" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('education') }}</textarea>
                        @error('education')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="certifications" class="block text-sm font-medium text-gray-700">Certifications</label>
                        <textarea name="certifications" id="certifications" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('certifications') }}</textarea>
                        @error('certifications')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Document Upload -->
                    <div class="col-span-2 mt-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Required Documents</h3>
                        <div class="space-y-6">
                            <!-- National ID -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">National ID</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="national_id_number" class="block text-sm font-medium text-gray-700">ID Number</label>
                                        <input type="text" name="document_numbers[national_id]" id="national_id_number" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Upload ID Scan</label>
                                        <input type="file" name="documents[national_id]" accept=".pdf,.jpg,.jpeg,.png" required
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>
                                </div>
                            </div>

                            <!-- Passport Photo -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Passport Photo</h4>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Upload Recent Passport Photo</label>
                                    <input type="file" name="documents[passport_photo]" accept=".jpg,.jpeg,.png" required
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="mt-1 text-sm text-gray-500">Must be a recent color photo with white background</p>
                                </div>
                            </div>

                            <!-- Medical License -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Medical License</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="license_number_doc" class="block text-sm font-medium text-gray-700">License Number</label>
                                        <input type="text" name="document_numbers[license]" id="license_number_doc" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Upload License</label>
                                        <input type="file" name="documents[license]" accept=".pdf,.jpg,.jpeg,.png" required
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Certificate -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Academic Certificate</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="certificate_number" class="block text-sm font-medium text-gray-700">Certificate Number</label>
                                        <input type="text" name="document_numbers[academic_certificate]" id="certificate_number" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Upload Certificate</label>
                                        <input type="file" name="documents[academic_certificate]" accept=".pdf,.jpg,.jpeg,.png" required
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>
                                </div>
                            </div>

                            <!-- Resume/CV -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Resume/CV</h4>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Upload Resume/CV</label>
                                    <input type="file" name="documents[resume]" accept=".pdf,.doc,.docx" required
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                            </div>

                            <!-- Additional Documents -->
                            <div class="border rounded-lg p-4">
                                <h4 class="text-md font-medium text-gray-900 mb-3">Additional Documents</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="additional_doc_type" class="block text-sm font-medium text-gray-700">Document Type</label>
                                        <input type="text" name="additional_documents[0][type]" id="additional_doc_type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            placeholder="e.g., Certification, Award, etc.">
                                    </div>
                                    <div>
                                        <label for="additional_doc_number" class="block text-sm font-medium text-gray-700">Document Number (if applicable)</label>
                                        <input type="text" name="additional_documents[0][number]" id="additional_doc_number"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Upload Document</label>
                                        <input type="file" name="additional_documents[0][file]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    </div>
                                </div>
                                <button type="button" id="add_more_docs" class="mt-4 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add More Documents
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Medical Worker
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add drag and drop functionality for document upload
    const dropzone = document.querySelector('input[type="file"]').closest('div');
    const fileInput = document.querySelector('input[type="file"]');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropzone.classList.add('border-blue-300', 'bg-blue-50');
    }

    function unhighlight(e) {
        dropzone.classList.remove('border-blue-300', 'bg-blue-50');
    }

    dropzone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
    }

    // Handle additional documents
    let additionalDocCount = 1;
    const addMoreDocsBtn = document.getElementById('add_more_docs');
    const additionalDocsContainer = addMoreDocsBtn.closest('.border').querySelector('.space-y-4');

    addMoreDocsBtn.addEventListener('click', () => {
        const newDocHtml = `
            <div class="border-t pt-4 mt-4">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Document Type</label>
                        <input type="text" name="additional_documents[${additionalDocCount}][type]"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="e.g., Certification, Award, etc.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Document Number (if applicable)</label>
                        <input type="text" name="additional_documents[${additionalDocCount}][number]"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Upload Document</label>
                        <input type="file" name="additional_documents[${additionalDocCount}][file]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <button type="button" class="remove-doc text-sm text-red-600 hover:text-red-900">
                        <i class="fas fa-trash mr-1"></i> Remove Document
                    </button>
                </div>
            </div>
        `;
        additionalDocsContainer.insertAdjacentHTML('beforeend', newDocHtml);
        additionalDocCount++;

        // Add event listener to the new remove button
        const newRemoveBtn = additionalDocsContainer.querySelector('.remove-doc:last-child');
        newRemoveBtn.addEventListener('click', (e) => {
            e.target.closest('.border-t').remove();
        });
    });
</script>
@endpush
