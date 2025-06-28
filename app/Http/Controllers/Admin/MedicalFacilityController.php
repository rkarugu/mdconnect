<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalFacility;
use App\Models\FacilityDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MedicalFacilityController extends Controller
{
    /**
     * Display a listing of the medical facilities.
     */
    public function index(Request $request)
    {
        $query = MedicalFacility::with(['user', 'documents'])
            ->when($request->search, function ($query, $search) {
                $query->where('facility_name', 'like', "%{$search}%")
                    ->orWhere('license_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            });

        $facilities = $query->latest()->paginate(10);

        return view('admin.medical_facilities.index', compact('facilities'));
    }
    
    /**
     * Show the form for creating a new medical facility.
     */
    public function create()
    {
        return view('admin.medical_facilities.create');
    }
    
    /**
     * Store a newly created medical facility in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'facility_name' => 'required|string|max:255',
            'facility_type' => 'required|string|max:100',
            'license_number' => 'required|string|max:100|unique:medical_facilities',
            'tax_id' => 'nullable|string|max:100',
            'bed_capacity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
            'document_numbers' => 'nullable|array',
            'document_numbers.*' => 'nullable|string|max:100',
            'facility_photo' => 'nullable|image|max:5120',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create user account for the facility
            $user = new User();
            $user->name = $request->facility_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            // Assign 'Facility Admin' role to the new user
            $user->assignRole('Facility Admin');
            
            // Create medical facility
            $facility = new MedicalFacility();
            $facility->user_id = $user->id;
            $facility->facility_name = $request->facility_name;
            $facility->facility_type = $request->facility_type;
            $facility->license_number = $request->license_number;
            $facility->tax_id = $request->tax_id;
            $facility->bed_capacity = $request->bed_capacity;
            $facility->description = $request->description;
            $facility->email = $request->email;
            $facility->phone = $request->phone;
            $facility->website = $request->website;
            $facility->address = $request->address;
            $facility->city = $request->city;
            $facility->state = $request->state;
            $facility->postal_code = $request->postal_code;
            $facility->country = $request->country;
            $facility->status = 'pending';
            
            // Note: facility_photo column doesn't exist in the database yet
            // We'll skip photo upload for now
            
            $facility->save();
            
            // Upload documents if provided
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    $path = $file->store('facility_documents/' . $facility->id, 'public');
                    
                    $documentTitle = ucfirst(str_replace('_', ' ', $type));
                    if ($type == 'license') $documentTitle = 'Facility License';
                    if ($type == 'tax') $documentTitle = 'Tax Certificate';
                    if ($type == 'registration') $documentTitle = 'Registration Certificate';
                    
                    $document = new FacilityDocument();
                    $document->medical_facility_id = $facility->id;
                    $document->title = $documentTitle;
                    $document->document_type = $documentTitle;
                    $document->document_number = $request->document_numbers[$type] ?? null;
                    $document->file_path = $path;
                    $document->mime_type = $file->getMimeType();
                    $document->file_size = $file->getSize();
                    $document->status = 'pending';
                    $document->save();
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('medical_facilities.show', $facility)
                ->with('success', 'Medical facility created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error creating medical facility: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified medical facility.
     */
    public function show(MedicalFacility $medical_facility)
    {
        $medical_facility->load(['user', 'documents']);
        return view('admin.medical_facilities.show', compact('medical_facility'));
    }

    /**
     * Show the form for editing the specified medical facility.
     */
    public function edit(MedicalFacility $medical_facility)
    {
        $medical_facility->load(['user', 'documents']);
        
        // Group documents by type
        $documents = collect([
            'license' => null,
            'tax' => null,
            'registration' => null,
            'additional' => []
        ]);

        foreach ($medical_facility->documents as $document) {
            switch ($document->document_type) {
                case 'Facility License':
                    $documents['license'] = $document;
                    break;
                case 'Tax Certificate':
                    $documents['tax'] = $document;
                    break;
                case 'Registration Certificate':
                    $documents['registration'] = $document;
                    break;
                default:
                    $documents['additional'][] = $document;
                    break;
            }
        }

        return view('admin.medical_facilities.edit', compact('medical_facility', 'documents'));
    }

    /**
     * Update the status of the specified medical facility.
     */
    public function updateStatus(Request $request, MedicalFacility $medical_facility)
    {
        $request->validate([
            'status' => 'required|in:pending,verified,approved,rejected,suspended',
            'status_reason' => 'required_if:status,rejected,suspended|nullable|string|max:255',
        ]);

        try {
            $medical_facility->updateStatus($request->status, $request->status_reason);

            return redirect()
                ->route('medical_facilities.show', $medical_facility)
                ->with('success', 'Medical facility status updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating facility status: ' . $e->getMessage());
        }
    }

    /**
     * Preview a facility document.
     */
    public function previewDocument(MedicalFacility $medical_facility, FacilityDocument $document)
    {
        if ($document->medical_facility_id !== $medical_facility->id) {
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

    /**
     * Show document verification page for a facility.
     */
    public function documents(MedicalFacility $medical_facility)
    {
        $medical_facility->load('documents');
        $documents = $medical_facility->documents;
        
        return view('admin.medical_facilities.documents', [
            'medical_facility' => $medical_facility,
            'documents' => $documents
        ]);
    }
    
    /**
     * Verify a document for a medical facility.
     */
    public function verifyDocument(Request $request, MedicalFacility $medical_facility, FacilityDocument $document)
    {
        if ($document->medical_facility_id !== $medical_facility->id) {
            abort(404);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:255'
        ]);

        try {
            $document->updateVerificationStatus(
                $request->status,
                $request->rejection_reason,
                auth()->id()
            );
            
            // Check if redirect should go to documents page or show page
            $redirectRoute = $request->has('redirect_to_documents') 
                ? 'admin.medical_facilities.documents' 
                : 'admin.medical_facilities.show';

            return redirect()
                ->route($redirectRoute, $medical_facility)
                ->with('success', 'Document verification status updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating document status: ' . $e->getMessage());
        }
    }
    
    /**
     * Verify all pending documents for a medical facility.
     */
    public function verifyAllDocuments(Request $request, MedicalFacility $medical_facility)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:255'
        ]);
        
        try {
            $pendingDocuments = $medical_facility->documents()->where('status', 'pending')->get();
            
            foreach ($pendingDocuments as $document) {
                $document->updateVerificationStatus(
                    $request->status,
                    $request->rejection_reason,
                    auth()->id()
                );
            }
            
            $count = $pendingDocuments->count();
            $status = $request->status === 'approved' ? 'approved' : 'rejected';
            
            return redirect()
                ->route('medical_facilities.documents', $medical_facility)
                ->with('success', "{$count} documents have been {$status} successfully.");
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error updating document statuses: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of facilities pending verification.
     */
    public function verification()
    {
        $facilities = MedicalFacility::with(['user', 'documents'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('admin.medical_facilities.verification', compact('facilities'));
    }

    /**
     * Display a listing of verified facilities pending approval.
     */
    public function approval()
    {
        $facilities = MedicalFacility::with(['user', 'documents'])
            ->where('status', 'verified')
            ->latest()
            ->paginate(10);

        return view('admin.medical_facilities.approval', compact('facilities'));
    }

    /**
     * Verify a medical facility.
     */
    public function verify(MedicalFacility $medical_facility)
    {
        try {
            // Check if all documents are approved
            $pendingDocuments = $medical_facility->documents()->where('status', 'pending')->count();
            if ($pendingDocuments > 0) {
                return back()->with('error', 'Cannot verify facility. Some documents are still pending approval.');
            }

            $medical_facility->updateStatus('verified');
            return redirect()
                ->route('medical_facilities.verification')
                ->with('success', 'Medical facility verified successfully. Ready for final approval.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error verifying medical facility: ' . $e->getMessage());
        }
    }

    /**
     * Approve a medical facility.
     */
    public function approve(MedicalFacility $medical_facility)
    {
        try {
            if ($medical_facility->status !== 'verified') {
                return back()->with('error', 'Cannot approve facility. Facility must be verified first.');
            }

            $medical_facility->updateStatus('approved');
            return redirect()
                ->route('medical_facilities.approval')
                ->with('success', 'Medical facility approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving medical facility: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a medical facility.
     */
    public function reject(Request $request, MedicalFacility $medical_facility)
    {
        $request->validate([
            'status_reason' => 'required|string|max:255',
        ]);

        try {
            $medical_facility->updateStatus('rejected', $request->status_reason);
            return redirect()
                ->route('medical_facilities.approval')
                ->with('success', 'Medical facility rejected successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error rejecting medical facility: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified medical facility from storage.
     */
    public function destroy(MedicalFacility $medical_facility)
    {
        try {
            // Delete associated documents from storage
            foreach ($medical_facility->documents as $document) {
                Storage::delete($document->file_path);
            }

            $medical_facility->delete(); // This will soft delete the facility
            $medical_facility->user->delete(); // This will delete the associated user

            return redirect()->route('medical_facilities.index')
                ->with('success', 'Medical facility deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting medical facility: ' . $e->getMessage());
        }
    }
    
    /**
     * Show the form to upload documents for a medical facility.
     */
    public function showDocumentUploadForm(MedicalFacility $medical_facility)
    {
        // Define the required document types
        $documentTypes = [
            'license' => 'Facility License',
            'registration' => 'Business Registration',
            'tax' => 'Tax Certificate',
            'insurance' => 'Liability Insurance',
            'accreditation' => 'Accreditation Certificate',
            'other' => 'Other Documents'
        ];
        
        // Get currently uploaded documents grouped by type
        $uploadedDocuments = $medical_facility->documents->groupBy('document_type');
        
        return view('admin.medical_facilities.upload_documents', [
            'medical_facility' => $medical_facility,
            'documentTypes' => $documentTypes,
            'uploadedDocuments' => $uploadedDocuments
        ]);
    }
    
    /**
     * Upload documents for a medical facility.
     */
    public function uploadDocuments(Request $request, MedicalFacility $medical_facility)
    {
        $request->validate([
            'document_type' => 'required|string',
            'document_title' => 'required|string|max:255',
            'document_number' => 'nullable|string|max:100',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240'
        ]);
        
        try {
            DB::beginTransaction();
            
            $file = $request->file('document_file');
            $path = $file->store('facility_documents/' . $medical_facility->id, 'public');
            
            $document = new FacilityDocument();
            $document->medical_facility_id = $medical_facility->id;
            $document->title = $request->document_title;
            $document->document_type = $request->document_type;
            $document->document_number = $request->document_number;
            $document->file_path = $path;
            $document->mime_type = $file->getMimeType();
            $document->file_size = $file->getSize();
            $document->status = 'pending';
            $document->save();
            
            DB::commit();
            
            return redirect()
                ->route('medical_facilities.documents.upload', $medical_facility)
                ->with('success', 'Document uploaded successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error uploading document: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a facility document.
     */
    public function deleteDocument(MedicalFacility $medical_facility, FacilityDocument $document)
    {
        try {
            // Ensure the document belongs to this facility
            if ($document->medical_facility_id !== $medical_facility->id) {
                abort(404);
            }
            
            // Only allow deletion of pending documents
            if ($document->status !== 'pending') {
                return back()->with('error', 'Only pending documents can be deleted.');
            }
            
            // Delete the file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            // Delete the document record
            $document->delete();
            
            return back()->with('success', 'Document deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting document: ' . $e->getMessage());
        }
    }
}
