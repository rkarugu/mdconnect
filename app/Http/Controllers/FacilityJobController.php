<?php

namespace App\Http\Controllers;

use App\Models\LocumJobRequest;
use App\Models\MedicalFacility;
use App\Models\MedicalSpecialty;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FacilityJobController extends Controller
{
    /**
     * Display a listing of the facility's job postings
     */
    public function index(Request $request)
    {
        // Get the current user's facility
        $facility = $this->getUserFacility();
        
        if (!$facility) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a medical facility profile.');
        }
        
        // Query jobs for this facility with filtering
        $query = LocumJobRequest::with(['specialty', 'applications'])
            ->where('medical_facility_id', $facility->id);
            
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by specialty
        if ($request->has('specialty_id') && $request->specialty_id) {
            $query->where('specialty_id', $request->specialty_id);
        }
        
        // Search by title or description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        // Get jobs with pagination
        $jobs = $query->orderByDesc('created_at')->paginate(10);
        
        // Get specialties for filter dropdown
        $specialties = MedicalSpecialty::orderBy('name')->get();
        
        return view('facility_jobs.index', compact('jobs', 'specialties', 'facility'));
    }
    
    /**
     * Show the form for creating a new job posting
     */
    public function create()
    {
        $facility = $this->getUserFacility();
        
        if (!$facility) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a medical facility profile.');
        }
        
        // Check if facility is approved
        if ($facility->status !== 'approved') {
            return redirect()->route('facility_jobs.index')
                ->with('error', 'Your facility must be approved before posting jobs.');
        }
        
        $specialties = MedicalSpecialty::orderBy('name')->get();
        
        return view('facility_jobs.create', compact('facility', 'specialties'));
    }
    
    /**
     * Store a newly created job posting
     */
    public function store(Request $request)
    {
        $facility = $this->getUserFacility();
        
        if (!$facility) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a medical facility profile.');
        }
        
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'specialty_id' => 'required|exists:medical_specialties,id',
            'description' => 'required|string',
            'required_experience_years' => 'required|integer|min:0',
            'required_qualifications' => 'nullable|array',
            'responsibilities' => 'required|string',
            'is_recurring' => 'boolean',
            'shift_type' => 'required|in:day,night,evening,custom',
            'shift_start' => 'required_if:is_recurring,0|nullable|date',
            'shift_end' => 'required_if:is_recurring,0|nullable|date|after:shift_start',
            'recurring_days' => 'required_if:is_recurring,1|nullable|array',
            'recurring_duration_days' => 'required_if:is_recurring,1|nullable|integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'benefits' => 'nullable|array',
            'is_remote' => 'boolean',
            'location' => 'nullable|string|max:255',
            'slots_available' => 'required|integer|min:1',
            'auto_match_enabled' => 'boolean',
            'instant_book_enabled' => 'boolean',
            'deadline' => 'nullable|date|after:today',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Prepare data for storage
            $jobData = [
                'medical_facility_id' => $facility->id,
                'specialty_id' => $request->specialty_id,
                'title' => $request->title,
                'description' => $request->description,
                'required_experience_years' => $request->required_experience_years,
                'required_qualifications' => $request->required_qualifications,
                'responsibilities' => $request->responsibilities,
                'is_recurring' => $request->has('is_recurring'),
                'shift_type' => $request->shift_type,
                'hourly_rate' => $request->hourly_rate,
                'benefits' => $request->benefits,
                'is_remote' => $request->has('is_remote'),
                'location' => $request->location,
                'status' => 'open',
                'slots_available' => $request->slots_available,
                'auto_match_enabled' => $request->has('auto_match_enabled'),
                'instant_book_enabled' => $request->has('instant_book_enabled'),
                'posted_at' => now(),
                'deadline' => $request->deadline,
            ];
            
            // Handle shift information based on whether it's recurring or not
            if ($request->has('is_recurring')) {
                // Process recurring pattern
                $recurringPattern = [
                    'days' => $request->recurring_days,
                    'times' => [
                        'start' => $request->recurring_start_time,
                        'end' => $request->recurring_end_time,
                    ],
                ];
                
                $jobData['recurring_pattern'] = $recurringPattern;
                $jobData['recurring_duration_days'] = $request->recurring_duration_days;
            } else {
                // Single shift
                $jobData['shift_start'] = $request->shift_start;
                $jobData['shift_end'] = $request->shift_end;
            }
            
            // Create job posting
            $job = LocumJobRequest::create($jobData);
            
            DB::commit();
            
            return redirect()->route('facility_jobs.show', $job)
                ->with('success', 'Job posting created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error creating job posting: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified job posting
     */
    public function show(LocumJobRequest $job)
    {
        $facility = $this->getUserFacility();
        
        if (!$facility) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a medical facility profile.');
        }
        
        // Check if the job belongs to this facility
        if ($job->medical_facility_id !== $facility->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load relationships
        $job->load(['specialty', 'applications.medicalWorker.user', 'contracts']);
        
        return view('facility_jobs.show', compact('job', 'facility'));
    }
    
    /**
     * Show the form for editing the specified job posting
     */
    public function edit(LocumJobRequest $job)
    {
        $facility = $this->getUserFacility();
        
        if (!$facility) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a medical facility profile.');
        }
        
        // Check if the job belongs to this facility
        if ($job->medical_facility_id !== $facility->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Can only edit if job is still open
        if ($job->status !== 'open') {
            return redirect()->route('facility_jobs.show', $job)
                ->with('error', 'Only open job postings can be edited.');
        }
        
        $specialties = MedicalSpecialty::orderBy('name')->get();
        
        return view('facility_jobs.edit', compact('job', 'facility', 'specialties'));
    }
    
    /**
     * Update the specified job posting
     */
    public function update(Request $request, LocumJobRequest $job)
    {
        $facility = $this->getUserFacility();
        
        if (!$facility) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a medical facility profile.');
        }
        
        // Check if the job belongs to this facility
        if ($job->medical_facility_id !== $facility->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Can only update if job is still open
        if ($job->status !== 'open') {
            return redirect()->route('facility_jobs.show', $job)
                ->with('error', 'Only open job postings can be updated.');
        }
        
        // Validate the request (same validation as store)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'specialty_id' => 'required|exists:medical_specialties,id',
            'description' => 'required|string',
            'required_experience_years' => 'required|integer|min:0',
            'required_qualifications' => 'nullable|array',
            'responsibilities' => 'required|string',
            'is_recurring' => 'boolean',
            'shift_type' => 'required|in:day,night,evening,custom',
            'shift_start' => 'required_if:is_recurring,0|nullable|date',
            'shift_end' => 'required_if:is_recurring,0|nullable|date|after:shift_start',
            'recurring_days' => 'required_if:is_recurring,1|nullable|array',
            'recurring_duration_days' => 'required_if:is_recurring,1|nullable|integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'benefits' => 'nullable|array',
            'is_remote' => 'boolean',
            'location' => 'nullable|string|max:255',
            'slots_available' => 'required|integer|min:1',
            'auto_match_enabled' => 'boolean',
            'instant_book_enabled' => 'boolean',
            'deadline' => 'nullable|date|after:today',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Update job posting fields
            $job->title = $request->title;
            $job->specialty_id = $request->specialty_id;
            $job->description = $request->description;
            $job->required_experience_years = $request->required_experience_years;
            $job->required_qualifications = $request->required_qualifications;
            $job->responsibilities = $request->responsibilities;
            $job->is_recurring = $request->has('is_recurring');
            $job->shift_type = $request->shift_type;
            $job->hourly_rate = $request->hourly_rate;
            $job->benefits = $request->benefits;
            $job->is_remote = $request->has('is_remote');
            $job->location = $request->location;
            $job->slots_available = $request->slots_available;
            $job->auto_match_enabled = $request->has('auto_match_enabled');
            $job->instant_book_enabled = $request->has('instant_book_enabled');
            $job->deadline = $request->deadline;
            
            // Handle shift information based on whether it's recurring or not
            if ($request->has('is_recurring')) {
                // Process recurring pattern
                $recurringPattern = [
                    'days' => $request->recurring_days,
                    'times' => [
                        'start' => $request->recurring_start_time,
                        'end' => $request->recurring_end_time,
                    ],
                ];
                
                $job->recurring_pattern = $recurringPattern;
                $job->recurring_duration_days = $request->recurring_duration_days;
                $job->shift_start = null;
                $job->shift_end = null;
            } else {
                // Single shift
                $job->shift_start = $request->shift_start;
                $job->shift_end = $request->shift_end;
                $job->recurring_pattern = null;
                $job->recurring_duration_days = null;
            }
            
            $job->save();
            
            DB::commit();
            
            return redirect()->route('facility_jobs.show', $job)
                ->with('success', 'Job posting updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating job posting: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the status of a job posting
     */
    public function updateStatus(Request $request, LocumJobRequest $job)
    {
        $facility = $this->getUserFacility();
        
        if (!$facility) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a medical facility profile.');
        }
        
        // Check if the job belongs to this facility
        if ($job->medical_facility_id !== $facility->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Validate the request
        $request->validate([
            'status' => 'required|in:open,cancelled',
            'reason' => 'required_if:status,cancelled|nullable|string|max:255',
        ]);
        
        try {
            // Update the status
            if ($request->status === 'open') {
                // Re-open the job
                if ($job->status === 'cancelled') {
                    $job->status = 'open';
                    $job->cancelled_at = null;
                    $job->save();
                    
                    return redirect()->route('facility_jobs.show', $job)
                        ->with('success', 'Job posting re-opened successfully.');
                }
            } else if ($request->status === 'cancelled') {
                // Cancel the job
                $job->status = 'cancelled';
                $job->cancelled_at = now();
                $job->save();
                
                return redirect()->route('facility_jobs.show', $job)
                    ->with('success', 'Job posting cancelled successfully.');
            }
            
            return back()->with('error', 'Invalid status update.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating job status: ' . $e->getMessage());
        }
    }
    
    /**
     * Process a job application
     */
    public function processApplication(Request $request, LocumJobRequest $job, JobApplication $application)
    {
        $facility = $this->getUserFacility();
        
        if (!$facility) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have a medical facility profile.');
        }
        
        // Check if the job belongs to this facility
        if ($job->medical_facility_id !== $facility->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if the application belongs to this job
        if ($application->locum_job_request_id !== $job->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Validate the request
        $request->validate([
            'action' => 'required|in:accept,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:255',
        ]);
        
        try {
            DB::beginTransaction();
            
            if ($request->action === 'accept') {
                // Accept the application
                $application->status = 'accepted';
                $application->accepted_at = now();
                $application->responded_at = now();
                $application->save();
                
                // Check if all slots are filled
                $acceptedApps = $job->applications()->where('status', 'accepted')->count();
                if ($acceptedApps >= $job->slots_available) {
                    $job->status = 'filled';
                    $job->filled_at = now();
                    $job->save();
                }
                
                DB::commit();
                
                return redirect()->route('facility_jobs.show', $job)
                    ->with('success', 'Application accepted successfully.');
                    
            } else if ($request->action === 'reject') {
                // Reject the application
                $application->status = 'rejected';
                $application->rejection_reason = $request->rejection_reason;
                $application->rejected_at = now();
                $application->responded_at = now();
                $application->save();
                
                DB::commit();
                
                return redirect()->route('facility_jobs.show', $job)
                    ->with('success', 'Application rejected successfully.');
            }
            
            DB::rollBack();
            return back()->with('error', 'Invalid action.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing application: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the current user's medical facility
     */
    private function getUserFacility()
    {
        $user = Auth::user();
        return MedicalFacility::where('user_id', $user->id)->first();
    }
}
