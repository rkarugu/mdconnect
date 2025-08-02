<?php

echo "=== DEBUGGING ROUTE EXECUTION ===\n\n";

// Test if our route is actually being hit by checking for the log message
echo "1. Testing dashboard route execution...\n";

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

echo "   ✅ Authentication successful\n";

// Clear recent logs to see new entries
file_put_contents('storage/logs/laravel.log', '', LOCK_EX);

echo "2. Calling dashboard endpoint...\n";

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

echo "   HTTP Status: $httpCode\n";

// Check if our log message appears
sleep(1); // Give time for log to be written
$logContent = file_get_contents('storage/logs/laravel.log');

echo "3. Checking for route execution log...\n";

if (strpos($logContent, 'Dashboard with notifications') !== false) {
    echo "   ✅ Our route is being executed!\n";
    
    // Parse the response
    $data = json_decode($response, true);
    if (isset($data['data']['bid_invitations'])) {
        $bidCount = count($data['data']['bid_invitations']);
        echo "   📊 Bid invitations returned: $bidCount\n";
        
        if ($bidCount > 0) {
            echo "   🎉 SUCCESS! Notifications converted to bid invitations!\n";
            echo "   Sample: " . $data['data']['bid_invitations'][0]['title'] . "\n";
        } else {
            echo "   ❌ Route executed but no bid invitations found\n";
            echo "   This means the notification query is not finding data\n";
        }
    }
    
} else {
    echo "   ❌ Our route is NOT being executed\n";
    echo "   The dashboard call is hitting a different route\n";
    echo "   Log content: " . (empty($logContent) ? 'EMPTY' : substr($logContent, 0, 200)) . "\n";
}

echo "\n4. Response analysis:\n";
$data = json_decode($response, true);
if (isset($data['data']['worker']['name'])) {
    echo "   Worker name: " . $data['data']['worker']['name'] . "\n";
    if ($data['data']['worker']['name'] === 'Test Worker') {
        echo "   ❌ This is the temporary route response (Test Worker)\n";
        echo "   Our updated route is not being used\n";
    } elseif ($data['data']['worker']['name'] === 'Medical Worker') {
        echo "   ✅ This looks like our updated route response\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "ROUTE DEBUG SUMMARY:\n";
echo "- Check if our route code is actually executing\n";
echo "- Verify log messages appear when route is called\n";
echo "- Identify which route is actually being hit\n";
echo "- Debug notification query if route is working\n";
echo str_repeat("=", 60) . "\n";
