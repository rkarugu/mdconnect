<?php

namespace App\Http\Controllers\Web\Facility;

use App\Http\Controllers\Controller;
use App\Models\LocumShift;
use App\Models\MedicalWorker;
use App\Models\ShiftApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ShiftApplicationController extends Controller
{
    public function accept(Request $request, LocumShift $locumShift, MedicalWorker $medicalWorker)
    {
        Gate::authorize('manage-shift', $locumShift);

        $application = ShiftApplication::where('shift_id', $locumShift->id)
            ->where('medical_worker_id', $medicalWorker->id)
            ->where('status', 'waiting')
            ->first();

        if (!$application) {
            return back()->with('error', 'Application not found or no longer waiting approval.');
        }

        if ($locumShift->slots_available <= 0) {
            return back()->with('error', 'This shift is already filled.');
        }

        try {
            DB::transaction(function () use ($locumShift, $application) {
                $application->update(['status' => 'approved', 'selected_at' => now()]);
                 // link worker to shift
                 $locumShift->update(['medical_worker_id' => $application->medicalWorker->id, 'status' => 'confirmed']);
                 // notify worker
                 $application->medicalWorker->notify(new \App\Notifications\ShiftApproved($locumShift));
                $locumShift->decrement('slots_available');

                if ($locumShift->fresh()->slots_available == 0) {
                    $locumShift->update(['status' => 'filled']);
                    // Reject all other pending applications for this shift
                    $locumShift->applications()->where('status', 'waiting')->update(['status' => 'rejected']);
                }
            });

            return back()->with('success', 'Application accepted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while accepting the application. Please try again.');
        }
    }
}
