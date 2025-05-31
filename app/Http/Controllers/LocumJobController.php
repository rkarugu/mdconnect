<?php

namespace App\Http\Controllers;

use App\Models\LocumJob;
use App\Models\JobApplication;
use App\Models\MedicalFacility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocumJobController extends Controller
{
    /**
     * Display a listing of locum jobs.
     */
    public function index(Request $request)
    {
        $query = LocumJob::query()->with('facility');
        
        // Filter by facility if provided
        if ($request->has('facility') && $request->facility) {
            $query->where('medical_facility_id', $request->facility);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by specialization
        if ($request->has('specialization') && $request->specialization) {
            $query->where('specialization', 'like', '%' . $request->specialization . '%');
        }
        
        // Filter by location
        if ($request->has('location') && $request->location) {
            $query->whereHas('facility', function($q) use ($request) {
                $q->where('city', 'like', '%' . $request->location . '%')
                  ->orWhere('state', 'like', '%' . $request->location . '%')
                  ->orWhere('country', 'like', '%' . $request->location . '%');
            });
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('requirements', 'like', '%' . $search . '%')
                  ->orWhereHas('facility', function($fq) use ($search) {
                      $fq->where('facility_name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Sort by options
        $sortField = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);
        
        $jobs = $query->paginate(10);
        
        return view('locum_jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new locum job.
     */
    public function create()
    {
        // Check if user has an approved facility
        $user = Auth::user();
        $facility = MedicalFacility::where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();
        
        if (!$facility) {
            return redirect()->route('medical_facilities.index')
                ->with('error', 'You need an approved medical facility to post jobs. Please register or verify your facility first.');
        }
        
        return view('locum_jobs.create', compact('facility'));
    }

    /**
     * Store a newly created locum job in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'medical_facility_id' => 'required|exists:medical_facilities,id',
            'title' => 'required|string|max:255',
            'specialization' => 'required|string|max:100',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'shift_start_date' => 'required|date',
            'shift_end_date' => 'required|date|after_or_equal:shift_start_date',
            'shift_start_time' => 'required',
            'shift_end_time' => 'required',
            'rate_amount' => 'required|numeric|min:0',
            'rate_type' => 'required|in:hourly,daily,weekly,monthly,fixed',
            'application_deadline' => 'required|date|after_or_equal:today',
        ]);
        
        // Verify the facility belongs to the authenticated user
        $facility = MedicalFacility::findOrFail($request->medical_facility_id);
        if ($facility->user_id != Auth::id()) {
            return back()->with('error', 'You can only create jobs for your own facilities');
        }
        
        $job = new LocumJob();
        $job->medical_facility_id = $request->medical_facility_id;
        $job->title = $request->title;
        $job->specialization = $request->specialization;
        $job->description = $request->description;
        $job->requirements = $request->requirements;
        $job->shift_start_date = $request->shift_start_date;
        $job->shift_end_date = $request->shift_end_date;
        $job->shift_start_time = $request->shift_start_time;
        $job->shift_end_time = $request->shift_end_time;
        $job->rate_amount = $request->rate_amount;
        $job->rate_type = $request->rate_type;
        $job->application_deadline = $request->application_deadline;
        $job->status = 'open';
        $job->save();
        
        return redirect()->route('locum_jobs.show', $job)
            ->with('success', 'Job posted successfully');
    }

    /**
     * Display the specified locum job.
     */
    public function show(LocumJob $job)
    {
        $job->load('facility', 'applications');
        
        // Check if current user has applied
        $hasApplied = false;
        $application = null;
        
        if (Auth::check()) {
            $application = JobApplication::where('locum_job_id', $job->id)
                ->where('user_id', Auth::id())
                ->first();
            
            $hasApplied = !is_null($application);
        }
        
        return view('locum_jobs.show', compact('job', 'hasApplied', 'application'));
    }

    /**
     * Show the form for editing the specified locum job.
     */
    public function edit(LocumJob $job)
    {
        // Check if the job belongs to the authenticated user's facility
        if ($job->facility->user_id != Auth::id()) {
            return redirect()->route('locum_jobs.index')
                ->with('error', 'You can only edit your own job listings');
        }
        
        return view('locum_jobs.edit', compact('job'));
    }

    /**
     * Update the specified locum job in storage.
     */
    public function update(Request $request, LocumJob $job)
    {
        // Check if the job belongs to the authenticated user's facility
        if ($job->facility->user_id != Auth::id()) {
            return redirect()->route('locum_jobs.index')
                ->with('error', 'You can only update your own job listings');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'specialization' => 'required|string|max:100',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'shift_start_date' => 'required|date',
            'shift_end_date' => 'required|date|after_or_equal:shift_start_date',
            'shift_start_time' => 'required',
            'shift_end_time' => 'required',
            'rate_amount' => 'required|numeric|min:0',
            'rate_type' => 'required|in:hourly,daily,weekly,monthly,fixed',
            'application_deadline' => 'required|date',
            'status' => 'required|in:open,closed,filled,cancelled',
        ]);
        
        $job->title = $request->title;
        $job->specialization = $request->specialization;
        $job->description = $request->description;
        $job->requirements = $request->requirements;
        $job->shift_start_date = $request->shift_start_date;
        $job->shift_end_date = $request->shift_end_date;
        $job->shift_start_time = $request->shift_start_time;
        $job->shift_end_time = $request->shift_end_time;
        $job->rate_amount = $request->rate_amount;
        $job->rate_type = $request->rate_type;
        $job->application_deadline = $request->application_deadline;
        $job->status = $request->status;
        $job->save();
        
        return redirect()->route('locum_jobs.show', $job)
            ->with('success', 'Job updated successfully');
    }

    /**
     * Remove the specified locum job from storage.
     */
    public function destroy(LocumJob $job)
    {
        // Check if the job belongs to the authenticated user's facility
        if ($job->facility->user_id != Auth::id()) {
            return redirect()->route('locum_jobs.index')
                ->with('error', 'You can only delete your own job listings');
        }
        
        // Check if job has applications
        if ($job->applications->count() > 0) {
            return redirect()->route('locum_jobs.show', $job)
                ->with('error', 'Cannot delete job with existing applications');
        }
        
        $job->delete();
        
        return redirect()->route('locum_jobs.index')
            ->with('success', 'Job deleted successfully');
    }
    
    /**
     * Apply for a job.
     */
    public function apply(Request $request, LocumJob $job)
    {
        // Check if job is open
        if ($job->status !== 'open') {
            return back()->with('error', 'This job is no longer accepting applications');
        }
        
        // Check if application deadline has passed
        if (now() > $job->application_deadline) {
            return back()->with('error', 'The application deadline for this job has passed');
        }
        
        // Check if user has already applied
        $existingApplication = JobApplication::where('locum_job_id', $job->id)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($existingApplication) {
            return back()->with('error', 'You have already applied for this job');
        }
        
        $request->validate([
            'cover_letter' => 'required|string',
            'availability' => 'required|string',
            'expected_rate' => 'nullable|numeric|min:0',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx|max:10240',
        ]);
        
        // Create application
        $application = new JobApplication();
        $application->locum_job_id = $job->id;
        $application->user_id = Auth::id();
        $application->cover_letter = $request->cover_letter;
        $application->availability = $request->availability;
        $application->expected_rate = $request->expected_rate;
        $application->status = 'pending';
        $application->save();
        
        // Handle document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('job_applications/' . $application->id, 'public');
                
                $document = new ApplicationDocument();
                $document->job_application_id = $application->id;
                $document->file_path = $path;
                $document->file_name = $file->getClientOriginalName();
                $document->file_type = $file->getClientMimeType();
                $document->save();
            }
        }
        
        // Notify facility owner
        // Notification logic here
        
        return redirect()->route('locum_jobs.show', $job)
            ->with('success', 'Your application has been submitted successfully');
    }
    
    /**
     * Display applications for a job.
     */
    public function applications(LocumJob $job)
    {
        // Check if the job belongs to the authenticated user's facility
        if ($job->facility->user_id != Auth::id()) {
            return redirect()->route('locum_jobs.index')
                ->with('error', 'You can only view applications for your own job listings');
        }
        
        $job->load('applications.user', 'applications.documents');
        
        return view('locum_jobs.applications', compact('job'));
    }
    
    /**
     * Update an application status.
     */
    public function updateApplication(Request $request, LocumJob $job, JobApplication $application)
    {
        // Check if the job belongs to the authenticated user's facility
        if ($job->facility->user_id != Auth::id()) {
            return redirect()->route('locum_jobs.index')
                ->with('error', 'You can only manage applications for your own job listings');
        }
        
        $request->validate([
            'status' => 'required|in:pending,shortlisted,interviewed,offered,accepted,rejected',
            'feedback' => 'nullable|string',
        ]);
        
        $application->status = $request->status;
        $application->feedback = $request->feedback;
        $application->save();
        
        // If status is accepted, mark job as filled and reject other applications
        if ($request->status === 'accepted') {
            $job->status = 'filled';
            $job->save();
            
            // Reject other applications
            JobApplication::where('locum_job_id', $job->id)
                ->where('id', '!=', $application->id)
                ->update(['status' => 'rejected']);
        }
        
        // Notify applicant
        // Notification logic here
        
        return back()->with('success', 'Application status updated successfully');
    }
}
