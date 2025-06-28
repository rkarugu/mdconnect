<?php

namespace App\Http\Controllers\Web\Facility;

use App\Http\Controllers\Controller;
use App\Models\LocumShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class LocumShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Automatically expire shifts whose end time has passed (real-time)
        \App\Models\LocumShift::expireDueShifts();
        $facility = Auth::user()->medicalFacility;

        if (!$facility) {
            return redirect()->route('facility.dashboard')
                ->with('error', 'Your account is not associated with any facility. Please contact support.');
        }

        $shifts = LocumShift::where('facility_id', $facility->id)
            ->latest()
            ->paginate(10);

        return view('facility.locum-shifts.index', compact('shifts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialties = \App\Models\MedicalSpecialty::where('is_active', true)->orderBy('name')->get();
        return view('facility.locum-shifts.create', compact('specialties'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'worker_type' => 'required|string|exists:medical_specialties,name',
            'slots_available' => 'required|integer|min:1',
            'pay_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $facility = Auth::user()->medicalFacility;

        if (!$facility) {
            return redirect()->route('facility.dashboard')
                ->with('error', 'Your account is not associated with any facility. Cannot create a shift.');
        }

        $shift = new LocumShift($validatedData);
        // Store the exact datetime the user entered (already in app timezone)
        $shift->start_datetime = $validatedData['start_datetime'];
        $shift->end_datetime   = $validatedData['end_datetime'];
        $shift->facility_id = $facility->id;
        $shift->created_by = Auth::id();
        $shift->status = 'open';
        $shift->save();

        return redirect()->route('facility.locum-shifts.index')
            ->with('success', 'Locum shift created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LocumShift $locumShift)
    {
        Gate::authorize('manage-shift', $locumShift);

        $locumShift->load(['applications.medicalWorker.user', 'applications' => function ($query) {
            $query->orderBy('status', 'asc')->orderBy('applied_at', 'desc');
        }]);

        return view('facility.locum-shifts.show', compact('locumShift'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LocumShift $locumShift)
    {
        Gate::authorize('manage-shift', $locumShift);

        return view('facility.locum-shifts.edit', compact('locumShift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LocumShift $locumShift)
    {
        Gate::authorize('manage-shift', $locumShift);

        $validatedData = $request->validate([
            'shift_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'worker_type' => 'required|string|max:255',
            'total_slots' => 'required|integer|min:1',
            'pay_rate' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $current_filled_slots = $locumShift->applications()->where('status', 'accepted')->count();

        if ($validatedData['total_slots'] < $current_filled_slots) {
            return back()->withErrors(['total_slots' => 'Total slots cannot be less than the number of accepted applicants (' . $current_filled_slots . ').'])->withInput();
        }

        $validatedData['slots_available'] = $validatedData['total_slots'] - $current_filled_slots;
        $validatedData['status'] = ($validatedData['slots_available'] > 0) ? 'open' : 'filled';

        $locumShift->update($validatedData);

        return redirect()->route('facility.locum-shifts.index')
            ->with('success', 'Locum shift updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LocumShift $locumShift)
    {
        Gate::authorize('manage-shift', $locumShift);

        // Prevent deletion if there are accepted applications
        if ($locumShift->applications()->where('status', 'accepted')->exists()) {
            return redirect()->route('facility.locum-shifts.show', $locumShift)
                ->with('error', 'Cannot delete a shift with accepted applicants. Please manage applications first.');
        }

        $locumShift->applications()->delete();
        $locumShift->delete();

        return redirect()->route('facility.locum-shifts.index')
            ->with('success', 'Locum shift and all its pending applications have been deleted.');
    }
}
