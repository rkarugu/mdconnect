<?php

echo "=== TESTING SUCCESS ROUTE - FINAL SOLUTION ===\n\n";

$testUrl = 'http://localhost/mediconnect/public/api/worker/dashboard-success';

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

echo "Testing URL: $testUrl\n";
echo "HTTP Status: $httpCode\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    echo "🎉 SUCCESS! Route is working!\n\n";
    
    if (isset($data['debug'])) {
        echo "📊 DEBUG INFO:\n";
        echo "- Notifications found: " . ($data['debug']['notifications_found'] ?? 0) . "\n";
        echo "- Bid invitations created: " . ($data['debug']['bid_invitations_created'] ?? 0) . "\n\n";
    }
    
    if (isset($data['data']['bidInvitations']) && count($data['data']['bidInvitations']) > 0) {
        echo "🎯 BID INVITATIONS FOUND:\n";
        foreach (array_slice($data['data']['bidInvitations'], 0, 5) as $index => $invitation) {
            echo "   " . ($index + 1) . ". " . ($invitation['title'] ?? 'Unknown') . "\n";
            echo "      - Facility: " . ($invitation['facility'] ?? 'Unknown') . "\n";
            echo "      - Pay: KES " . number_format($invitation['minimumBid'] ?? 0) . "\n";
            echo "      - Time: " . ($invitation['shiftTime'] ?? 'TBD') . "\n\n";
        }
        
        echo "🎉🎉🎉 MISSION ACCOMPLISHED! 🎉🎉🎉\n";
        echo "✅ Notifications are successfully converting to bid invitations!\n";
        echo "✅ Dashboard endpoint is working perfectly!\n";
        echo "✅ Flutter app can now receive bid invitations!\n\n";
        
        echo "🚀 NEXT STEP: Update Flutter app to use this endpoint:\n";
        echo "   URL: $testUrl\n\n";
        
        echo "📱 Flutter Integration:\n";
        echo "   1. Update DashboardRepository to call: $testUrl\n";
        echo "   2. Parse the 'bidInvitations' array from response\n";
        echo "   3. Display notifications as bid invitations in the app\n\n";
        
    } else {
        echo "⚠️  No bid invitations found\n";
        echo "   This could mean:\n";
        echo "   - No unread notifications exist\n";
        echo "   - Notifications don't have shift_id data\n";
        echo "   - Worker ID 1 has no notifications\n\n";
    }
    
    echo "📋 FULL RESPONSE:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
    
} else {
    echo "❌ Route failed - HTTP $httpCode\n";
    echo "Response: $response\n\n";
}

echo str_repeat("=", 80) . "\n";
echo "FINAL TEST COMPLETE\n";
echo str_repeat("=", 80) . "\n";
