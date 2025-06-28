<?php

namespace App\Http\Controllers\Api\Facility;

use App\Http\Controllers\Controller;
use App\Models\LocumShift;
use App\Models\MedicalWorker;
use App\Models\ShiftApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ShiftApplicationController extends Controller
{
    public function index(LocumShift $locumShift)
    {
        Gate::authorize('manage-shift', $locumShift);

        $applicants = $locumShift->applications()->with('medicalWorker.user')->get();

        return response()->json($applicants);
    }

    public function accept(Request $request, LocumShift $locumShift, MedicalWorker $medicalWorker)
    {
        Gate::authorize('manage-shift', $locumShift);

        $application = ShiftApplication::where('shift_id', $locumShift->id)
            ->where('medical_worker_id', $medicalWorker->id)
            ->where('status', 'waiting')
            ->firstOrFail();

        if ($locumShift->slots_available <= 0) {
            return response()->json(['message' => 'Shift is already filled.'], 422);
        }

        DB::transaction(function () use ($locumShift, $application) {
            // link worker to shift and mark confirmed
            $locumShift->update([
                'medical_worker_id' => $application->medical_worker_id,
                'status' => 'confirmed'
            ]);
            // update status and selected_at
            $application->update(['status' => 'approved', 'selected_at' => now()]);
            // notify the medical worker
            $application->medicalWorker->notify(new \App\Notifications\ShiftApproved($locumShift));

            $application->update(['status' => 'approved', 'selected_at' => now()]);
            $locumShift->decrement('slots_available');

            if ($locumShift->fresh()->slots_available == 0) {
                $locumShift->update(['status' => 'filled']);
                // Optionally reject other pending applications
                $locumShift->applications()->where('status', 'waiting')->update(['status' => 'rejected']);
            }
        });

        return response()->json(['message' => 'Application accepted successfully.']);
    }
}
