<?php

// Test the worker notification endpoint with authentication
$baseUrl = 'http://localhost/mediconnect/public';

// First, login as the medical worker
$loginData = [
    'email' => 'ayden@uptownnvintage.com',
    'password' => 'password'
];

echo "=== Step 1: Login as medical worker ===\n";
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
if ($loginHttpCode === 200 && isset($loginData['data']['token'])) {
    $token = $loginData['data']['token'];
    
    echo "=== Step 2: Get notifications with token ===\n";
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

    echo "Notifications HTTP Code: $httpCode\n";
    echo "Notifications Response: $response\n\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "✓ Notifications endpoint working!\n";
        echo "Found {$data['count']} notifications\n";
        if ($data['count'] > 0) {
            echo "Latest notification: " . json_encode($data['data'][0]) . "\n";
        }
    } else {
        echo "✗ Notifications endpoint failed\n";
    }
} else {
    echo "✗ Could not login\n";
}
