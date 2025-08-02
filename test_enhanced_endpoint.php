<?php

// Test the enhanced notification endpoint with proper token authentication
echo "Testing enhanced notification endpoint with authentication...\n\n";

// First, login to get a token
$loginUrl = 'http://localhost/mediconnect/public/api/medical-worker/login';
$loginData = [
    'email' => 'john.doe@example.com',
    'password' => 'password123'
];

echo "Step 1: Logging in to get authentication token...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login HTTP Code: $loginHttpCode\n";

if ($loginHttpCode === 200) {
    $loginData = json_decode($loginResponse, true);
    if (isset($loginData['token'])) {
        $token = $loginData['token'];
        echo "✅ Login successful! Token obtained.\n\n";
        
        // Now test the notifications endpoint with the token
        echo "Step 2: Fetching notifications with Bearer token...\n";
        $notificationsUrl = 'http://localhost/mediconnect/public/api/worker/notifications';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $notificationsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Notifications HTTP Code: $httpCode\n";
        echo "Response: $response\n\n";
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if (isset($data['success']) && $data['success']) {
                echo "🎉 SUCCESS! Enhanced endpoint working with authentication!\n";
                echo "Worker ID: {$data['worker_id']}\n";
                echo "Found {$data['count']} notifications\n";
                
                if ($data['count'] > 0) {
                    echo "\nLatest notification:\n";
                    $latest = $data['data'][0];
                    $notificationData = json_decode($latest->data, true);
                    echo "- ID: {$latest->id}\n";
                    echo "- Shift: {$notificationData['title']}\n";
                    echo "- Facility: {$notificationData['facility_name']}\n";
                    echo "- Created: {$latest->created_at}\n";
                }
                
                echo "\n✅ The Flutter medical worker app can now authenticate and fetch notifications!\n";
            }
        } else {
            echo "✗ Notifications endpoint failed with HTTP $httpCode\n";
        }
        
    } else {
        echo "✗ Login successful but no token in response\n";
        echo "Response: $loginResponse\n";
    }
} else {
    echo "✗ Login failed with HTTP $loginHttpCode\n";
    echo "Response: $loginResponse\n";
    
    // Test without authentication (fallback)
    echo "\nStep 2b: Testing without authentication (fallback to worker ID 1)...\n";
    $notificationsUrl = 'http://localhost/mediconnect/public/api/worker/notifications';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $notificationsUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode\n";
    echo "Response: $response\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['success']) && $data['success']) {
            echo "✅ Fallback working! Using default worker ID: {$data['worker_id']}\n";
        }
    }
}
