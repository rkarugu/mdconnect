<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'Notification endpoint is working',
            'data' => [],
            'count' => 0
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        return response()->json(['message' => 'Notification marked as read (placeholder)']);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        return response()->json(['message' => 'All notifications marked as read (placeholder)']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return response()->json(['message' => 'Notification deleted (placeholder)']);
    }
}
