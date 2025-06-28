<?php

namespace App\Http\Controllers\Api\Worker;

use App\Http\Controllers\Controller;
use App\Models\LocumShift;
use App\Models\ShiftApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LocumShiftController extends Controller
{
    public function availableShifts()
    {
        Log::info('--- availableShifts API endpoint hit ---');
        $worker = Auth::guard('medical-worker')->user();

        if (!$worker) {
            Log::error('Medical worker not authenticated for available shifts.');
            return response()->json(['data' => [], 'message' => 'Unauthenticated'], 401);
        }

        Log::info('Authenticated worker for available shifts:', ['id' => $worker->id, 'specialty_id' => $worker->specialty_id]);

        $query = LocumShift::where('status', 'open')
            ->where('start_datetime', '>=', now());

        Log::info('Open shifts count before specialty filter:', ['count' => $query->count()]);

        $finalShifts = $query->where('medical_specialty_id', $worker->specialty_id)
            ->latest('start_datetime')
            ->get();

        Log::info('Final shifts count after specialty filter:', ['count' => $finalShifts->count()]);
        Log::info('--- availableShifts API endpoint finished ---');

        return response()->json($finalShifts);
    }

    public function apply(Request $request, LocumShift $locumShift)
    {
        $worker = Auth::guard('medical-worker')->user();

        if ($locumShift->status !== 'open') {
            return response()->json(['message' => 'This shift is no longer open for applications.'], 422);
        }

        $existingApplication = ShiftApplication::where('shift_id', $locumShift->id)
            ->where('medical_worker_id', $worker->id)
            ->exists();

        if ($existingApplication) {
            return response()->json(['message' => 'You have already applied for this shift.'], 422);
        }

        $application = ShiftApplication::create([
            'shift_id' => $locumShift->id,
            'medical_worker_id' => $worker->id,
            'status' => 'waiting',
            'applied_at' => now(),
        ]);

        return response()->json(['message' => 'Application submitted successfully.', 'application' => $application], 201);
    }

    public function myApplications()
    {
        $worker = Auth::guard('medical-worker')->user();

        $applications = ShiftApplication::where('medical_worker_id', $worker->id)
            ->with('shift')
            ->latest()
            ->get();

        return response()->json($applications);
    }

    public function index(Request $request)
    {
        // Expire due shifts first
        LocumShift::expireDueShifts();
        $worker = Auth::guard('medical-worker')->user();
        if (!$worker) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $shifts = LocumShift::where('status', 'open')
            ->where('start_datetime', '>=', now())
            ->where('medical_specialty_id', $worker->specialty_id)
            ->latest('start_datetime')
            ->get();

        return response()->json($shifts);
    }

    public function accept(Request $request, $id)
    {
        $worker = Auth::guard('medical-worker')->user();
        if (!$worker) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $shift = LocumShift::findOrFail($id);

        // Ensure shift hasn't started yet
        if ($shift->start_datetime <= now()) {
            $shift->update(['status' => 'expired']);
            return response()->json(['error' => 'Shift has already started/expired.'], 400);
        }

        // Validate shift status and worker specialty
        if ($shift->status !== 'open') {
            return response()->json(['error' => 'Shift is not available for acceptance.'], 400);
        }

        if ($shift->medical_specialty_id !== $worker->specialty_id) {
            return response()->json(['error' => 'Worker specialty does not match shift requirements.'], 400);
        }

        // Check if worker already has a confirmed shift at the same time
        $overlappingShift = LocumShift::where('medical_worker_id', $worker->id)
            ->where('status', 'confirmed')
            ->where(function ($query) use ($shift) {
                $query->where(function ($q) use ($shift) {
                    $q->where('start_datetime', '<=', $shift->start_datetime)
                       ->where('end_datetime', '>', $shift->start_datetime);
                })
                ->orWhere(function ($q) use ($shift) {
                    $q->where('start_datetime', '<', $shift->end_datetime)
                       ->where('end_datetime', '>=', $shift->end_datetime);
                });
            })
            ->first();

        if ($overlappingShift) {
            return response()->json([
                'error' => 'You already have a confirmed shift during this time.'
            ], 400);
        }

        // Accept the shift
        $shift->update([
            'medical_worker_id' => $worker->id,
            'status' => 'confirmed'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift accepted successfully',
            'shift' => $shift
        ]);
    }

    public function start(Request $request, $id)
    {
        $worker = Auth::guard('medical-worker')->user();
        if (!$worker) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $shift = LocumShift::findOrFail($id);

        // attach worker if not yet linked
        if (!$shift->medical_worker_id) {
            $shift->medical_worker_id = $worker->id;
        }

        // Use application timezone for comparison
        $appTz = config('app.timezone', 'UTC');
        $shiftStart = $shift->start_datetime->clone()->setTimezone($appTz);
        if (now($appTz)->lt($shiftStart->subMinutes(5))) {
            return response()->json([
                'error' => 'Shift start time has not yet arrived. You may start within 5 minutes before the scheduled start time.',
                'start_time' => $shiftStart->format('Y-m-d H:i:s'),
                'current_time' => now($appTz)->format('Y-m-d H:i:s')
            ], 400);
        }

        // Start the shift
        $shift->status = 'in_progress';
        $shift->medical_worker_id = $worker->id;
        $shift->actual_start_time = now();
        $shift->save();

        // Update the corresponding application row so the dashboard reflects the new status
        \App\Models\ShiftApplication::where('shift_id', $shift->id)
            ->where('medical_worker_id', $worker->id)
            ->update([
                'status' => 'in_progress',
            ]);


        return response()->json([
            'success' => true,
            'message' => 'Shift started successfully',
            'shift' => $shift
        ]);
    }
}
