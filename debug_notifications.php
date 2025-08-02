<?php
// Quick debug for notification system

echo "=== Notification Debug ===\n\n";

// Check recent notifications
try {
    $notifications = \DB::table('notifications')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    echo "📊 Recent Notifications:\n";
    foreach ($notifications as $notification) {
        echo "   ID: {$notification->id}\n";
        echo "   Type: {$notification->type}\n";
        echo "   Worker ID: {$notification->notifiable_id}\n";
        echo "   Created: {$notification->created_at}\n";
        
        $data = json_decode($notification->data, true);
        echo "   Title: " . ($data['title'] ?? 'N/A') . "\n";
        echo "   ---\n";
    }
    
    // Check medical workers
    $workers = \DB::table('medical_workers')->get();
    echo "\n👥 Medical Workers:\n";
    foreach ($workers as $worker) {
        echo "   ID: {$worker->id}, Email: {$worker->email}\n";
    }
    
    // Check specialties
    $specialties = \DB::table('medical_specialties')->get();
    echo "\n🏷️ Specialties:\n";
    foreach ($specialties as $specialty) {
        echo "   ID: {$specialty->id}, Name: {$specialty->name}\n";
    }
    
    // Check shifts
    $shifts = \DB::table('locum_shifts')->orderBy('created_at', 'desc')->limit(5)->get();
    echo "\n📅 Recent Shifts:\n";
    foreach ($shifts as $shift) {
        echo "   ID: {$shift->id}, Title: {$shift->title}, Worker Type: {$shift->worker_type}, Created: {$shift->created_at}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🔍 Debug complete. Check above data.\n";
