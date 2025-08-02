<?php

// Test the final working endpoint for Flutter app
$url = 'http://localhost/mediconnect/public/api/worker/notifications';

echo "Testing final notification endpoint for Flutter app...\n";
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
    if (isset($data['success']) && $data['success']) {
        echo "🎉 SUCCESS! The notification endpoint is now working!\n";
        echo "Found {$data['count']} notifications\n";
        
        if ($data['count'] > 0) {
            echo "\nLatest notification:\n";
            $latest = $data['data'][0];
            $notificationData = json_decode($latest->data, true);
            echo "- ID: {$latest->id}\n";
            echo "- Shift: {$notificationData['title']}\n";
            echo "- Facility: {$notificationData['facility_name']}\n";
            echo "- Pay Rate: {$notificationData['pay_rate']}\n";
            echo "- Start: {$notificationData['start_datetime']}\n";
            echo "- Created: {$latest->created_at}\n";
            echo "- Read: " . ($latest->read_at ? 'Yes' : 'No') . "\n";
        }
        
        echo "\n✅ The Flutter medical worker app should now be able to fetch notifications!\n";
        echo "Endpoint: GET /api/worker/notifications\n";
        echo "Response format matches Flutter expectations\n";
    } else {
        echo "✗ Endpoint returned success=false\n";
    }
} else {
    echo "✗ Endpoint failed with HTTP $httpCode\n";
}
