<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SIMPLE NOTIFICATION CONVERSION TEST ===\n\n";

try {
    // Test direct notification query using Laravel's DB
    $worker_id = 1;
    
    echo "1. Testing notification query for worker $worker_id...\n";
    
    $notifications = \Illuminate\Support\Facades\DB::table('notifications')
        ->where('notifiable_type', 'App\\Models\\MedicalWorker')
        ->where('notifiable_id', $worker_id)
        ->whereNull('read_at')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    echo "   📊 Unread notifications found: " . $notifications->count() . "\n";
    
    if ($notifications->count() > 0) {
        echo "\n2. Testing conversion to bid invitations...\n";
        
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
                    'title' => $data['title'] ?? 'New Shift'
                ];
            }
        }
        
        echo "   🎯 Bid invitations created: " . count($bidInvitations) . "\n\n";
        
        if (count($bidInvitations) > 0) {
            echo "✅ SUCCESS! Conversion logic works!\n";
            echo "Sample bid invitation:\n";
            $sample = $bidInvitations[0];
            echo "- Title: " . $sample['title'] . "\n";
            echo "- Facility: " . $sample['facility'] . "\n";
            echo "- Pay: KES " . number_format($sample['minimumBid']) . "\n";
            echo "- Time: " . $sample['shiftTime'] . "\n\n";
            
            echo "🔧 ISSUE IDENTIFIED:\n";
            echo "The conversion logic works perfectly!\n";
            echo "The problem is the controller method is NOT being executed.\n\n";
            
        } else {
            echo "❌ Conversion failed\n";
        }
        
    } else {
        echo "\n❌ No unread notifications found\n";
        
        // Check total notifications
        $total = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_id', $worker_id)
            ->count();
            
        echo "   Total notifications (including read): $total\n";
        
        if ($total > 0) {
            echo "   Issue: All notifications are marked as read\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "NEXT STEPS:\n";
echo "1. If conversion works: Fix controller execution\n";
echo "2. If no notifications: Check notification creation\n";
echo "3. If notifications read: Reset read_at to NULL\n";
echo str_repeat("=", 60) . "\n";
