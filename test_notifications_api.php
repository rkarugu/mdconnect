<?php

// Test script to check if the notification API endpoints are working
require_once 'vendor/autoload.php';

$baseUrl = 'http://localhost/mediconnect/public';

// Test 1: Try to get notifications without authentication (should fail)
echo "=== Test 1: GET /api/worker/notifications (no auth) ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/worker/notifications');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

// Test 2: Check if we can get a medical worker token first
echo "=== Test 2: Login as medical worker ===\n";
$loginData = [
    'email' => 'worker@example.com', // You may need to adjust this
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/medical-worker/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);
$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login HTTP Code: $loginHttpCode\n";
echo "Login Response: $loginResponse\n\n";

$loginData = json_decode($loginResponse, true);
if ($loginHttpCode === 200 && isset($loginData['token'])) {
    $token = $loginData['token'];
    
    // Test 3: Get notifications with authentication
    echo "=== Test 3: GET /api/worker/notifications (with auth) ===\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/worker/notifications');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP Code: $httpCode\n";
    echo "Response: $response\n\n";
} else {
    echo "Could not get authentication token. Please check medical worker credentials.\n";
}

echo "=== Test Complete ===\n";
