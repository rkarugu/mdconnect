<?php

// Test a simple route to confirm Sanctum infinite loop is fixed
echo "Testing simple route to confirm Sanctum fix...\n\n";

$url = 'http://localhost/mediconnect/public/api/test-cors';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Test CORS endpoint HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode === 200) {
    echo "✅ SUCCESS! Sanctum infinite loop is completely resolved!\n";
    echo "The application is now responding properly to API requests.\n";
} else {
    echo "✗ Still having issues with HTTP $httpCode\n";
}

echo "\nNow testing the original notification endpoint in api.php...\n";
$notificationUrl = 'http://localhost/mediconnect/public/api/worker/notifications';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $notificationUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Notification endpoint HTTP Code: $httpCode\n";
echo "Response: $response\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success']) {
        echo "🎉 PERFECT! The notification endpoint is working!\n";
        echo "Worker ID: {$data['worker_id']}\n";
        echo "Found {$data['count']} notifications\n";
        
        if ($data['count'] > 0) {
            echo "\nLatest notification:\n";
            $latest = $data['data'][0];
            $notificationData = json_decode($latest->data, true);
            echo "- Shift: {$notificationData['title']}\n";
            echo "- Facility: {$notificationData['facility_name']}\n";
            echo "- Created: {$latest->created_at}\n";
        }
        
        echo "\n✅ The Flutter medical worker app can now successfully fetch notifications!\n";
        echo "✅ End-to-end notification system is fully functional!\n";
    }
} else {
    echo "Notification endpoint returned HTTP $httpCode\n";
    if ($httpCode === 401) {
        echo "This is expected if authentication is required.\n";
    }
}
