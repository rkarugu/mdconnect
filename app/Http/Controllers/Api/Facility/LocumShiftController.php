<?php

namespace App\Http\Controllers\Api\Facility;

use App\Http\Controllers\Controller;
use App\Models\LocumShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
}
