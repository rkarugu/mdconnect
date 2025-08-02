<?php

echo "=== TESTING DASHBOARD WITH DEBUG INFO ===\n\n";

// Login and get token
$loginUrl = 'http://localhost/mediconnect/public/api/medical-worker/login';
$loginData = ['email' => 'ayden@uptownnvintage.com', 'password' => 'password'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$loginData = json_decode($response, true);
$token = $loginData['data']['token'] ?? null;

if (!$token) {
    echo "❌ Authentication failed\n";
    exit(1);
}

echo "1. ✅ Authentication successful\n\n";

// Test dashboard with debug info
echo "2. Testing dashboard with debug information...\n";
$dashboardUrl = 'http://localhost/mediconnect/public/api/worker/dashboard';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dashboardUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Status: $httpCode\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    echo "3. 🔍 DEBUG INFORMATION:\n";
    if (isset($data['debug'])) {
        $debug = $data['debug'];
        echo "   ✅ Route executed: " . ($debug['route_executed'] ? 'YES' : 'NO') . "\n";
        echo "   📊 Worker ID: " . $debug['worker_id'] . "\n";
        echo "   📧 Notifications found: " . $debug['notifications_found'] . "\n";
        echo "   🎯 Bid invitations created: " . $debug['bid_invitations_created'] . "\n";
        echo "   ⏰ Timestamp: " . $debug['timestamp'] . "\n\n";
        
        if ($debug['notifications_found'] > 0 && $debug['bid_invitations_created'] === 0) {
            echo "   🚨 ISSUE IDENTIFIED:\n";
            echo "   - Notifications exist in database (" . $debug['notifications_found'] . ")\n";
            echo "   - But conversion to bid invitations failed (0 created)\n";
            echo "   - This suggests a problem with the conversion logic\n\n";
        } elseif ($debug['notifications_found'] === 0) {
            echo "   🚨 ISSUE IDENTIFIED:\n";
            echo "   - No notifications found for worker " . $debug['worker_id'] . "\n";
            echo "   - This suggests a problem with the notification query\n\n";
        } elseif ($debug['bid_invitations_created'] > 0) {
            echo "   🎉 SUCCESS!\n";
            echo "   - Notifications found and converted successfully\n";
            echo "   - Bid invitations created: " . $debug['bid_invitations_created'] . "\n\n";
        }
        
    } else {
        echo "   ❌ No debug information in response\n";
        echo "   This means our updated route is not being executed\n\n";
    }
    
    echo "4. 📋 DASHBOARD DATA:\n";
    if (isset($data['data'])) {
        $dashboardData = $data['data'];
        echo "   - Worker: " . $dashboardData['worker']['name'] . " (ID: " . $dashboardData['worker']['id'] . ")\n";
        echo "   - Bid Invitations: " . count($dashboardData['bid_invitations']) . "\n";
        echo "   - Pending Applications: " . $dashboardData['stats']['pending_applications'] . "\n\n";
        
        if (count($dashboardData['bid_invitations']) > 0) {
            echo "   🎉 BID INVITATIONS FOUND!\n";
            foreach (array_slice($dashboardData['bid_invitations'], 0, 2) as $index => $invitation) {
                echo "   " . ($index + 1) . ". " . $invitation['title'] . " at " . $invitation['facility'] . "\n";
                echo "      Pay: KES " . number_format($invitation['minimumBid']) . "\n";
                echo "      Time: " . $invitation['shiftTime'] . "\n\n";
            }
        }
    }
    
} else {
    echo "   ❌ Dashboard call failed: $httpCode\n";
    echo "   Response: " . substr($response, 0, 200) . "...\n";
}

echo str_repeat("=", 80) . "\n";
echo "FINAL DIAGNOSIS:\n";
if (isset($debug)) {
    if ($debug['route_executed'] && $debug['notifications_found'] > 0 && $debug['bid_invitations_created'] > 0) {
        echo "🎉 SUCCESS! Medical worker notification system is FULLY FUNCTIONAL!\n";
        echo "✅ Route execution: WORKING\n";
        echo "✅ Notification retrieval: WORKING\n";
        echo "✅ Bid invitation conversion: WORKING\n";
        echo "✅ Flutter app integration: READY\n";
    } else {
        echo "🔧 DEBUGGING NEEDED:\n";
        echo ($debug['route_executed'] ? "✅" : "❌") . " Route execution\n";
        echo ($debug['notifications_found'] > 0 ? "✅" : "❌") . " Notification retrieval\n";
        echo ($debug['bid_invitations_created'] > 0 ? "✅" : "❌") . " Bid invitation conversion\n";
    }
} else {
    echo "❌ Route not executing - need to debug routing issue\n";
}
echo str_repeat("=", 80) . "\n";
