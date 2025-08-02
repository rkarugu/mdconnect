<?php

echo "=== DEBUGGING NOTIFICATION TO BID INVITATION CONVERSION ===\n\n";

// Test the exact query used in the dashboard controller
echo "1. Testing notification query for worker ID 1:\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=mediconnect', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("
        SELECT * FROM notifications 
        WHERE notifiable_type = 'App\\\\Models\\\\MedicalWorker' 
        AND notifiable_id = 1 
        AND read_at IS NULL 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Found " . count($notifications) . " unread notifications for worker 1\n\n";
    
    if (count($notifications) > 0) {
        echo "2. Sample notification data:\n";
        $sample = $notifications[0];
        echo "   - ID: " . $sample['id'] . "\n";
        echo "   - Type: " . $sample['type'] . "\n";
        echo "   - Notifiable Type: " . $sample['notifiable_type'] . "\n";
        echo "   - Notifiable ID: " . $sample['notifiable_id'] . "\n";
        echo "   - Read At: " . ($sample['read_at'] ?? 'NULL') . "\n";
        echo "   - Data: " . substr($sample['data'], 0, 100) . "...\n\n";
        
        echo "3. Testing JSON parsing:\n";
        $data = json_decode($sample['data'], true);
        if ($data) {
            echo "   ✅ JSON parsing successful\n";
            echo "   - shift_id: " . ($data['shift_id'] ?? 'MISSING') . "\n";
            echo "   - title: " . ($data['title'] ?? 'MISSING') . "\n";
            echo "   - facility_name: " . ($data['facility_name'] ?? 'MISSING') . "\n";
            echo "   - pay_rate: " . ($data['pay_rate'] ?? 'MISSING') . "\n";
            echo "   - start_datetime: " . ($data['start_datetime'] ?? 'MISSING') . "\n";
            
            if (isset($data['shift_id'])) {
                echo "\n4. Converting to bid invitation format:\n";
                $bidInvitation = [
                    'invitationId' => (int)$data['shift_id'],
                    'facility' => $data['facility_name'] ?? 'Unknown Facility',
                    'shiftTime' => $data['start_datetime'] ?? 'TBD',
                    'minimumBid' => (int)($data['pay_rate'] ?? 0),
                    'status' => 'pending',
                    'title' => $data['title'] ?? 'New Shift',
                    'location' => $data['location'] ?? '',
                    'endTime' => $data['end_datetime'] ?? '',
                    'notificationId' => $sample['id']
                ];
                
                echo "   ✅ Conversion successful:\n";
                echo "   " . json_encode($bidInvitation, JSON_PRETTY_PRINT) . "\n\n";
                
                echo "🎯 ISSUE ANALYSIS:\n";
                echo "The notification data is valid and should convert properly.\n";
                echo "The dashboard controller logic should work.\n";
                echo "Possible issues:\n";
                echo "1. Database connection issue in dashboard controller\n";
                echo "2. Authentication issue (worker ID mismatch)\n";
                echo "3. Query escaping issue with backslashes\n";
                echo "4. Laravel DB facade vs direct query difference\n";
                
            } else {
                echo "   ❌ Missing shift_id in notification data\n";
            }
        } else {
            echo "   ❌ JSON parsing failed\n";
            echo "   Raw data: " . $sample['data'] . "\n";
        }
        
    } else {
        echo "❌ No unread notifications found for worker 1\n";
        echo "Let's check all notifications for this worker:\n";
        
        $stmt = $pdo->prepare("
            SELECT * FROM notifications 
            WHERE notifiable_type = 'App\\\\Models\\\\MedicalWorker' 
            AND notifiable_id = 1 
            ORDER BY created_at DESC 
            LIMIT 5
        ");
        $stmt->execute();
        $allNotifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "   Total notifications (including read): " . count($allNotifications) . "\n";
        
        if (count($allNotifications) > 0) {
            foreach ($allNotifications as $notif) {
                echo "   - ID: " . $notif['id'] . ", Read: " . ($notif['read_at'] ? 'Yes' : 'No') . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "DEBUGGING SUMMARY:\n";
echo "- Check if notifications exist for worker 1\n";
echo "- Verify notification data structure\n";
echo "- Test JSON parsing and conversion logic\n";
echo "- Identify why dashboard controller returns empty array\n";
echo str_repeat("=", 60) . "\n";
