<?php

echo "=== TESTING AUTHENTICATED DASHBOARD ENDPOINT ===\n\n";

// First, let's get a valid token by logging in
echo "1. Logging in to get authentication token...\n";

$loginUrl = 'http://localhost/mediconnect/public/api/medical-worker/login';
$loginData = [
    'email' => 'ayden@uptownnvintage.com',
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Login HTTP Status: $loginHttpCode\n";

if ($loginHttpCode === 200) {
    $loginData = json_decode($loginResponse, true);
    if (isset($loginData['token'])) {
        $token = $loginData['token'];
        echo "   ✅ Login successful! Token obtained.\n\n";
        
        // Now test the dashboard with authentication
        echo "2. Testing dashboard with authentication...\n";
        
        $dashboardUrl = 'http://localhost/mediconnect/public/api/worker/dashboard';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $dashboardUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        $dashboardResponse = curl_exec($ch);
        $dashboardHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "   Dashboard HTTP Status: $dashboardHttpCode\n";
        
        if ($dashboardHttpCode === 200) {
            $dashboardData = json_decode($dashboardResponse, true);
            
            if (isset($dashboardData['success']) && $dashboardData['success'] && isset($dashboardData['data'])) {
                $data = $dashboardData['data'];
                
                echo "   ✅ Authenticated dashboard call successful!\n\n";
                echo "   Dashboard Data:\n";
                echo "   - Worker ID: " . $data['worker']['id'] . "\n";
                echo "   - Worker Name: " . $data['worker']['name'] . "\n";
                echo "   - Upcoming Shifts: " . count($data['upcoming_shifts']) . "\n";
                echo "   - Instant Requests: " . count($data['instant_requests']) . "\n";
                echo "   - Bid Invitations: " . count($data['bid_invitations']) . " 🎯\n";
                echo "   - Shift History: " . count($data['shift_history']) . "\n";
                echo "   - Pending Applications: " . $data['stats']['pending_applications'] . "\n\n";
                
                if (count($data['bid_invitations']) > 0) {
                    echo "   🎉 SUCCESS! BID INVITATIONS FOUND! 🎉\n";
                    echo "   Sample bid invitations:\n";
                    
                    foreach (array_slice($data['bid_invitations'], 0, 3) as $index => $invitation) {
                        echo "     " . ($index + 1) . ". " . $invitation['title'] . "\n";
                        echo "        - Facility: " . $invitation['facility'] . "\n";
                        echo "        - Pay Rate: " . $invitation['minimumBid'] . "\n";
                        echo "        - Shift Time: " . $invitation['shiftTime'] . "\n";
                        echo "        - Status: " . $invitation['status'] . "\n\n";
                    }
                    
                    echo "   🚀 FLUTTER APP INTEGRATION STATUS:\n";
                    echo "   ✅ Authentication working\n";
                    echo "   ✅ Dashboard endpoint working\n";
                    echo "   ✅ Notifications converted to bid invitations\n";
                    echo "   ✅ Flutter app should now show " . count($data['bid_invitations']) . " bid invitations\n\n";
                    
                } else {
                    echo "   ❌ No bid invitations found even with authentication\n";
                    echo "   This suggests the notification conversion logic has an issue.\n\n";
                }
                
            } else {
                echo "   ❌ Dashboard response format error\n";
                echo "   Response: " . substr($dashboardResponse, 0, 200) . "...\n";
            }
            
        } else {
            echo "   ❌ Dashboard call failed: $dashboardHttpCode\n";
            echo "   Response: " . substr($dashboardResponse, 0, 200) . "...\n";
        }
        
    } else {
        echo "   ❌ Login successful but no token in response\n";
        echo "   Response: " . substr($loginResponse, 0, 200) . "...\n";
    }
    
} else {
    echo "   ❌ Login failed: $loginHttpCode\n";
    echo "   Response: " . substr($loginResponse, 0, 200) . "...\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "AUTHENTICATION TEST SUMMARY:\n";
echo ($loginHttpCode === 200 ? "✅" : "❌") . " Medical worker login\n";
echo ($dashboardHttpCode === 200 ? "✅" : "❌") . " Authenticated dashboard access\n";
echo (isset($data['bid_invitations']) && count($data['bid_invitations']) > 0 ? "✅" : "❌") . " Notifications converted to bid invitations\n";
echo "\n🎯 NEXT STEP: " . (isset($data['bid_invitations']) && count($data['bid_invitations']) > 0 ? "INTEGRATION COMPLETE!" : "Debug notification conversion logic") . "\n";
echo str_repeat("=", 80) . "\n";
