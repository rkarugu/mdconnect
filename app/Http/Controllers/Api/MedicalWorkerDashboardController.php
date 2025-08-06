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
                        'invitationId' => $notification->id, // Use notification ID as invitation ID
                        'id' => $notification->id, // Add explicit 'id' field
                        'facility' => $data['facility'] ?? ($data['facility_name'] ?? 'Unknown Facility'),
                        'shiftTime' => $data['start_datetime'] ?? 'TBD',
                        'minimumBid' => (int)($data['minimum_bid'] ?? ($data['pay_rate'] ?? 0)),
                        'status' => 'pending',
                        'title' => $data['title'] ?? 'New Shift',
                        'location' => $data['location'] ?? '',
                        'endTime' => $data['end_datetime'] ?? '',
                        'notificationId' => $notification->id,
                        'shift_id' => $data['shift_id'] // Include original shift_id for reference
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

            Log::info('Fetching bid invitations for worker', ['worker_id' => $worker->id]);
            
            // Get notifications that represent bid invitations (since bid invitations are sent as notifications)
            $notifications = DB::table('notifications')
                ->where('notifiable_type', 'App\\Models\\MedicalWorker')
                ->where('notifiable_id', $worker->id)
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->get();
                
            $bidInvitations = [];
            
            foreach ($notifications as $notification) {
                $data = json_decode($notification->data, true);
                
                if (!isset($data['shift_id'])) {
                    continue;
                }
                
                // Get the shift details
                $shift = LocumShift::with('facility')->find($data['shift_id']);
                
                if (!$shift) {
                    continue;
                }
                
                // Skip expired shifts (shifts that have already started)
                if ($shift->start_datetime <= now()) {
                    Log::info('Skipping expired shift', [
                        'shift_id' => $shift->id,
                        'start_datetime' => $shift->start_datetime,
                        'current_time' => now()
                    ]);
                    continue;
                }
                
                // Skip shifts that are no longer open
                if ($shift->status !== 'open') {
                    continue;
                }
                
                $bidInvitations[] = [
                    'invitationId' => (int)$notification->id,
                    'facility' => $shift->facility->facility_name ?? 'Medical Facility',
                    'shiftTime' => $shift->start_datetime->format('M d, Y H:i') . ' - ' . $shift->end_datetime->format('H:i'),
                    'minimumBid' => $data['minimum_bid'] ?? 0,
                    'status' => 'pending',
                    'title' => $data['title'] ?? $shift->title ?? 'New Shift',
                    'shift_id' => $shift->id,
                    'createdAt' => $notification->created_at,
                ];
            }
            
            Log::info('Bid invitations fetched', [
                'worker_id' => $worker->id,
                'total_notifications' => count($notifications),
                'valid_invitations' => count($bidInvitations)
            ]);

            return response()->json([
                'success' => true,
                'data' => $bidInvitations,
                'count' => count($bidInvitations)
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
        try {
            $worker = auth('sanctum')->user();
            if (!$worker) {
                return response()->json(['success' => false, 'error' => 'Unauthenticated.'], 401);
            }
            
            Log::info('Bid invitation application attempt', [
                'worker_id' => $worker->id,
                'invitation_id' => $id,
                'invitation_id_type' => gettype($id),
                'request_data' => $request->all()
            ]);
            
            // First, let's see what notifications exist for this worker
            $allNotifications = DB::table('notifications')
                ->where('notifiable_type', 'App\\Models\\MedicalWorker')
                ->where('notifiable_id', $worker->id)
                ->select('id', 'type', 'read_at', 'created_at', 'data')
                ->orderBy('created_at', 'desc')
                ->limit(20) // Increased limit for debugging
                ->get();
                
            Log::info('All notifications for worker', [
                'worker_id' => $worker->id,
                'total_notifications' => count($allNotifications),
                'notification_ids' => $allNotifications->pluck('id')->toArray()
            ]);
            
            // First try to find by UUID (direct match)
            $notification = $allNotifications->firstWhere('id', $id);
            
            // If not found by UUID, try to find by numeric ID in data
            if (!$notification) {
                foreach ($allNotifications as $notif) {
                    $data = json_decode($notif->data, true);
                    // Check if this notification has a numeric ID that matches
                    if (isset($data['id']) && $data['id'] == $id) {
                        $notification = $notif;
                        Log::info('Found notification by numeric ID in data', [
                            'notification_id' => $notification->id,
                            'data_id' => $data['id']
                        ]);
                        break;
                    }
                    // Also check shift_id for backward compatibility
                    if (isset($data['shift_id']) && $data['shift_id'] == $id) {
                        $notification = $notif;
                        Log::info('Found notification by shift_id', [
                            'notification_id' => $notification->id,
                            'shift_id' => $data['shift_id']
                        ]);
                        break;
                    }
                }
            }
            
            Log::info('Notification lookup result', [
                'notification_found' => $notification ? 'yes' : 'no',
                'notification_id' => $id,
                'worker_id' => $worker->id,
                'notification_details' => $notification ? [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'data' => json_decode($notification->data, true)
                ] : null,
                'all_notification_ids' => $allNotifications->pluck('id')->toArray()
            ]);
                
            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'error' => 'Bid invitation not found or already processed'
                ], 404);
            }
            
            $notificationData = json_decode($notification->data, true);
            $shiftId = $notificationData['shift_id'] ?? null;
            
            if (!$shiftId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid bid invitation data'
                ], 400);
            }
            
            // Find the actual shift
            $shift = LocumShift::find($shiftId);
            if (!$shift || $shift->status !== 'open') {
                return response()->json([
                    'success' => false,
                    'error' => 'Shift is no longer available'
                ], 400);
            }
            
            // Check if shift has expired
            if ($shift->start_datetime <= now()) {
                return response()->json([
                    'success' => false,
                    'error' => 'This shift has already started or expired'
                ], 400);
            }
            
            // Check if worker already applied
            $existingApplication = \App\Models\ShiftApplication::where('shift_id', $shiftId)
                ->where('medical_worker_id', $worker->id)
                ->first();
                
            if ($existingApplication) {
                return response()->json([
                    'success' => false,
                    'error' => 'You have already applied for this shift'
                ], 400);
            }
            
            // Create shift application
            $application = \App\Models\ShiftApplication::create([
                'shift_id' => $shiftId,
                'medical_worker_id' => $worker->id,
                'status' => 'waiting',
                'applied_at' => now(),
            ]);
            
            // Mark notification as read
            DB::table('notifications')
                ->where('id', $id)
                ->update(['read_at' => now()]);
            
            Log::info('Bid invitation accepted successfully', [
                'worker_id' => $worker->id,
                'shift_id' => $shiftId,
                'application_id' => $application->id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'application_id' => $application->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in applyToBidInvitation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to process application',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get shift applications for the authenticated medical worker
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShiftApplications(Request $request)
    {
        try {
            $worker = auth('sanctum')->user();
            if (!$worker) {
                return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
            }

            $applications = \App\Models\ShiftApplication::with(['shift.facility'])
                ->where('medical_worker_id', $worker->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($application) {
                    $shift = $application->shift;
                    return [
                        'id' => $application->id,
                        'shift_id' => $application->shift_id,
                        'status' => $application->status,
                        'facility_name' => $shift->facility->facility_name ?? 'Unknown Facility',
                        'shift_title' => $shift->title ?? 'Shift',
                        'shift_time' => $shift->start_datetime->format('M d, Y H:i') . ' - ' . $shift->end_datetime->format('H:i'),
                        'pay_rate' => (int)$shift->pay_rate,
                        'applied_at' => $application->created_at->toISOString(),
                        'selected_at' => $application->selected_at?->toISOString(),
                        'shift_start_time' => $shift->start_datetime->toISOString(),
                        'shift_end_time' => $shift->end_datetime->toISOString(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching shift applications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch shift applications'
            ], 500);
        }
    }

    /**
     * Start a shift (mark application as in progress)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function startShift(Request $request, $id)
    {
        try {
            $worker = auth('sanctum')->user();
            if (!$worker) {
                return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
            }

            $application = \App\Models\ShiftApplication::with('shift')
                ->where('id', $id)
                ->where('medical_worker_id', $worker->id)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'error' => 'Shift application not found'
                ], 404);
            }

            if ($application->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'error' => 'Shift is not approved yet'
                ], 400);
            }

            $shift = $application->shift;
            $now = now();
            $startTime = $shift->start_datetime;
            $allowedStartTime = $startTime->subMinutes(15);

            if ($now->isBefore($allowedStartTime)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot start shift more than 15 minutes early'
                ], 400);
            }

            if ($now->isAfter($startTime->addHour())) {
                return response()->json([
                    'success' => false,
                    'error' => 'Shift start time has passed'
                ], 400);
            }

            // Update application status to in_progress
            $application->update([
                'status' => 'in_progress',
                'started_at' => $now
            ]);

            // Update shift status and track start time if first worker
            if (!$shift->actual_start_time) {
                $shift->update([
                    'status' => 'in_progress',
                    'actual_start_time' => $now
                ]);
                
                Log::info('First worker started - Shift timing started', [
                    'shift_id' => $shift->id,
                    'actual_start_time' => $now->toISOString()
                ]);
            } else {
                $shift->update(['status' => 'in_progress']);
            }

            Log::info('Shift started successfully', [
                'worker_id' => $worker->id,
                'application_id' => $application->id,
                'shift_id' => $shift->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shift started successfully',
                'application_id' => $application->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error starting shift', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to start shift'
            ], 500);
        }
    }

    /**
     * Complete a shift (mark application as completed)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeShift(Request $request, $id)
    {
        try {
            $worker = auth('sanctum')->user();
            if (!$worker) {
                return response()->json(['success' => false, 'error' => 'Unauthenticated'], 401);
            }

            $application = \App\Models\ShiftApplication::with('shift')
                ->where('id', $id)
                ->where('medical_worker_id', $worker->id)
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'error' => 'Shift application not found'
                ], 404);
            }

            if ($application->status !== 'in_progress') {
                return response()->json([
                    'success' => false,
                    'error' => 'Shift is not in progress'
                ], 400);
            }

            $shift = $application->shift;
            $now = now();
            $endTime = $shift->end_datetime;
            $allowedCompleteTime = $endTime->subMinutes(5);

            if ($now->isBefore($allowedCompleteTime)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Cannot complete shift more than 5 minutes early'
                ], 400);
            }

            // Update application status to completed
            $application->update([
                'status' => 'completed',
                'completed_at' => $now
            ]);

            // Check if this is the first worker to start (for shift timing)
            if (!$shift->actual_start_time) {
                $shift->updateStartTime();
            }

            // Check if all workers have completed their shifts
            if ($shift->allWorkersCompleted()) {
                // Mark shift as completed and set end time
                $shift->updateEndTime();
                
                Log::info('All workers completed - Shift marked as completed', [
                    'shift_id' => $shift->id,
                    'duration' => $shift->duration_display
                ]);
            }

            Log::info('Shift completed successfully', [
                'worker_id' => $worker->id,
                'application_id' => $application->id,
                'shift_id' => $shift->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shift completed successfully',
                'application_id' => $application->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error completing shift', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to complete shift'
            ], 500);
        }
    }
}
