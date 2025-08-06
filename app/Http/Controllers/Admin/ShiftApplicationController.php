<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShiftApplication;
use App\Models\LocumShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShiftApplicationController extends Controller
{
    /**
     * Display a listing of shift applications
     */
    public function index()
    {
        $applications = ShiftApplication::with(['shift.facility', 'medicalWorker'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.shift-applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource
     */
    public function show(ShiftApplication $shiftApplication)
    {
        $shiftApplication->load(['shift.facility', 'medicalWorker']);
        return view('admin.shift-applications.show', compact('shiftApplication'));
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(ShiftApplication $shiftApplication)
    {
        //
    }

    /**
     * Update the specified resource in storage
     */
    public function update(Request $request, ShiftApplication $shiftApplication)
    {
        //
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(ShiftApplication $shiftApplication)
    {
        //
    }

    /**
     * Approve a shift application
     */
    public function approve(Request $request, ShiftApplication $shiftApplication)
    {
        try {
            if ($shiftApplication->status !== 'waiting') {
                return redirect()->back()->with('error', 'Application is not in waiting status');
            }

            // Check if shift is still available
            $shift = $shiftApplication->shift;
            if ($shift->status !== 'open') {
                return redirect()->back()->with('error', 'Shift is no longer available');
            }

            // Approve this application
            $shiftApplication->update([
                'status' => 'approved',
                'selected_at' => now()
            ]);

            // Reject all other applications for this shift
            ShiftApplication::where('shift_id', $shiftApplication->shift_id)
                ->where('id', '!=', $shiftApplication->id)
                ->where('status', 'waiting')
                ->update([
                    'status' => 'rejected',
                    'selected_at' => now()
                ]);

            // Update shift status to assigned
            $shift->update([
                'status' => 'assigned',
                'accepted_worker_id' => $shiftApplication->medical_worker_id
            ]);

            Log::info('Shift application approved', [
                'application_id' => $shiftApplication->id,
                'shift_id' => $shift->id,
                'worker_id' => $shiftApplication->medical_worker_id
            ]);

            return redirect()->back()->with('success', 'Application approved successfully');

        } catch (\Exception $e) {
            Log::error('Error approving shift application', [
                'error' => $e->getMessage(),
                'application_id' => $shiftApplication->id
            ]);

            return redirect()->back()->with('error', 'Failed to approve application');
        }
    }

    /**
     * Reject a shift application
     */
    public function reject(Request $request, ShiftApplication $shiftApplication)
    {
        try {
            if ($shiftApplication->status !== 'waiting') {
                return redirect()->back()->with('error', 'Application is not in waiting status');
            }

            $shiftApplication->update([
                'status' => 'rejected',
                'selected_at' => now()
            ]);

            Log::info('Shift application rejected', [
                'application_id' => $shiftApplication->id,
                'shift_id' => $shiftApplication->shift_id,
                'worker_id' => $shiftApplication->medical_worker_id
            ]);

            return redirect()->back()->with('success', 'Application rejected successfully');

        } catch (\Exception $e) {
            Log::error('Error rejecting shift application', [
                'error' => $e->getMessage(),
                'application_id' => $shiftApplication->id
            ]);

            return redirect()->back()->with('error', 'Failed to reject application');
        }
    }
}
