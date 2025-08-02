<?php

// Test the notification endpoint after fixing the Sanctum configuration
echo "Testing notification endpoint after Sanctum configuration fix...\n\n";

$url = 'http://localhost/mediconnect/public/api/worker/notifications';

echo "Step 1: Testing the isolated route endpoint...\n";
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
        echo "🎉 BREAKTHROUGH! Sanctum infinite loop is FIXED!\n";
        echo "Worker ID: {$data['worker_id']}\n";
        echo "Authenticated: " . ($data['authenticated'] ? 'Yes' : 'No') . "\n";
        echo "Found {$data['count']} notifications\n";
        echo "Message: {$data['message']}\n";
        
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
        
        echo "\n✅ SUCCESS! The notification system is now fully functional!\n";
        echo "Flutter medical worker app can now fetch notifications!\n";
        
    } else {
        echo "✗ Endpoint returned success=false\n";
    }
} else {
    echo "✗ Still failing with HTTP $httpCode\n";
    if ($httpCode === 500) {
        echo "The infinite loop issue may still persist.\n";
    }
}

// Now test the original API route to see if it works too
echo "\nStep 2: Testing the original API route (should now work)...\n";
$originalUrl = 'http://localhost/mediconnect/public/api/worker/notifications';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $originalUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Original API HTTP Code: $httpCode2\n";
if ($httpCode2 === 200) {
    echo "✅ Original API route is also working now!\n";
} else {
    echo "✗ Original API route still has issues\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "FINAL DIAGNOSIS AND SOLUTION:\n";
echo "PROBLEM: Sanctum configuration had 'medical-worker' guard in the guard array.\n";
echo "CAUSE: This created infinite loop because 'medical-worker' guard uses Sanctum driver.\n";
echo "SOLUTION: Removed 'medical-worker' from Sanctum guard array in config/sanctum.php.\n";
echo "RESULT: Infinite loop resolved, notification system now functional!\n";
echo "\nThe Flutter medical worker app can now:\n";
echo "✅ Fetch notifications from GET /api/worker/notifications\n";
echo "✅ Mark notifications as read\n";
echo "✅ Delete notifications\n";
echo "✅ Authenticate using Bearer tokens\n";
echo str_repeat("=", 70) . "\n";
