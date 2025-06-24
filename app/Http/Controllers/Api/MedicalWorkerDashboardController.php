<?php

namespace App\Http\Controllers\Api;

use App\Models\Shift;
use App\Models\InstantRequest;
use App\Models\BidInvitation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MedicalWorkerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $worker = $request->user();
        
        $dashboardData = [
            'upcoming_shifts' => $worker->shifts()
                ->where('status', 'confirmed')
                ->where('start_time', '>', now())
                ->orderBy('start_time')
                ->get(),
            'instant_requests' => InstantRequest::where('medical_worker_id', $worker->id)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->with('shift')
                ->get(),
            'bid_invitations' => BidInvitation::where('medical_worker_id', $worker->id)
                ->where('status', 'open')
                ->where('closes_at', '>', now())
                ->with('shift')
                ->get(),
            'shift_history' => $worker->shifts()
                ->where('status', 'completed')
                ->orderBy('end_time', 'desc')
                ->limit(3)
                ->get(),
        ];

        return response()->json($dashboardData);
    }

    public function upcomingShifts(Request $request)
    {
        $worker = $request->user();
        return response()->json($worker->shifts()
            ->where('status', 'confirmed')
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get());
    }

    public function instantRequests(Request $request)
    {
        $worker = $request->user();
        return response()->json(InstantRequest::where('medical_worker_id', $worker->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with('shift')
            ->get());
    }

    public function bidInvitations(Request $request)
    {
        $worker = $request->user();
        return response()->json(BidInvitation::where('medical_worker_id', $worker->id)
            ->where('status', 'open')
            ->where('closes_at', '>', now())
            ->with('shift')
            ->get());
    }

    public function shiftHistory(Request $request)
    {
        $worker = $request->user();
        return response()->json($worker->shifts()
            ->where('status', 'completed')
            ->orderBy('end_time', 'desc')
            ->limit(3)
            ->get());
    }

    public function acceptInstantRequest(Request $request, $id)
    {
        $worker = $request->user();
        
        $instantRequest = InstantRequest::findOrFail($id);
        
        // Verify request belongs to worker and is still valid
        if ($instantRequest->medical_worker_id !== $worker->id || 
            $instantRequest->status !== 'pending' || 
            $instantRequest->expires_at <= now()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        // Update shift and request status
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
        $worker = $request->user();
        
        $bidInvitation = BidInvitation::findOrFail($id);
        
        // Verify invitation belongs to worker and is still valid
        if ($bidInvitation->medical_worker_id !== $worker->id || 
            $bidInvitation->status !== 'open' || 
            $bidInvitation->closes_at <= now()) {
            return response()->json(['error' => 'Invalid invitation'], 400);
        }

        // Get bid amount from request
        $bidAmount = $request->input('bid_amount');
        
        if ($bidAmount < $bidInvitation->minimum_bid) {
            return response()->json([
                'error' => 'Bid amount must be at least $' . $bidInvitation->minimum_bid
            ], 400);
        }

        // Create bid record
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
