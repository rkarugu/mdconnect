<?php

echo "=== TESTING BACKEND DASHBOARD RESPONSE ===\n\n";

$url = 'http://localhost/mediconnect/public/api/worker/dashboard';

echo "Testing: $url\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    echo "✅ Dashboard Response Structure:\n";
    echo "- upcoming_shifts: " . count($data['upcoming_shifts']) . "\n";
    echo "- instant_requests: " . count($data['instant_requests']) . "\n";
    echo "- bid_invitations: " . count($data['bid_invitations']) . "\n";
    echo "- shift_history: " . count($data['shift_history']) . "\n\n";
    
    echo "🔍 ISSUE IDENTIFIED:\n";
    echo "The dashboard returns empty bid_invitations but we have 10 notifications!\n";
    echo "The notifications need to be converted to bid_invitations in the dashboard endpoint.\n\n";
    
} else {
    echo "❌ Dashboard endpoint failed: $httpCode\n";
    echo "Response: $response\n";
}

echo "🎯 SOLUTION NEEDED:\n";
echo "Update the backend dashboard endpoint to include notifications as bid_invitations\n";
echo "so the Flutter app can display them properly.\n";
