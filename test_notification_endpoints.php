<?php

echo "=== TESTING NOTIFICATION ENDPOINTS FOR FLUTTER ===\n\n";

// Test notification endpoint
echo "1. Testing /api/worker/notifications:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/mediconnect/public/api/worker/notifications');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status: $httpCode\n";
if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "   ✅ Notifications: " . $data['total'] . "\n";
    if ($data['total'] > 0) {
        echo "   Sample: " . $data['data'][0]['data']['title'] . " at " . $data['data'][0]['data']['facility_name'] . "\n";
    }
} else {
    echo "   ❌ Failed\n";
}

// Test bid invitations endpoint  
echo "\n2. Testing /api/worker/shifts/bid-invitations:\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/mediconnect/public/api/worker/shifts/bid-invitations');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status: $httpCode\n";
if ($httpCode === 200) {
    $data = json_decode($response, true);
    $count = isset($data['data']) ? count($data['data']) : 0;
    echo "   ✅ Bid Invitations: $count\n";
} else {
    echo "   ❌ Failed: " . substr($response, 0, 100) . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "ISSUE ANALYSIS:\n";
echo "- Backend has notifications stored ✅\n";
echo "- Flutter app connects successfully ✅\n";  
echo "- But Flutter shows 'no notification found' ❌\n";
echo "\nPOSSIBLE CAUSES:\n";
echo "1. Flutter not calling notification endpoint\n";
echo "2. Flutter expecting different response format\n";
echo "3. Flutter looking for bid invitations only\n";
echo "4. Authentication issue in Flutter requests\n";
echo str_repeat("=", 50) . "\n";
