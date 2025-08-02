<?php
// Simple verification script for notification system

// Check current notifications
echo "=== Notification System Verification ===\n\n";

// Check database directly
try {
    $notifications = \DB::table('notifications')->get();
    echo "📊 Total notifications in database: " . count($notifications) . "\n";
    
    if (count($notifications) > 0) {
        echo "\n📋 Notification Details:\n";
        foreach ($notifications as $notification) {
            $data = json_decode($notification->data, true);
            echo "   ID: {$notification->id}\n";
            echo "   Worker ID: {$notification->notifiable_id}\n";
            echo "   Type: {$notification->type}\n";
            echo "   Title: " . ($data['title'] ?? 'N/A') . "\n";
            echo "   Message: " . ($data['message'] ?? 'N/A') . "\n";
            echo "   Created: {$notification->created_at}\n";
            echo "   ---\n";
        }
    } else {
        echo "❌ No notifications found in database\n";
    }
    
    // Check medical workers
    $workers = \DB::table('medical_workers')->get();
    echo "\n👥 Medical Workers: " . count($workers) . "\n";
    foreach ($workers as $worker) {
        echo "   ID: {$worker->id}, Email: {$worker->email}, Specialty: {$worker->medical_specialty_id}\n";
    }
    
    // Check facilities
    $facilities = \DB::table('medical_facilities')->get();
    echo "\n🏥 Facilities: " . count($facilities) . "\n";
    foreach ($facilities as $facility) {
        echo "   ID: {$facility->id}, Name: {$facility->name}\n";
    }
    
    // Check shifts
    $shifts = \DB::table('locum_job_requests')->get();
    echo "\n📅 Shifts: " . count($shifts) . "\n";
    foreach ($shifts as $shift) {
        echo "   ID: {$shift->id}, Facility: {$shift->facility_id}, Status: {$shift->status}\n";
    }
    
    echo "\n✅ Verification complete. Check above details.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
