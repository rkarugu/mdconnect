<?php

namespace App\Http\Controllers\Api\Facility;

use App\Http\Controllers\Controller;
use App\Models\LocumShift;
use App\Models\MedicalWorker;
use App\Models\MedicalSpecialty;
use App\Notifications\NewShiftAvailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class LocumShiftController extends Controller
{
    public function index()
    {
        $facility = Auth::user()->facility;
        $shifts = LocumShift::where('facility_id', $facility->id)->latest()->get();
        return response()->json($shifts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'shift_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string',
            'worker_type' => 'required|in:Nurse,Doctor,Phlebotomist',
            'slots_available' => 'required|integer|min:1',
            'pay_rate' => 'required|numeric|min:0',
            'auto_match' => 'sometimes|boolean',
            'instant_book' => 'sometimes|boolean',
        ]);

        $facility = Auth::user()->facility;

        $shift = $facility->locumShifts()->create([
            'created_by' => Auth::id(),
        ] + $request->all());

        // Load the facility relationship for the notification
        $shift->load('facility');

        // Notify medical workers who match the required specialty
        $this->notifyEligibleMedicalWorkers($shift);

        return response()->json($shift, 201);
    }

    public function show(LocumShift $locumShift)
    {
        Gate::authorize('manage-shift', $locumShift);
        return response()->json($locumShift->load('applications.medicalWorker'));
    }

    public function update(Request $request, LocumShift $locumShift)
    {
        Gate::authorize('manage-shift', $locumShift);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'shift_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'location' => 'sometimes|string',
            'worker_type' => 'sometimes|in:Nurse,Doctor,Phlebotomist',
            'slots_available' => 'sometimes|integer|min:1',
            'pay_rate' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:open,filled,canceled,closed',
            'auto_match' => 'sometimes|boolean',
            'instant_book' => 'sometimes|boolean',
        ]);

        // Check if status is being updated to 'filled'
        if ($request->has('status') && $request->status === 'filled' && $locumShift->status !== 'filled') {
            $request->merge([
                'ended_at' => now(),
                'ended_by' => auth()->id()
            ]);
        }

        $locumShift->update($request->all());

        return response()->json($locumShift);
    }

    public function destroy(LocumShift $locumShift)
    {
        Gate::authorize('manage-shift', $locumShift);

        $locumShift->delete();

        return response()->json(['message' => 'Shift canceled successfully.'], 200);
    }

    /**
     * Notify medical workers who match the shift requirements
     */
    private function notifyEligibleMedicalWorkers(LocumShift $shift)
    {
        try {
            $workerType = strtolower($shift->worker_type);
            \Log::info('Starting notification process for shift', [
                'shift_id' => $shift->id,
                'worker_type_original' => $shift->worker_type,
                'worker_type_processed' => $workerType,
            ]);

            // Use a case-insensitive query to find the specialty
            $specialty = \App\Models\MedicalSpecialty::whereRaw('LOWER(name) = ?', [$workerType])->first();
            
            if (!$specialty) {
                \Log::warning('Specialty not found for shift notification', [
                    'shift_id' => $shift->id,
                    'worker_type' => $shift->worker_type
                ]);
                return;
            }
            \Log::info('Found matching specialty', ['specialty_id' => $specialty->id, 'specialty_name' => $specialty->name]);

            // Start building the query to get eligible workers
            $workersQuery = \App\Models\MedicalWorker::where('medical_specialty_id', $specialty->id);
            $countWithSpecialty = $workersQuery->count();
            \Log::info('Worker filtering step 1: Found with specialty', ['count' => $countWithSpecialty]);

            $workersQuery->where('status', 'approved');
            $countWithStatus = $workersQuery->count();
            \Log::info('Worker filtering step 2: Found with approved status', ['count' => $countWithStatus]);

            $workersQuery->where('is_available', true);
            $countWithAvailability = $workersQuery->count();
            \Log::info('Worker filtering step 3: Found with availability', ['count' => $countWithAvailability]);

            $eligibleWorkers = $workersQuery->get();

            if ($eligibleWorkers->isEmpty()) {
                \Log::info('No eligible workers found for shift notification after filtering', [
                    'shift_id' => $shift->id,
                    'specialty_id' => $specialty->id
                ]);
                return;
            }

            // Send notification to all eligible workers
            \Illuminate\Support\Facades\Notification::send($eligibleWorkers, new \App\Notifications\NewShiftAvailable($shift));

            \Log::info('Shift notifications sent successfully', [
                'shift_id' => $shift->id,
                'workers_notified' => $eligibleWorkers->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send shift notifications', [
                'shift_id' => $shift->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
