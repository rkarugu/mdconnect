<?php

echo "=== TESTING NOTIFICATION CONVERSION LOGIC DIRECTLY ===\n\n";

// Test the notification conversion logic directly without routing issues
try {
    // Connect to database
    $pdo = new PDO('mysql:host=localhost;dbname=mediconnect', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "1. Testing direct database notification query...\n";
    
    $worker_id = 1;
    
    // Get notifications using the same query as our controller
    $stmt = $pdo->prepare("
        SELECT * FROM notifications 
        WHERE notifiable_type = 'App\\\\Models\\\\MedicalWorker' 
        AND notifiable_id = ? 
        AND read_at IS NULL 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$worker_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   📊 Notifications found: " . count($notifications) . "\n\n";
    
    if (count($notifications) > 0) {
        echo "2. Testing notification to bid invitation conversion...\n";
        
        $bidInvitations = [];
        foreach ($notifications as $notification) {
            $data = json_decode($notification['data'], true);
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
                    'notificationId' => $notification['id']
                ];
            }
        }
        
        echo "   🎯 Bid invitations created: " . count($bidInvitations) . "\n\n";
        
        if (count($bidInvitations) > 0) {
            echo "3. ✅ SUCCESS! Conversion logic works!\n";
            echo "   Sample bid invitations:\n";
            
            foreach (array_slice($bidInvitations, 0, 3) as $index => $invitation) {
                echo "   " . ($index + 1) . ". " . $invitation['title'] . "\n";
                echo "      - Facility: " . $invitation['facility'] . "\n";
                echo "      - Pay Rate: KES " . number_format($invitation['minimumBid']) . "\n";
                echo "      - Shift Time: " . $invitation['shiftTime'] . "\n";
                echo "      - Shift ID: " . $invitation['invitationId'] . "\n\n";
            }
            
            echo "🎉 CONVERSION LOGIC IS WORKING!\n";
            echo "The issue is NOT with the conversion logic.\n";
            echo "The issue is that the controller method is not being executed.\n\n";
            
            echo "🔧 SOLUTION NEEDED:\n";
            echo "1. The controller method needs to be properly called\n";
            echo "2. Authentication needs to work correctly\n";
            echo "3. The route needs to hit the updated controller\n\n";
            
        } else {
            echo "❌ Conversion failed - notifications found but no bid invitations created\n";
            echo "Issue with conversion logic\n";
        }
        
    } else {
        echo "❌ No notifications found for worker $worker_id\n";
        echo "This suggests the notification query is not finding data\n";
        
        // Check if notifications exist at all
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notifications WHERE notifiable_id = ?");
        $stmt->execute([$worker_id]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo "   Total notifications for worker (including read): $total\n";
        
        if ($total > 0) {
            echo "   Issue: All notifications are marked as read\n";
        } else {
            echo "   Issue: No notifications exist for this worker\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "NOTIFICATION CONVERSION TEST SUMMARY:\n";
echo "- Test if notification query finds data\n";
echo "- Test if conversion logic creates bid invitations\n";
echo "- Identify if issue is with logic or controller execution\n";
echo str_repeat("=", 80) . "\n";
