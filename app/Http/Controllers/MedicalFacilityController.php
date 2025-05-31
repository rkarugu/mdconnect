<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicalFacility;
use App\Models\FacilityDocument;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        return view('medical_facilities.index', compact('facilities'));
    }

    /**
     * Show the form for creating a new medical facility.
     */
    public function create()
    {
        return view('medical_facilities.create');
    }

    /**
     * Store a newly created medical facility in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'facility_name' => 'required|string|max:255',
            'facility_type' => 'required|string|max:100',
            'license_number' => 'required|string|max:50|unique:medical_facilities',
            'tax_id' => 'nullable|string|max:50',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'bed_capacity' => 'nullable|integer|min:0',
            'password' => 'required|string|min:8|confirmed',
            'document_numbers.license' => 'required|string|max:50',
            'document_numbers.tax' => 'nullable|string|max:50',
            'document_numbers.registration' => 'required|string|max:50',
            'documents.license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.tax' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.registration' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'facility_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();

        try {
            // Get the Medical Facility role
            $facilityRole = Role::where('name', 'Medical Facility')->first();
            if (!$facilityRole) {
                throw new \Exception('Medical Facility role not found');
            }

            // Create user
            $user = User::create([
                'name' => $request->facility_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role_id' => $facilityRole->id,
            ]);

            // Handle facility photo
            $facilityPhotoPath = null;
            if ($request->hasFile('facility_photo')) {
                $facilityPhotoPath = $request->file('facility_photo')->store('facility-photos', 'public');
            }

            // Create medical facility
            $facility = MedicalFacility::create([
                'user_id' => $user->id,
                'facility_name' => $request->facility_name,
                'facility_type' => $request->facility_type,
                'license_number' => $request->license_number,
                'tax_id' => $request->tax_id,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'phone' => $request->phone,
                'email' => $request->email,
                'website' => $request->website,
                'description' => $request->description,
                'bed_capacity' => $request->bed_capacity,
                'status' => 'pending',
            ]);

            // Handle required documents
            $requiredDocuments = [
                'license' => 'Facility License',
                'tax' => 'Tax Certificate',
                'registration' => 'Registration Certificate'
            ];

            foreach ($requiredDocuments as $key => $title) {
                if ($request->hasFile("documents.{$key}")) {
                    $file = $request->file("documents.{$key}");
                    $path = $file->store('facility-documents', 'public');
                    
                    FacilityDocument::create([
                        'medical_facility_id' => $facility->id,
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
                ->route('medical_facilities.show', $facility)
                ->with('success', 'Medical facility registration submitted successfully. Your application is pending review by the administrator.');

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
    public function show(MedicalFacility $facility)
    {
        $facility->load(['user', 'documents']);
        return view('medical_facilities.show', compact('facility'));
    }

    /**
     * Show the form for editing the specified medical facility.
     */
    public function edit(MedicalFacility $facility)
    {
        $facility->load(['user', 'documents']);
        
        // Group documents by type
        $documents = collect([
            'license' => null,
            'tax' => null,
            'registration' => null,
            'additional' => []
        ]);

        foreach ($facility->documents as $document) {
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

        return view('medical_facilities.edit', compact('facility', 'documents'));
    }

    /**
     * Update the specified medical facility in storage.
     */
    public function update(Request $request, MedicalFacility $facility)
    {
        $request->validate([
            'facility_name' => 'required|string|max:255',
            'facility_type' => 'required|string|max:100',
            'license_number' => 'required|string|max:50|unique:medical_facilities,license_number,' . $facility->id,
            'tax_id' => 'nullable|string|max:50',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email,' . $facility->user_id,
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'bed_capacity' => 'nullable|integer|min:0',
            'document_numbers.license' => 'nullable|string|max:50',
            'document_numbers.tax' => 'nullable|string|max:50',
            'document_numbers.registration' => 'nullable|string|max:50',
            'documents.license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.tax' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'documents.registration' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'facility_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();

        try {
            // Update user
            $facility->user->update([
                'name' => $request->facility_name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            // Handle facility photo
            if ($request->hasFile('facility_photo')) {
                // Delete old photo if exists
                if ($facility->facility_photo) {
                    Storage::disk('public')->delete($facility->facility_photo);
                }
                
                $facilityPhotoPath = $request->file('facility_photo')->store('facility-photos', 'public');
                $facility->facility_photo = $facilityPhotoPath;
            }

            // Update facility
            $facility->update([
                'facility_name' => $request->facility_name,
                'facility_type' => $request->facility_type,
                'license_number' => $request->license_number,
                'tax_id' => $request->tax_id,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'phone' => $request->phone,
                'email' => $request->email,
                'website' => $request->website,
                'description' => $request->description,
                'bed_capacity' => $request->bed_capacity,
            ]);

            // Handle required documents
            $requiredDocuments = [
                'license' => 'Facility License',
                'tax' => 'Tax Certificate',
                'registration' => 'Registration Certificate'
            ];

            foreach ($requiredDocuments as $key => $title) {
                if ($request->hasFile("documents.{$key}")) {
                    // Delete old document file if it exists
                    $oldDoc = $facility->documents()
                        ->where('document_type', $title)
                        ->first();
                    
                    if ($oldDoc) {
                        Storage::disk('public')->delete($oldDoc->file_path);
                        $oldDoc->delete();
                    }

                    // Upload and create new document
                    $file = $request->file("documents.{$key}");
                    $path = $file->store('facility-documents', 'public');
                    
                    FacilityDocument::create([
                        'medical_facility_id' => $facility->id,
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
                    $existingDoc = $facility->documents()
                        ->where('document_type', $title)
                        ->first();
                    
                    if ($existingDoc) {
                        $existingDoc->update([
                            'document_number' => $request->input("document_numbers.{$key}")
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('medical_facilities.show', $facility)
                ->with('success', 'Medical facility updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error updating medical facility: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the specified medical facility.
     */
    public function updateStatus(Request $request, MedicalFacility $facility)
    {
        $request->validate([
            'status' => 'required|in:pending,verified,approved,rejected,suspended',
            'status_reason' => 'required_if:status,rejected,suspended|nullable|string|max:255',
        ]);

        try {
            $facility->updateStatus($request->status, $request->status_reason);

            return redirect()
                ->route('medical_facilities.show', $facility)
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
    public function previewDocument(MedicalFacility $facility, FacilityDocument $document)
    {
        if ($document->medical_facility_id !== $facility->id) {
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
     * Remove the specified medical facility from storage.
     */
    public function destroy(MedicalFacility $facility)
    {
        try {
            // Delete associated documents from storage
            foreach ($facility->documents as $document) {
                Storage::delete($document->file_path);
            }

            $facility->delete(); // This will soft delete the facility
            $facility->user->delete(); // This will delete the associated user

            return redirect()->route('medical_facilities.index')
                ->with('success', 'Medical facility deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting medical facility: ' . $e->getMessage());
        }
    }
}
