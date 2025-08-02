<?php

// Test the simplified worker notification endpoint
$worker_id = 1; // From the login response we saw earlier
$url = "http://localhost/mediconnect/public/api/worker/{$worker_id}/notifications";

echo "Testing simplified notification endpoint...\n";
echo "URL: $url\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✓ Notifications endpoint working!\n";
    echo "Found {$data['count']} notifications for worker {$data['worker_id']}\n";
    
    if ($data['count'] > 0) {
        echo "\nLatest notifications:\n";
        foreach (array_slice($data['data'], 0, 3) as $notification) {
            echo "- ID: {$notification->id}, Type: {$notification->type}, Created: {$notification->created_at}\n";
        }
    } else {
        echo "No notifications found. This might mean:\n";
        echo "1. The notifications were sent but not stored properly\n";
        echo "2. The worker_id doesn't match\n";
        echo "3. The notification table structure is different\n";
    }
} else {
    echo "✗ Endpoint failed with HTTP $httpCode\n";
}
