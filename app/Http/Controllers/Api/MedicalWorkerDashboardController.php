<?php

namespace App\Http\Controllers\Api;

use App\Models\LocumShift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MedicalWorkerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $worker = Auth::guard('medical-worker')->user();

        if (!$worker) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        
        $dashboardData = [
            // Filter shifts by the worker's specialty name (stored as `worker_type` string)
            'upcoming_shifts' => LocumShift::where(function($q) use ($worker) {
                    // Open shifts not yet started
                    $q->where(function($open) {
                        $open->where('status', 'open')
                             ->where('start_datetime', '>', now());
                    })
                    // Shifts the worker has applied for (waiting / approved)
                      ->orWhereHas('applications', function($q2) use ($worker){
                          $q2->where('medical_worker_id', $worker->id)
                             ->whereIn('status', ['waiting','approved']);
                      })
                    // Shifts already assigned and in progress
                      ->orWhere(function($own) use ($worker){
                          $own->where('medical_worker_id', $worker->id)
                              ->where('status', 'in_progress');
                      });
                })
                ->when($worker->specialty, function ($query) use ($worker) {
                    $query->where('worker_type', $worker->specialty->name);
                })
                ->whereNotNull('start_datetime')
                ->with(['facility', 'applications' => function($q) use ($worker) {
                        $q->where('medical_worker_id', $worker->id);
                 }])
                ->orderBy('start_datetime')
                ->limit(15)
                ->get()
                ->map(function ($shift) use ($worker) {
                    return [
                        'id'          => $shift->id,
                        'title'       => $shift->title,
                        'facility'    => optional($shift->facility)->facility_name ?? optional($shift->facility)->name ?? 'Unknown',
                        'location'    => optional($shift->facility)->address ?? '',
                        'startTime'   => $shift->start_datetime->toDateTimeString(),
                        'endTime'     => $shift->end_datetime->toDateTimeString(),
                        'payRate'     => (float) $shift->pay_rate,
                        'status'      => $shift->status,
                        'durationHrs' => $shift->start_datetime->diffInHours($shift->end_datetime),
                        'expectedPay' => round($shift->pay_rate * $shift->start_datetime->diffInHours($shift->end_datetime), 2),
                        'applicationStatus' => $shift->medical_worker_id === $worker->id ? $shift->status : (optional($shift->applications->first())->status ?? null),
                    ];
                }),
            'instant_requests' => [],
            'bid_invitations' => [],
            'shift_history' => [],
        ];

        return response()->json($dashboardData);
    }

    public function upcomingShifts(Request $request)
    {
        $worker = Auth::guard('medical-worker')->user();
        if (!$worker) { return response()->json(['error' => 'Unauthenticated.'], 401); }

        return response()->json($worker->shifts()
            ->where('status', 'confirmed')
            ->where('start_datetime', '>', now())
            ->orderBy('start_datetime')
            ->get());
    }

    public function instantRequests(Request $request)
    {
        $worker = Auth::guard('medical-worker')->user();
        if (!$worker) { return response()->json(['error' => 'Unauthenticated.'], 401); }

        // The InstantRequest model is not imported, this will fail if called.
        return response()->json(InstantRequest::where('medical_worker_id', $worker->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with('shift')
            ->get());
    }

    public function bidInvitations(Request $request)
    {
        $worker = Auth::guard('medical-worker')->user();
        if (!$worker) { return response()->json(['error' => 'Unauthenticated.'], 401); }

        // The BidInvitation model is not imported, this will fail if called.
        return response()->json(BidInvitation::where('medical_worker_id', $worker->id)
            ->where('status', 'open')
            ->where('closes_at', '>', now())
            ->with('shift')
            ->get());
    }

    public function shiftHistory(Request $request)
    {
        $worker = Auth::guard('medical-worker')->user();
        if (!$worker) { return response()->json(['error' => 'Unauthenticated.'], 401); }

        return response()->json($worker->shifts()
            ->where('status', 'completed')
            ->orderBy('end_datetime', 'desc')
            ->limit(3)
            ->get());
    }

    public function acceptInstantRequest(Request $request, $id)
    {
        $worker = Auth::guard('medical-worker')->user();
        if (!$worker) { return response()->json(['error' => 'Unauthenticated.'], 401); }
        
        $instantRequest = InstantRequest::findOrFail($id);
        
        if ($instantRequest->medical_worker_id !== $worker->id || 
            $instantRequest->status !== 'pending' || 
            $instantRequest->expires_at <= now()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $instantRequest->shift->update([
            'medical_worker_id' => $worker->id,
            'status' => 'confirmed'
        ]);

        $instantRequest->update([
            'status' => 'accepted'
        ]);

        return response()->json(['message' => 'Shift accepted successfully']);
    }

    public function applyToBidInvitation(Request $request, $id)
    {
        $worker = Auth::guard('medical-worker')->user();
        if (!$worker) { return response()->json(['error' => 'Unauthenticated.'], 401); }
        
        $bidInvitation = BidInvitation::findOrFail($id);
        
        if ($bidInvitation->medical_worker_id !== $worker->id || 
            $bidInvitation->status !== 'open' || 
            $bidInvitation->closes_at <= now()) {
            return response()->json(['error' => 'Invalid invitation'], 400);
        }

        $bidAmount = $request->input('bid_amount');
        
        if ($bidAmount < $bidInvitation->minimum_bid) {
            return response()->json([
                'error' => 'Bid amount must be at least $' . $bidInvitation->minimum_bid
            ], 400);
        }

        // The Bid model is not imported, this will fail if called.
        Bid::create([
            'bid_invitation_id' => $bidInvitation->id,
            'medical_worker_id' => $worker->id,
            'amount' => $bidAmount
        ]);

        return response()->json([
            'message' => 'Bid submitted successfully',
            'bid_amount' => $bidAmount
        ]);
    }
}
