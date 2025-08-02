<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Simple test route for notifications
Route::get('/test/notifications', function (Request $request) {
    try {
        // Get authenticated user through Sanctum
        $user = $request->user();
        
        if (!$user || !($user instanceof \App\Models\MedicalWorker)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Use direct database query
        $notifications = \DB::table('notifications')
            ->where('notifiable_type', 'App\\Models\\MedicalWorker')
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        $formattedNotifications = $notifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'data' => json_decode($notification->data, true),
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedNotifications,
            'total' => $notifications->count()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Internal server error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
})->middleware('auth:sanctum');
