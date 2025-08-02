<?php

// Debug why Flutter app isn't getting notifications
echo "=== DEBUGGING FLUTTER NOTIFICATION RETRIEVAL ===\n\n";

// Test the exact notification endpoint the Flutter app should be calling
$notificationUrl = 'http://localhost/mediconnect/public/api/worker/notifications';
$bidInvitationsUrl = 'http://localhost/mediconnect/public/api/worker/shifts/bid-invitations';

echo "1. Testing notification endpoint: $notificationUrl\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $notificationUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Notifications available: " . $data['total'] . "\n";
    if ($data['total'] > 0) {
        echo "Sample notification: " . $data['data'][0]['data']['title'] . "\n";
    }
} else {
    echo "❌ Notification endpoint failed: $httpCode\n";
}

echo "\n2. Testing bid invitations endpoint: $bidInvitationsUrl\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $bidInvitationsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Bid invitations available: " . (isset($data['data']) ? count($data['data']) : 0) . "\n";
} else {
    echo "❌ Bid invitations endpoint failed: $httpCode\n";
    echo "Response: " . substr($response, 0, 200) . "\n";
}

echo "\n3. Checking if notifications are being converted to bid invitations...\n";

// Check if our notifications should appear as bid invitations
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $notificationUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
curl_close($ch);

if ($response) {
    $data = json_decode($response, true);
    if (isset($data['data']) && count($data['data']) > 0) {
        echo "Found " . count($data['data']) . " notifications:\n";
        foreach ($data['data'] as $index => $notification) {
            echo "  Notification " . ($index + 1) . ":\n";
            echo "    - Type: " . $notification['type'] . "\n";
            echo "    - Title: " . $notification['data']['title'] . "\n";
            echo "    - Shift ID: " . $notification['data']['shift_id'] . "\n";
            echo "    - Read: " . ($notification['read_at'] ? 'Yes' : 'No') . "\n";
        }
        
        echo "\n🔍 ANALYSIS:\n";
        echo "The notifications exist but Flutter app might be:\n";
        echo "1. Not calling the notification endpoint correctly\n";
        echo "2. Not parsing the response properly\n";
        echo "3. Not displaying them in the UI correctly\n";
        echo "4. Looking for bid invitations in a different endpoint\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "FLUTTER APP DEBUGGING CHECKLIST:\n";
echo "1. ✅ Backend connection working\n";
echo "2. ✅ Dashboard loading successfully\n";
echo "3. ❓ Notification endpoint being called by Flutter\n";
echo "4. ❓ Notification response being parsed correctly\n";
echo "5. ❓ Notifications being displayed in UI\n";
echo "\n🎯 NEXT STEP: Check Flutter app's notification service calls\n";
echo str_repeat("=", 60) . "\n";
