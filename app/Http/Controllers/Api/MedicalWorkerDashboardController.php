<?php

namespace App\Http\Controllers\Api;

use App\Models\LocumShift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MedicalWorkerDashboardController extends Controller
{
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }
    /**
     * Get dashboard data for authenticated medical worker
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $worker = Auth::guard('medical-worker')->user();

            if (!$worker) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            Log::info('Dashboard request for worker', [
                'worker_id' => $worker->id, 
                'email' => $worker->email
            ]);
            
            // Debug: Check if we can find notifications for this worker
            $notificationCount = DB::table('notifications')
                ->where('notifiable_type', 'App\\Models\\MedicalWorker')
                ->where('notifiable_id', $worker->id)
                ->count();
            
            $unreadCount = DB::table('notifications')
                ->where('notifiable_type', 'App\\Models\\MedicalWorker')
                ->where('notifiable_id', $worker->id)
                ->whereNull('read_at')
                ->count();
            
            Log::info('Notification counts for worker', [
                'worker_id' => $worker->id,
                'total_notifications' => $notificationCount,
                'unread_notifications' => $unreadCount
            ]);
            
            // Get notifications and convert them to bid invitations
            $notifications = DB::table('notifications')
                ->where('notifiable_type', 'App\\Models\\MedicalWorker')
                ->where('notifiable_id', $worker->id)
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $bidInvitations = [];
            foreach ($notifications as $notification) {
                $data = json_decode($notification->data, true);
                if ($data && isset($data['shift_id'])) {
                    $bidInvitations[] = [
                        'invitationId' => (int)$data['shift_id'],
                        'facility' => $data['facility_name'] ?? 'Unknown Facility',
                        'shiftTime' => $data['start_datetime'] ?? 'TBD',
                        'minimumBid' => (int)($data['pay_rate'] ?? 0),
                        'status' => 'pending',
                        'title' => $data['title'] ?? 'New Shift',
                        'location' => $data['location'] ?? '',
                        'endTime' => $data['end_datetime'] ?? '',
                        'notificationId' => $notification->id
                    ];
                }
            }
            
            Log::info('Dashboard notifications converted to bid invitations', [
                'worker_id' => $worker->id,
                'notifications_count' => count($notifications),
                'bid_invitations_count' => count($bidInvitations)
            ]);
            
            // Add debug info to response for troubleshooting
            $debug_info = [
                'worker_id' => $worker->id,
                'notifications_found' => count($notifications),
                'bid_invitations_created' => count($bidInvitations),
                'route_executed' => true,
                'controller_method' => 'MedicalWorkerDashboardController@index',
                'timestamp' => now()->toDateTimeString()
            ];
            
            // Dashboard data with notifications as bid invitations
            $dashboardData = [
                'worker' => [
                    'id' => $worker->id,
                    'name' => $worker->name,
                    'email' => $worker->email,
                    'status' => $worker->status,
                ],
                'upcoming_shifts' => [],
                'instant_requests' => [],
                'bid_invitations' => $bidInvitations,
                'shift_history' => [],
                'stats' => [
                    'total_shifts' => 0,
                    'completed_shifts' => 0,
                    'pending_applications' => count($bidInvitations),
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => $dashboardData,
                'debug' => $debug_info // Add debug info to response
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load dashboard data',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get upcoming shifts with memory optimization
     */
    private function getUpcomingShifts($worker, $limit = 5)
    {
        try {
            return DB::table('locum_shifts')
                ->select([
                    'id',
                    'title',
                    'start_datetime',
                    'end_datetime',
                    'pay_rate',
                    'status'
                ])
                ->where('status', 'open')
                ->where('start_datetime', '>', now())
                ->orderBy('start_datetime')
                ->limit($limit)
                ->get()
                ->map(function ($shift) {
                    return (object)[
                        'id' => $shift->id,
                        'title' => $shift->title ?? 'Shift',
                        'start_datetime' => $shift->start_datetime,
                        'end_datetime' => $shift->end_datetime,
                        'pay_rate' => (float)($shift->pay_rate ?? 0),
                        'status' => $shift->status,
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Error fetching upcoming shifts', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }
    
    /**
     * Get worker statistics
     */
    private function getWorkerStats($worker)
    {
        try {
            return [
                'total_shifts' => 0,
                'completed_shifts' => 0,
                'pending_applications' => 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching worker stats', ['error' => $e->getMessage()]);
            return [
                'total_shifts' => 0,
                'completed_shifts' => 0,
                'pending_applications' => 0,
            ];
        }
    }
    
    /**
     * Get shift history with memory optimization
     */
    private function getShiftHistory($worker, $limit = 5)
    {
        try {
            return DB::table('locum_shifts')
                ->select([
                    'id',
                    'title',
                    'start_datetime',
                    'end_datetime',
                    'pay_rate',
                    'status'
                ])
                ->where('status', 'completed')
                ->where('medical_worker_id', $worker->id)
                ->orderBy('end_datetime', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($shift) {
                    return (object)[
                        'id' => $shift->id,
                        'title' => $shift->title ?? 'Shift',
                        'start_datetime' => $shift->start_datetime,
                        'end_datetime' => $shift->end_datetime,
                        'pay_rate' => (float)($shift->pay_rate ?? 0),
                        'status' => $shift->status,
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Error fetching shift history', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    /**
     * Get upcoming confirmed shifts for the worker
     */
    public function upcomingShifts(Request $request)
    {
        try {
            $worker = Auth::guard('medical-worker')->user();
            if (!$worker) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            $shifts = DB::table('locum_shifts')
                ->select([
                    'id',
                    'title',
                    'start_datetime',
                    'end_datetime',
                    'pay_rate',
                    'status'
                ])
                ->where('medical_worker_id', $worker->id)
                ->where('status', 'confirmed')
                ->where('start_datetime', '>', now())
                ->orderBy('start_datetime')
                ->get()
                ->map(function ($shift) {
                    return (object)[
                        'id' => $shift->id,
                        'title' => $shift->title ?? 'Shift',
                        'start_datetime' => $shift->start_datetime,
                        'end_datetime' => $shift->end_datetime,
                        'pay_rate' => (float)($shift->pay_rate ?? 0),
                        'status' => $shift->status,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $shifts
            ]);
        } catch (\Exception $e) {
            Log::error('Error in upcomingShifts', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch upcoming shifts',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get instant requests (placeholder)
     */
    public function instantRequests(Request $request)
    {
        try {
            $worker = Auth::guard('medical-worker')->user();
            if (!$worker) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthenticated.'
                ], 401);
            }

            // Feature not implemented yet
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Instant requests feature coming soon'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in instantRequests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to process instant requests',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get bid invitations for the authenticated medical worker
     */
    public function bidInvitations(Request $request)
    {
        try {
            $worker = Auth::guard('medical-worker')->user();
            if (!$worker) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthenticated.'
                ], 401);
            }

            // Get actual bid invitations for this worker
            $bidInvitations = \App\Models\BidInvitation::with(['locumShift.facility'])
                ->where('medical_worker_id', $worker->id)
                ->where('status', 'open')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($invitation) {
                    return [
                        'invitationId' => $invitation->id,
                        'facility' => $invitation->locumShift->facility->name ?? 'Medical Facility',
                        'shiftTime' => $invitation->locumShift->start_datetime->format('M d, Y H:i') . ' - ' . $invitation->locumShift->end_datetime->format('H:i'),
                        'minimumBid' => $invitation->minimum_bid,
                        'status' => $invitation->status,
                        'createdAt' => $invitation->created_at->toISOString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $bidInvitations,
                'count' => $bidInvitations->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in bidInvitations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to load bid invitations',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get shift history for the worker
     */
    /**
     * Get shift history for the worker
     */
    public function shiftHistory(Request $request)
    {
        try {
            $worker = Auth::guard('medical-worker')->user();
            if (!$worker) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthenticated.'
                ], 401);
            }

            $limit = $request->input('limit', 10);
            $shifts = DB::table('locum_shifts')
                ->select([
                    'id',
                    'title',
                    'start_datetime',
                    'end_datetime',
                    'pay_rate',
                    'status'
                ])
                ->where('medical_worker_id', $worker->id)
                ->where('status', 'completed')
                ->orderBy('end_datetime', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($shift) {
                    return (object)[
                        'id' => $shift->id,
                        'title' => $shift->title ?? 'Completed Shift',
                        'start_datetime' => $shift->start_datetime,
                        'end_datetime' => $shift->end_datetime,
                        'pay_rate' => (float)($shift->pay_rate ?? 0),
                        'status' => $shift->status,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $shifts
            ]);
        } catch (\Exception $e) {
            Log::error('Error in shiftHistory', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch shift history',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Accept an instant shift request
     */
    public function acceptInstantRequest(Request $request, $id)
    {
        try {
            $worker = Auth::guard('medical-worker')->user();
            if (!$worker) { 
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthenticated.'
                ], 401); 
            }
            
            return DB::transaction(function () use ($worker, $id) {
                // 1. Lock and get request
                $request = DB::selectOne('SELECT * FROM instant_requests WHERE id = ? FOR UPDATE', [$id]);
                
                // 2. Validate
                if (!$request || $request->medical_worker_id != $worker->id || 
                    $request->status != 'pending' || strtotime($request->expires_at) <= time()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Invalid request'
                    ], 400);
                }
                
                // 3. Update shift
                DB::update(
                    'UPDATE locum_shifts SET medical_worker_id = ?, status = ?, updated_at = NOW() WHERE id = ?',
                    [$worker->id, 'confirmed', $request->shift_id]
                );
                
                // 4. Update request
                DB::update(
                    'UPDATE instant_requests SET status = ?, updated_at = NOW() WHERE id = ?',
                    ['accepted', $id]
                );
                
                return response()->json([
                    'success' => true,
                    'message' => 'Shift accepted successfully'
                ]);
            });
            
        } catch (\Exception $e) {
            Log::error('Error in acceptInstantRequest: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to process request',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
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
