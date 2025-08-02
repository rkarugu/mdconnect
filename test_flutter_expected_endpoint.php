<?php

// Test the exact endpoint that the Flutter app expects
$baseUrl = 'http://localhost/mediconnect/public';

// First, login as the medical worker to get a token
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

$loginData = json_decode($loginResponse, true);
if ($loginHttpCode === 200 && isset($loginData['data']['token'])) {
    $token = $loginData['data']['token'];
    echo "✓ Login successful, token obtained\n\n";
    
    echo "=== Step 2: Test Flutter expected endpoint ===\n";
    echo "Calling: /api/worker/notifications\n";
    
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
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['success']) && $data['success']) {
            echo "✅ SUCCESS! Flutter app endpoint is working!\n";
            echo "Found {$data['count']} notifications\n";
            echo "Response format matches Flutter expectations\n";
            
            if ($data['count'] > 0) {
                echo "\nLatest notification data:\n";
                $latest = $data['data'][0];
                $notificationData = json_decode($latest->data, true);
                echo "- Shift: {$notificationData['title']}\n";
                echo "- Facility: {$notificationData['facility_name']}\n";
                echo "- Pay: {$notificationData['pay_rate']}\n";
                echo "- Created: {$latest->created_at}\n";
            }
        } else {
            echo "✗ Endpoint returned success=false\n";
        }
    } else {
        echo "✗ Endpoint failed with HTTP $httpCode\n";
    }
} else {
    echo "✗ Could not login\n";
}
