<?php

echo "=== FINAL WORKING SOLUTION TEST ===\n\n";

// Create the simplest possible working dashboard endpoint test
echo "Testing direct URL access to our working dashboard route...\n\n";

// Test without authentication first to see if route exists
$testUrl = 'http://localhost/mediconnect/public/api/worker/dashboard-final';

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $testUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json'
    ]
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    if (isset($data['debug'])) {
        echo "🎉 SUCCESS! Route is working!\n";
        echo "✅ Route executed: " . ($data['debug']['route_executed'] ? 'YES' : 'NO') . "\n";
        echo "✅ Route name: " . ($data['debug']['route_name'] ?? 'Unknown') . "\n";
        echo "✅ Notifications found: " . ($data['debug']['notifications_found'] ?? 0) . "\n";
        echo "✅ Bid invitations created: " . ($data['debug']['bid_invitations_created'] ?? 0) . "\n";
        
        if (isset($data['data']['bidInvitations']) && count($data['data']['bidInvitations']) > 0) {
            echo "\n🎯 BID INVITATIONS FOUND:\n";
            foreach (array_slice($data['data']['bidInvitations'], 0, 3) as $index => $invitation) {
                echo "   " . ($index + 1) . ". " . ($invitation['title'] ?? 'Unknown') . "\n";
                echo "      - Facility: " . ($invitation['facility'] ?? 'Unknown') . "\n";
                echo "      - Pay: KES " . number_format($invitation['minimumBid'] ?? 0) . "\n\n";
            }
            
            echo "🎉 MISSION ACCOMPLISHED!\n";
            echo "✅ Notifications are successfully converting to bid invitations!\n";
            echo "✅ Dashboard endpoint is working!\n";
            echo "✅ Flutter app can now receive bid invitations!\n\n";
            
            echo "NEXT STEP: Update Flutter app to use this endpoint:\n";
            echo "URL: $testUrl\n";
            
        } else {
            echo "\n❌ No bid invitations found\n";
            echo "Check if notifications exist and are unread\n";
        }
        
    } else {
        echo "❌ No debug information - route may not be executing\n";
    }
    
} else {
    echo "❌ Route not accessible - HTTP $httpCode\n";
    echo "Response: $response\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "FINAL SOLUTION STATUS:\n";
echo "- Test if our working dashboard route is accessible\n";
echo "- Verify notification to bid invitation conversion\n";
echo "- Confirm Flutter app integration path\n";
echo str_repeat("=", 80) . "\n";
