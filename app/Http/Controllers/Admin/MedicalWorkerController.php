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
use Illuminate\Support\Str;
use App\Models\Role;

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
                $query->where('specialty_id', $specialty);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            });

        $workers = $query->latest()->paginate(10);
        $specialties = MedicalSpecialty::all();

        return view('admin.medical_workers.index', compact('workers', 'specialties'));
    }

    public function create()
    {
        $specialties = MedicalSpecialty::where('is_active', true)->get();
        return view('admin.medical_workers.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        \Log::info('Store method called', ['request' => $request->all()]);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'specialty_id' => 'required|exists:medical_specialties,id',
            'license_number' => 'required|string|max:50|unique:medical_workers',
            'years_of_experience' => 'required|integer|min:0',
            'bio' => 'required|string',
            'education' => 'required|string',
            'certifications' => 'nullable|string',
            'document_numbers.national_id' => 'required|string|max:50',
            'document_numbers.license' => 'required|string|max:50',
            'document_numbers.academic_certificate' => 'required|string|max:50',
            'documents.national_id' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.passport_photo' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'documents.license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.academic_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.resume' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        DB::beginTransaction();

        try {
            // Get the Medical Worker role
            $medicalWorkerRole = Role::where('name', 'Medical Worker')->first();
            if (!$medicalWorkerRole) {
                throw new \Exception('Medical Worker role not found');
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make(Str::random(12)),
                'role_id' => $medicalWorkerRole->id,
            ]);

            // Create medical worker with initial status
            $worker = MedicalWorker::create([
                'user_id' => $user->id,
                'specialty_id' => $request->specialty_id,
                'license_number' => $request->license_number,
                'years_of_experience' => $request->years_of_experience,
                'bio' => $request->bio,
                'education' => $request->education,
                'certifications' => $request->certifications,
                'status' => 'pending',
            ]);

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
                    $file = $request->file("documents.{$key}");
                    $path = $file->store('medical-documents', 'public');
                    
                    MedicalDocument::create([
                        'medical_worker_id' => $worker->id,
                        'document_type' => $title,
                        'title' => $title,
                        'document_number' => $request->input("document_numbers.{$key}"),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'status' => 'pending'
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.medical_workers.index')
                ->with('success', 'Medical worker registration submitted successfully. Your application is pending review by the administrator.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating medical worker', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->withInput()
                ->with('error', 'Error creating medical worker: ' . $e->getMessage());
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
            'email' => 'required|string|email|max:255|unique:users,email,' . $medical_worker->user_id,
            'phone' => 'required|string|max:20',
            'specialty_id' => 'required|exists:medical_specialties,id',
            'license_number' => 'required|string|max:50|unique:medical_workers,license_number,' . $medical_worker->id,
            'years_of_experience' => 'required|integer|min:0',
            'bio' => 'required|string',
            'education' => 'required|string',
            'certifications' => 'nullable|string',
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
            // Handle profile picture upload
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($medical_worker->user->profile_picture) {
                    Storage::disk('public')->delete($medical_worker->user->profile_picture);
                }
                
                // Store new profile picture
                $path = $request->file('profile_picture')->store('profile-pictures', 'public');
                $userData['profile_picture'] = $path;
            } elseif ($request->has('remove_profile_picture')) {
                // Remove profile picture if requested
                if ($medical_worker->user->profile_picture) {
                    Storage::disk('public')->delete($medical_worker->user->profile_picture);
                }
                $userData['profile_picture'] = null;
            }

            // Update user
            $medical_worker->user->update($userData);

            // Update medical worker
            $medical_worker->update([
                'specialty_id' => $request->specialty_id,
                'license_number' => $request->license_number,
                'years_of_experience' => $request->years_of_experience,
                'bio' => $request->bio,
                'education' => $request->education,
                'certifications' => $request->certifications,
            ]);

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
                ->route('admin.medical_workers.show', $medical_worker)
                ->with('success', 'Medical worker updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error updating medical worker: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, MedicalWorker $medical_worker)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,suspended',
            'status_reason' => 'required_if:status,rejected,suspended|nullable|string|max:255',
        ]);

        try {
            $medical_worker->update([
                'status' => $request->status,
                'status_reason' => $request->status_reason,
            ]);

            return redirect()
                ->route('admin.medical_workers.show', $medical_worker)
                ->with('success', 'Medical worker status updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating medical worker status: ' . $e->getMessage());
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
            $document->update([
                'status' => $request->status,
                'rejection_reason' => $request->rejection_reason,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            return redirect()
                ->route('admin.medical_workers.show', $medical_worker)
                ->with('success', 'Document verification status updated successfully.');
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

            return redirect()->route('admin.medical_workers.index')
                ->with('success', 'Medical worker deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting medical worker: ' . $e->getMessage());
        }
    }

    public function verification()
    {
        $workers = MedicalWorker::with(['user', 'specialty', 'documents'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        // Debug profile pictures
        foreach ($workers as $worker) {
            \Log::info('Medical Worker Profile Picture', [
                'worker_id' => $worker->id,
                'user_id' => $worker->user->id,
                'profile_picture' => $worker->user->profile_picture,
                'exists' => $worker->user->profile_picture ? Storage::exists('public/' . $worker->user->profile_picture) : false
            ]);
        }

        return view('admin.medical_workers.verification', compact('workers'));
    }

    public function approval()
    {
        $workers = MedicalWorker::with(['user', 'specialty', 'documents'])
            ->where('status', 'verified')
            ->latest()
            ->paginate(10);

        return view('admin.medical_workers.approval', compact('workers'));
    }

    public function verify(MedicalWorker $worker)
    {
        try {
            // Check if all documents are approved
            $pendingDocuments = $worker->documents()->where('status', 'pending')->count();
            if ($pendingDocuments > 0) {
                return back()->with('error', 'Cannot verify worker. Some documents are still pending approval.');
            }

            $worker->update(['status' => 'verified']);
            return redirect()
                ->route('admin.medical_workers.verification')
                ->with('success', 'Medical worker verified successfully. Ready for final approval.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error verifying medical worker: ' . $e->getMessage());
        }
    }

    public function approve(MedicalWorker $worker)
    {
        try {
            if ($worker->status !== 'verified') {
                return back()->with('error', 'Cannot approve worker. Worker must be verified first.');
            }

            $worker->update(['status' => 'approved']);
            return redirect()
                ->route('admin.medical_workers.approval')
                ->with('success', 'Medical worker approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving medical worker: ' . $e->getMessage());
        }
    }

    public function reject(MedicalWorker $worker)
    {
        try {
            $worker->update(['status' => 'rejected']);
            return redirect()
                ->route('admin.medical_workers.approval')
                ->with('success', 'Medical worker rejected successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error rejecting medical worker: ' . $e->getMessage());
        }
    }
}
