<?php

namespace App\Http\Controllers\Web\Facility;

use App\Http\Controllers\Controller;
use App\Models\LocumShift;
use App\Models\ShiftApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $facility = Auth::user()->medicalFacility;

        if (!$facility) {
            // Redirect to a different page or show an error if the user is not associated with a facility
            return redirect()->route('home')->with('error', 'You are not associated with any medical facility.');
        }

        $openShiftsCount = LocumShift::where('facility_id', $facility->id)
            ->where('status', 'open')
            ->count();

        $pendingApplicationsCount = ShiftApplication::whereHas('shift', function ($query) use ($facility) {
            $query->where('facility_id', $facility->id);
        })->where('status', 'pending')->count();

        $filledShiftsCount = LocumShift::where('facility_id', $facility->id)
            ->where('status', 'filled')
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();

        return view('facility.dashboard', compact(
            'facility',
            'openShiftsCount',
            'pendingApplicationsCount',
            'filledShiftsCount'
        ));
    }
}
