<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalWorker;
use App\Models\MedicalSpecialty;
use App\Models\MedicalDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Notifications\MedicalWorkerRegistered;
use App\Notifications\MedicalWorkerApproved;

class MedicalWorkerController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalWorker::with(['user', 'specialty', 'documents'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('license_number', 'like', "%{$search}%");
            })
            ->when($request->specialty, function ($query, $specialty) {
                $query->where('medical_specialty_id', $specialty);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            });

        $workers = $query->latest()->paginate(10);
        $specialties = MedicalSpecialty::all();

        return view('admin.medical_workers.index', compact('workers', 'specialties'));
    }

    public function create(Request $request)
    {
        $specialties = MedicalSpecialty::where('is_active', true)->get();
        
        // Check if this is an external registration (from public route) or admin-initiated creation
        if ($request->route()->getName() === 'medical_workers.register') {
            // External registration form (public route)
            return view('admin.medical_workers.medicregistration', compact('specialties'));
        } else {
            // Internal creation form (admin route)
            return view('admin.medical_workers.create', compact('specialties'));
        }
    }

    public function store(Request $request)
    {
        \Log::info('Store method called', [
            'request' => $request->all(), 
            'files' => $request->hasFile('documents') ? 'Has documents' : 'No documents',
            'route' => $request->route()->getName(),
            'referer' => $request->headers->get('referer')
        ]);
        
        \Log::info('Starting validation');
        
        // Define validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:medical_workers,email',
            'phone' => 'required|string|max:20',
            'medical_specialty_id' => 'required|exists:medical_specialties,id',
            'license_number' => 'required|string|max:50|unique:medical_workers',
            'years_of_experience' => 'required|integer|min:0',
            'bio' => 'required|string',
            'education' => 'required|string',
            'certifications' => 'nullable|string',
            // Support both array and dot notation for document numbers
            'document_numbers.national_id' => 'required_without:document_numbers.*.national_id|string|max:50',
            'document_numbers.license' => 'required_without:document_numbers.*.license|string|max:50',
            'document_numbers.academic_certificate' => 'required_without:document_numbers.*.academic_certificate|string|max:50',
            'document_numbers.*.national_id' => 'nullable|string|max:50',
            'document_numbers.*.license' => 'nullable|string|max:50',
            'document_numbers.*.academic_certificate' => 'nullable|string|max:50',
            
            // Support both array and dot notation for documents
            'documents.national_id' => 'required_without:documents.*.national_id|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.passport_photo' => 'required_without:documents.*.passport_photo|file|mimes:jpg,jpeg,png|max:5120',
            'documents.license' => 'required_without:documents.*.license|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.academic_certificate' => 'required_without:documents.*.academic_certificate|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.resume' => 'required_without:documents.*.resume|file|mimes:pdf,doc,docx|max:10240',
            
            // Alternative array notation
            'documents.*.national_id' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.*.passport_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'documents.*.license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.*.academic_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.*.resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ];
        
        // Validate the request
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);
        
        // Check for validation errors
        if ($validator->fails()) {
            \Log::error('Validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            
            $errorMessage = 'Please check the form for errors and try again.';
            $errorDetails = 'Validation errors: ' . print_r($validator->errors()->toArray(), true);
            
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', $errorMessage)
                ->with('errorDetails', $errorDetails);
        }
        
        try {
            \Log::info('Beginning database transaction');
            DB::beginTransaction();
            \Log::info('Starting medical worker creation');
            \Log::info('Creating medical worker record with data', [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'medical_specialty_id' => $request->medical_specialty_id,
                'license_number' => $request->license_number
            ]);
        
            // Create medical worker with initial status and authentication fields
            // Since the database schema has been updated to include auth fields directly in medical_workers table
            $worker = MedicalWorker::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make(Str::random(12)), // Temporary password until approved
                'medical_specialty_id' => $request->medical_specialty_id,
                'license_number' => $request->license_number,
                'years_of_experience' => $request->years_of_experience,
                'bio' => $request->bio,
                'education' => $request->education,
                'certifications' => $request->certifications,
                'status' => 'pending',
            ]);
        
            \Log::info('Medical worker record created', ['worker_id' => $worker->id]);

            // Handle required documents
            $requiredDocuments = [
                'national_id' => 'National ID',
                'passport_photo' => 'Passport Photo',
                'license' => 'Medical License',
                'academic_certificate' => 'Academic Certificate',
                'resume' => 'Resume/CV'
            ];
            
            \Log::info('Starting document processing', ['document_types' => array_keys($requiredDocuments)]);  
            // Check what format the files are submitted in
            \Log::info('Document request format', [
                'has_documents_array' => $request->has('documents'),
                'documents_is_array' => $request->has('documents') ? is_array($request->input('documents')) : false,
                'file_keys' => $request->hasFile('documents') ? array_keys($request->file('documents')) : [],
            ]);
            
            // Process and store each required document
            foreach ($requiredDocuments as $key => $title) {
                \Log::info("Processing document: {$title}", ['key' => $key]);
                
                // The form submits files as an array: documents[national_id]
                if ($request->hasFile("documents.{$key}")) {
                    // Dot notation (unlikely but supported for backward compatibility)
                    $file = $request->file("documents.{$key}");
                    $path = $file->store('medical-documents', 'public');
                    
                    $docNumber = $request->input("document_numbers.{$key}");
                    
                    MedicalDocument::create([
                        'medical_worker_id' => $worker->id,
                        'document_type' => $title,
                        'title' => $title,
                        'document_number' => $docNumber,
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'status' => 'pending'
                    ]);
                } 
                // Array notation (most likely scenario based on the form)
                elseif ($request->file("documents") && is_array($request->file("documents")) && array_key_exists($key, $request->file("documents"))) {
                    \Log::info("Using array notation for document: {$title}", ['key' => $key]);
                    $file = $request->file("documents")[$key];
                    $path = $file->store('medical-documents', 'public');
                    
                    // Get document number from array if available
                    $docNumber = null;
                    if (is_array($request->input("document_numbers")) && array_key_exists($key, $request->input("document_numbers"))) {
                        $docNumber = $request->input("document_numbers")[$key];
                    }
                    
                    MedicalDocument::create([
                        'medical_worker_id' => $worker->id,
                        'document_type' => $title,
                        'title' => $title,
                        'document_number' => $docNumber,
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'status' => 'pending'
                    ]);
                }
            }
            
            // Handle additional documents if present
            if ($request->has('additional_documents')) {
                $additionalDocs = $request->input('additional_documents');
                
                foreach ($additionalDocs as $index => $docData) {
                    if (isset($docData['file']) && $docData['file'] instanceof \Illuminate\Http\UploadedFile) {
                        // File is directly accessible
                        $file = $docData['file'];
                        $path = $file->store('medical-documents', 'public');
                        
                        MedicalDocument::create([
                            'medical_worker_id' => $worker->id,
                            'document_type' => $docData['type'] ?? 'Additional Document',
                            'title' => $docData['type'] ?? 'Additional Document',
                            'document_number' => $docData['number'] ?? null,
                            'file_path' => $path,
                            'mime_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                            'status' => 'pending'
                        ]);
                    } 
                    // Check if the file is in the files array
                    elseif ($request->hasFile("additional_documents.{$index}.file") || 
                           (isset($request->file('additional_documents')[$index]['file']))) {
                        
                        $file = $request->hasFile("additional_documents.{$index}.file") ? 
                               $request->file("additional_documents.{$index}.file") : 
                               $request->file('additional_documents')[$index]['file'];
                        
                        $path = $file->store('medical-documents', 'public');
                        
                        MedicalDocument::create([
                            'medical_worker_id' => $worker->id,
                            'document_type' => $docData['type'] ?? 'Additional Document',
                            'title' => $docData['type'] ?? 'Additional Document',
                            'document_number' => $docData['number'] ?? null,
                            'file_path' => $path,
                            'mime_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                            'status' => 'pending'
                        ]);
                    }
                }
            }
            
            // Send registration confirmation notification directly to medical worker
            \Log::info('Sending notification to medical worker', ['email' => $worker->email]);
            try {
                $worker->notify(new MedicalWorkerRegistered($worker));
                \Log::info('Notification sent successfully');
            } catch (\Exception $e) {
                \Log::warning('Failed to send notification but continuing process', [
                    'error' => $e->getMessage(),
                    'email' => $worker->email
                ]);
                // Don't throw the exception - allow the process to continue
            }
            
            \Log::info('Attempting to commit transaction');
            DB::commit();
            \Log::info('Transaction committed successfully');

            // Check if this is an external registration or admin-created worker
            if ($request->route()->getName() === 'medical_workers.store' && $request->headers->get('referer') && 
                str_contains($request->headers->get('referer'), 'register')) {
                \Log::info('External registration detected');
                // External registration - redirect to a public thank you page
                return redirect()->route('medical_workers.register')
                    ->with('success', 'Your application has been submitted successfully. You will receive an email confirmation shortly. Our team will review your documents and contact you regarding the status of your application.');
            } else {
                // Admin-created worker - redirect to admin panel
                \Log::info('Admin creation detected');
                return redirect()
                    ->route('medical_workers.index')
                    ->with('success', 'Medical worker registration submitted successfully. The application is pending review.');
            }

        } catch (\Exception $e) {
            \Log::error('Exception caught, rolling back transaction', [
                'error' => $e->getMessage(),
                'class' => get_class($e)
            ]);
            DB::rollBack();
            \Log::error('Error creating medical worker', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'previous' => $e->getPrevious() ? $e->getPrevious()->getMessage() : null
            ]);
            
            // For database exceptions, show more specific messages
            $errorMessage = 'Error creating medical worker: ' . $e->getMessage();
            
            // Add very detailed error info to help debug
            $errorDetails = 'Exception class: ' . get_class($e) . '<br>';
            $errorDetails .= 'Message: ' . $e->getMessage() . '<br>';
            $errorDetails .= 'File: ' . $e->getFile() . ' (Line: ' . $e->getLine() . ')<br>';
            
            if ($e instanceof \Illuminate\Database\QueryException) {
                $errorDetails .= 'SQL: ' . ($e->getSql() ?? 'N/A') . '<br>';
                $errorDetails .= 'Bindings: ' . print_r($e->getBindings() ?? [], true) . '<br>';
                
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $errorMessage = 'A medical worker with this email or license number already exists.';
                } else {
                    $errorMessage = 'Database error occurred. Please try again or contact support.';
                }
            } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                $errorMessage = 'Please check the form for errors and try again.';
                $errorDetails .= 'Validation errors: ' . print_r($e->errors(), true) . '<br>';
            }
            
            // Pass both the user-friendly message and the detailed error info
            return back()
                ->withInput()
                ->with('error', $errorMessage)
                ->with('errorDetails', $errorDetails);
        }
    }

    public function show(MedicalWorker $medical_worker)
    {
        $medical_worker->load(['user', 'specialty', 'documents']);
        return view('admin.medical_workers.show', compact('medical_worker'));
    }

    public function edit(MedicalWorker $medical_worker)
    {
        $medical_worker->load(['user', 'specialty', 'documents']);
        $specialties = MedicalSpecialty::all();

        // Group documents by type
        $documents = collect([
            'national_id' => null,
            'passport_photo' => null,
            'license' => null,
            'academic_certificate' => null,
            'resume' => null,
            'additional' => []
        ]);

        foreach ($medical_worker->documents as $document) {
            switch ($document->document_type) {
                case 'National ID':
                    $documents['national_id'] = $document;
                    break;
                case 'Passport Photo':
                    $documents['passport_photo'] = $document;
                    break;
                case 'Medical License':
                    $documents['license'] = $document;
                    break;
                case 'Academic Certificate':
                    $documents['academic_certificate'] = $document;
                    break;
                case 'Resume/CV':
                    $documents['resume'] = $document;
                    break;
                default:
                    $documents['additional'][] = $document;
                    break;
            }
        }

        return view('admin.medical_workers.edit', compact('medical_worker', 'specialties', 'documents'));
    }

    public function update(Request $request, MedicalWorker $medical_worker)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:medical_workers,email,' . $medical_worker->id,
            'phone' => 'required|string|max:20',
            'medical_specialty_id' => 'required|exists:medical_specialties,id',
            'license_number' => 'required|string|max:50|unique:medical_workers,license_number,' . $medical_worker->id,
            'years_of_experience' => 'required|integer|min:0',
            'bio' => 'required|string',
            'education' => 'required|string',
            'certifications' => 'nullable|string',
            'status' => 'sometimes|in:pending,approved,rejected,suspended',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document_numbers.national_id' => 'required_without:documents.national_id|string|max:50',
            'document_numbers.license' => 'required_without:documents.license|string|max:50',
            'document_numbers.academic_certificate' => 'required_without:documents.academic_certificate|string|max:50',
            'documents.national_id' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.passport_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'documents.license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.academic_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'additional_documents.*.type' => 'nullable|string|max:255',
            'additional_documents.*.number' => 'nullable|string|max:50',
            'additional_documents.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction();

        try {
            // Create the data array for updating the medical worker
            $workerData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'medical_specialty_id' => $request->medical_specialty_id,
                'license_number' => $request->license_number,
                'years_of_experience' => $request->years_of_experience,
                'bio' => $request->bio,
                'education' => $request->education,
                'certifications' => $request->certifications,
            ];

            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($medical_worker->profile_picture) {
                    Storage::disk('public')->delete($medical_worker->profile_picture);
                }
                
                // Store new profile picture
                $path = $request->file('profile_picture')->store('profile-pictures', 'public');
                $workerData['profile_picture'] = $path;
            } elseif ($request->has('remove_profile_picture')) {
                // Remove profile picture if requested
                if ($medical_worker->profile_picture) {
                    Storage::disk('public')->delete($medical_worker->profile_picture);
                }
                $workerData['profile_picture'] = null;
            }

            // If status is being changed to 'approved' and no profile picture is set, use the passport photo
            if ($request->status === 'approved' && !isset($workerData['profile_picture'])) {
                $passportPhoto = $medical_worker->documents()->where('document_type', 'Passport Photo')->first();
                if ($passportPhoto) {
                    $workerData['profile_picture'] = $passportPhoto->file_path;
                }
            }

            // Update medical worker with all data
            $medical_worker->update($workerData);

            // Handle required documents
            $requiredDocuments = [
                'national_id' => 'National ID',
                'passport_photo' => 'Passport Photo',
                'license' => 'Medical License',
                'academic_certificate' => 'Academic Certificate',
                'resume' => 'Resume/CV'
            ];

            foreach ($requiredDocuments as $key => $title) {
                if ($request->hasFile("documents.{$key}")) {
                    // Delete old document file if it exists
                    $oldDoc = $medical_worker->documents()
                        ->where('document_type', $title)
                        ->first();
                    
                    if ($oldDoc) {
                        Storage::disk('public')->delete($oldDoc->file_path);
                        $oldDoc->delete();
                    }

                    // Upload and create new document
                    $file = $request->file("documents.{$key}");
                    $path = $file->store('medical-documents', 'public');
                    
                    MedicalDocument::create([
                        'medical_worker_id' => $medical_worker->id,
                        'document_type' => $title,
                        'title' => $title,
                        'document_number' => $request->input("document_numbers.{$key}"),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'status' => 'pending'
                    ]);
                } elseif ($request->has("document_numbers.{$key}")) {
                    // Update document number if only the number has changed
                    $existingDoc = $medical_worker->documents()
                        ->where('document_type', $title)
                        ->first();
                    
                    if ($existingDoc) {
                        $existingDoc->update([
                            'document_number' => $request->input("document_numbers.{$key}")
                        ]);
                    }
                }
            }
            
            // Handle additional documents
            if ($request->has('additional_documents')) {
                foreach ($request->additional_documents as $index => $doc) {
                    if (isset($doc['file']) && $doc['file'] instanceof UploadedFile) {
                        $file = $doc['file'];
                        $path = $file->store('medical-documents', 'public');
                        
                        MedicalDocument::create([
                            'medical_worker_id' => $medical_worker->id,
                            'document_type' => 'Additional Document',
                            'title' => $doc['type'],
                            'document_number' => $doc['number'],
                            'file_path' => $path,
                            'mime_type' => $file->getMimeType(),
                            'file_size' => $file->getSize(),
                            'status' => 'pending'
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('medical_workers.show', $medical_worker)
                ->with('success', 'Medical worker updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Error updating medical worker: ' . $e->getMessage());
        }
    }

    public function previewDocument(MedicalWorker $medical_worker, MedicalDocument $document)
    {
        if ($document->medical_worker_id !== $medical_worker->id) {
            abort(404);
        }

        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404);
        }

        $mimeType = $document->mime_type;
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($document->file_path) . '"'
        ];
        
        return response()->file($filePath, $headers);
    }

    public function verifyDocument(Request $request, MedicalWorker $medical_worker, MedicalDocument $document)
    {
        if ($document->medical_worker_id !== $medical_worker->id) {
            abort(404);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:255'
        ]);

        try {
            // Update document status
            $document->update([
                'status' => $request->status,
                'status_reason' => $request->rejection_reason, // Use status_reason for consistency
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            // If the approved document is the Passport Photo, update the worker's profile picture
            if ($document->document_type === 'Passport Photo' && $request->status === 'approved') {
                \Log::info('Passport Photo approved. Updating profile picture.', ['worker_id' => $medical_worker->id, 'document_id' => $document->id]);
                \Log::info('File path to be set: ' . $document->file_path);
                
                $medical_worker->profile_picture = $document->file_path;
                $medical_worker->save();
                
                $medical_worker->refresh();
                \Log::info('Worker profile picture after save: ' . $medical_worker->profile_picture);
            } else {
                \Log::info('Condition to update profile picture not met.', [
                    'document_type' => $document->document_type,
                    'expected_type' => 'Passport Photo',
                    'request_status' => $request->status,
                    'expected_status' => 'approved',
                    'type_match' => $document->document_type === 'Passport Photo',
                    'status_match' => $request->status === 'approved',
                ]);
            }
            
            // Send notification to the medical worker about document verification status
            $isVerified = ($request->status === 'approved');
            $medical_worker->notify(new \App\Notifications\MedicalWorkerDocumentVerified(
                $medical_worker, 
                $document, 
                $isVerified
            ));
            
            // Check if all documents are verified to potentially move to approval stage
            $pendingDocuments = $medical_worker->documents()->where('status', '!=', 'approved')->count();
        
            if ($pendingDocuments === 0) {
                // All documents verified, update worker status to 'approved' if currently pending
                if ($medical_worker->status === 'pending') {
                    // Find the passport photo and set it as the profile picture
                    $passportPhoto = $medical_worker->documents()->where('document_type', 'Passport Photo')->first();
                    $profilePicturePath = $passportPhoto ? $passportPhoto->file_path : null;

                    $medical_worker->update([
                        'status' => 'approved',
                        'status_reason' => 'All documents verified and approved automatically',
                        'approved_at' => now(),
                        'profile_picture' => $profilePicturePath
                    ]);
                }
            }

            return redirect()
                ->route('medical_workers.show', $medical_worker)
                ->with('success', 'Document verification status updated successfully. Notification sent to worker.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating document status: ' . $e->getMessage());
        }
    }

    public function destroy(MedicalWorker $worker)
    {
        try {
            // Delete associated documents from storage
            foreach ($worker->documents as $document) {
                Storage::delete($document->file_path);
            }

            $worker->delete(); // This will soft delete the worker
            $worker->user->delete(); // This will delete the associated user

            return redirect()->route('medical_workers.index')
                ->with('success', 'Medical worker deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting medical worker: ' . $e->getMessage());
        }
    }

    public function verification()
    {
        $workers = MedicalWorker::with(['specialty', 'documents'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        // Debug profile pictures
        foreach ($workers as $worker) {
            \Log::info('Medical Worker Profile Picture', [
                'worker_id' => $worker->id,
                'profile_picture' => $worker->profile_picture,
                'exists' => $worker->profile_picture ? Storage::exists('public/' . $worker->profile_picture) : false
            ]);
        }

        return view('admin.medical_workers.verification', compact('workers'));
    }

    // Constructor moved to the top of the class
    
    /**
     * Debug version of store method for form submission troubleshooting
     */
    public function debugStore(Request $request)
    {
        \Log::info('Debug store method called', [
            'request' => $request->all(),
            'files' => $request->allFiles(),
            'route' => $request->route()->getName(),
            'referer' => $request->headers->get('referer')
        ]);
        
        try {
            // Validate the same fields as the regular store method
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'required|string|max:20',
                'medical_specialty_id' => 'required|exists:medical_specialties,id',
                'license_number' => 'required|string|max:50|unique:medical_workers',
                'years_of_experience' => 'required|integer|min:0',
                'bio' => 'required|string',
                'education' => 'required|string',
                'certifications' => 'nullable|string',
                // Support both array and dot notation for document numbers
                'document_numbers.national_id' => 'required_without:document_numbers.*.national_id|string|max:50',
                'document_numbers.license' => 'required_without:document_numbers.*.license|string|max:50',
                'document_numbers.academic_certificate' => 'required_without:document_numbers.*.academic_certificate|string|max:50',
                'document_numbers.*.national_id' => 'nullable|string|max:50',
                'document_numbers.*.license' => 'nullable|string|max:50',
                'document_numbers.*.academic_certificate' => 'nullable|string|max:50',
                
                // Support both array and dot notation for documents
                'documents.national_id' => 'required_without:documents.*.national_id|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'documents.passport_photo' => 'required_without:documents.*.passport_photo|file|mimes:jpg,jpeg,png|max:5120',
                'documents.license' => 'required_without:documents.*.license|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'documents.academic_certificate' => 'required_without:documents.*.academic_certificate|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'documents.resume' => 'required_without:documents.*.resume|file|mimes:pdf,doc,docx|max:10240',
                
                // Alternative array notation
                'documents.*.national_id' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'documents.*.passport_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
                'documents.*.license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'documents.*.academic_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
                'documents.*.resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            ]);
            
            if ($validator->fails()) {
                \Log::error('Validation failed', ['errors' => $validator->errors()->toArray()]);
                return back()->withErrors($validator)->withInput();
            }
            
            // Check if we have files
            if ($request->hasFile('documents')) {
                \Log::info('Documents found', ['files' => $request->file('documents')]);
                
                // Test file storage
                foreach ($request->file('documents') as $key => $file) {
                    try {
                        $path = $file->store('test-uploads', 'public');
                        \Log::info('File stored successfully', [
                            'key' => $key,
                            'path' => $path,
                            'mime' => $file->getMimeType(),
                            'size' => $file->getSize()
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('File storage failed', [
                            'key' => $key,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            } else {
                \Log::warning('No documents found in the request');
            }
            
            return back()->with('success', 'Debug form processed successfully. Check the logs for details.');
            
        } catch (\Exception $e) {
            \Log::error('Debug error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return back()->withInput()->with('error', 'Debug error: ' . $e->getMessage());
        }
    }

    public function approval()
    {
        $workers = MedicalWorker::with(['specialty', 'documents'])
            ->where('status', 'pending')
            ->whereHas('documents', function ($query) {
                $query->where('status', 'approved');
            })
            ->latest()
            ->paginate(10);

        return view('admin.medical_workers.approval', compact('workers'));
    }

    public function verify(Request $request, MedicalWorker $medical_worker)
    {
        try {
            // Check if all documents are approved
            $pendingDocuments = $medical_worker->documents()->where('status', 'pending')->count();
            if ($pendingDocuments > 0) {
                return back()->with('error', 'Cannot verify worker. Some documents are still pending approval.');
            }

            // Update to approved status directly
            $medical_worker->update([
                'status' => 'approved',
                'status_reason' => $request->status_reason ?? 'All documents verified',
            ]);
            
            return redirect()
                ->route('medical_workers.show', $medical_worker)
                ->with('success', 'Medical worker verified and approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error verifying medical worker: ' . $e->getMessage());
        }
    }

    public function approve(MedicalWorker $worker)
    {
        try {
            if ($worker->status !== 'pending' && $worker->status !== 'verified') {
                return back()->with('error', 'Cannot approve worker. Worker must be pending or verified with approved documents.');
            }
            
            // Check if all documents are verified
            $pendingDocuments = $worker->documents()->where('status', '!=', 'approved')->count();
            if ($pendingDocuments > 0) {
                return back()->with('error', 'Cannot approve worker. All documents must be verified first.');
            }

            // Generate a secure random password for the worker
            $plainPassword = Str::random(10) . Str::upper(Str::random(1)) . rand(10, 99) . '!'; // Complex password with at least 1 uppercase, 2 numbers, and 1 special character
            
            // Update the worker's password directly
            $worker->update([
                'password' => Hash::make($plainPassword),
                'password_change_required' => true, // Flag to indicate the user needs to change password on first login
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id()
            ]);

            // Get the app download link from config
            $appDownloadLink = config('app.medical_worker_app_download_url', 'https://mediconnect.com/download/medical-worker-app');
            
            // Send approval notification with credentials to the worker directly
            $worker->notify(new \App\Notifications\MedicalWorkerApproved($worker, $plainPassword, $appDownloadLink));
            
            return redirect()
                ->route('medical_workers.approval')
                ->with('success', 'Medical worker approved successfully. Login credentials have been sent to their email.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving medical worker: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, MedicalWorker $worker)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        try {
            if ($worker->status === 'approved') {
                return back()->with('error', 'Cannot reject an already approved worker.');
            }
            
            // Update worker status to rejected
            $worker->update([
                'status' => 'rejected',
                'status_reason' => $request->rejection_reason, // Use rejection_reason from request
                'rejected_at' => now(),
                'rejected_by' => auth()->id()
            ]);
            
            // Send rejection notification to the worker
            $worker->notify(new \App\Notifications\MedicalWorkerRejected($worker, $request->rejection_reason));
            
            return redirect()
                ->route('medical_workers.index')
                ->with('success', 'Medical worker application rejected. A notification has been sent to the applicant.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error rejecting medical worker: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the status of a medical worker directly.
     */
    public function updateStatus(Request $request, MedicalWorker $medical_worker)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,suspended',
            'status_reason' => 'nullable|string|max:500',
        ]);
        
        try {
            // Handle special case for rejected status which requires a reason
            if ($request->status === 'rejected' && empty($request->status_reason)) {
                return back()->withInput()->with('error', 'A reason is required when rejecting a medical worker.');
            }
            
            // Update the status using the worker's updateStatus method
            $medical_worker->update([
                'status' => $request->status,
                'status_reason' => $request->status_reason,
                'last_status_change' => now(),
                'approved_at' => $request->status === 'approved' ? now() : $medical_worker->approved_at,
            ]);
            
            // Send appropriate notification based on the status
            if ($request->status === 'approved' && $medical_worker->email) {
                // Generate a secure random password for the worker
                $plainPassword = Str::random(10) . Str::upper(Str::random(1)) . rand(10, 99) . '!'; // Complex password with at least 1 uppercase, 2 numbers, and 1 special character
                
                // Find or create a user account for the medical worker
                $user = $medical_worker->user;
                
                if (!$user) {
                    // Create a user account if one doesn't exist
                    $user = User::create([
                        'name' => $medical_worker->name,
                        'email' => $medical_worker->email,
                        'password' => Hash::make($plainPassword),
                        'email_verified_at' => now(), // Mark as verified since we're approving them
                    ]);
                    
                    // Associate the user with the medical worker
                    $medical_worker->update(['user_id' => $user->id]);
                } else {
                    // Update the existing user's password
                    $user->update([
                        'password' => Hash::make($plainPassword),
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ]);
                }
                
                // Get the app download link from config
                $appDownloadLink = config('app.medical_worker_app_download_url', 'https://mediconnect.com/download/medical-worker-app');
                
                // Send approval notification with credentials
                $medical_worker->notify(new \App\Notifications\MedicalWorkerApproved($medical_worker, $plainPassword, $appDownloadLink));
            }
            
            return redirect()
                ->route('medical_workers.show', $medical_worker)
                ->with('success', 'Medical worker status updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating medical worker status: ' . $e->getMessage());
        }
    }
}
