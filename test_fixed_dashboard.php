<?php

echo "=== TESTING FIXED DASHBOARD ROUTE ===\n\n";

// Test the new fixed dashboard route
try {
    // Step 1: Authenticate
    echo "1. Authenticating medical worker...\n";
    
    $loginData = [
        'email' => 'ayden@uptownnvintage.com',
        'password' => 'password'
    ];
    
    $loginCurl = curl_init();
    curl_setopt_array($loginCurl, [
        CURLOPT_URL => 'http://localhost/mediconnect/public/api/medical-worker/login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($loginData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ]
    ]);
    
    $loginResponse = curl_exec($loginCurl);
    $loginHttpCode = curl_getinfo($loginCurl, CURLINFO_HTTP_CODE);
    curl_close($loginCurl);
    
    if ($loginHttpCode !== 200) {
        throw new Exception("Login failed with HTTP $loginHttpCode: $loginResponse");
    }
    
    $loginData = json_decode($loginResponse, true);
    $token = $loginData['data']['token'] ?? null;
    
    if (!$token) {
        throw new Exception("No token in login response");
    }
    
    echo "   ✅ Authentication successful\n\n";
    
    // Step 2: Test FIXED dashboard route
    echo "2. Testing FIXED dashboard route...\n";
    
    $dashboardCurl = curl_init();
    curl_setopt_array($dashboardCurl, [
        CURLOPT_URL => 'http://localhost/mediconnect/public/api/worker/dashboard-working',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ]
    ]);
    
    $dashboardResponse = curl_exec($dashboardCurl);
    $dashboardHttpCode = curl_getinfo($dashboardCurl, CURLINFO_HTTP_CODE);
    curl_close($dashboardCurl);
    
    echo "   HTTP Status: $dashboardHttpCode\n";
    
    if ($dashboardHttpCode !== 200) {
        echo "   ❌ Dashboard request failed\n";
        echo "   Response: $dashboardResponse\n";
        return;
    }
    
    $dashboardData = json_decode($dashboardResponse, true);
    
    // Step 3: Check for debug information (proves route execution)
    echo "\n3. 🔍 DEBUG INFORMATION:\n";
    if (isset($dashboardData['debug'])) {
        echo "   ✅ Debug information found!\n";
        echo "   - Route executed: " . ($dashboardData['debug']['route_executed'] ? 'YES' : 'NO') . "\n";
        echo "   - Route name: " . ($dashboardData['debug']['route_name'] ?? 'Unknown') . "\n";
        echo "   - Worker ID: " . ($dashboardData['debug']['worker_id'] ?? 'Unknown') . "\n";
        echo "   - Notifications found: " . ($dashboardData['debug']['notifications_found'] ?? 0) . "\n";
        echo "   - Bid invitations created: " . ($dashboardData['debug']['bid_invitations_created'] ?? 0) . "\n";
        echo "   - Timestamp: " . ($dashboardData['debug']['timestamp'] ?? 'Unknown') . "\n";
    } else {
        echo "   ❌ No debug information in response\n";
    }
    
    // Step 4: Check dashboard data
    echo "\n4. 📋 DASHBOARD DATA:\n";
    if (isset($dashboardData['data'])) {
        $data = $dashboardData['data'];
        echo "   - Worker: " . ($data['worker']['name'] ?? 'Unknown') . " (ID: " . ($data['worker']['id'] ?? 'Unknown') . ")\n";
        echo "   - Bid Invitations: " . count($data['bidInvitations'] ?? []) . "\n";
        echo "   - Pending Applications: " . count($data['pendingApplications'] ?? []) . "\n";
        
        if (!empty($data['bidInvitations'])) {
            echo "\n   🎉 BID INVITATIONS FOUND:\n";
            foreach (array_slice($data['bidInvitations'], 0, 3) as $index => $invitation) {
                echo "   " . ($index + 1) . ". " . ($invitation['title'] ?? 'Unknown') . "\n";
                echo "      - Facility: " . ($invitation['facility'] ?? 'Unknown') . "\n";
                echo "      - Pay: KES " . number_format($invitation['minimumBid'] ?? 0) . "\n";
                echo "      - Time: " . ($invitation['shiftTime'] ?? 'TBD') . "\n\n";
            }
        }
    }
    
    // Final assessment
    echo "\n" . str_repeat("=", 80) . "\n";
    
    $hasDebug = isset($dashboardData['debug']);
    $hasBidInvitations = !empty($dashboardData['data']['bidInvitations'] ?? []);
    $routeExecuted = $dashboardData['debug']['route_executed'] ?? false;
    
    if ($hasDebug && $routeExecuted && $hasBidInvitations) {
        echo "🎉 SUCCESS! FIXED DASHBOARD ROUTE WORKING!\n";
        echo "✅ Route executed properly\n";
        echo "✅ Debug information present\n";
        echo "✅ Bid invitations converted successfully\n";
        echo "✅ Notifications are now appearing as bid invitations!\n\n";
        echo "NEXT STEP: Update Flutter app to use this fixed endpoint\n";
    } else {
        echo "❌ STILL DEBUGGING NEEDED\n";
        echo "- Route executed: " . ($routeExecuted ? 'YES' : 'NO') . "\n";
        echo "- Debug info: " . ($hasDebug ? 'YES' : 'NO') . "\n";
        echo "- Bid invitations: " . ($hasBidInvitations ? 'YES' : 'NO') . "\n";
    }
    
    echo str_repeat("=", 80) . "\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
}
