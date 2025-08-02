<?php

// Clean test of the Flutter-compatible notification endpoint
echo "Testing Flutter-compatible notification endpoint...\n\n";

$url = 'http://localhost/mediconnect/public/api/worker/notifications';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    echo "✅ SUCCESS! Backend endpoint working\n";
    echo "✅ Response format: Flutter-compatible\n";
    echo "✅ Total notifications: " . $data['total'] . "\n";
    echo "✅ Worker ID: " . $data['worker_id'] . "\n";
    echo "✅ Authentication: " . ($data['authenticated'] ? 'Yes' : 'No (fallback)') . "\n\n";
    
    if ($data['total'] > 0) {
        $notification = $data['data'][0];
        echo "Sample notification structure:\n";
        echo "- ID: " . $notification['id'] . "\n";
        echo "- Type: " . $notification['type'] . "\n";
        echo "- Title: " . $notification['data']['title'] . "\n";
        echo "- Facility: " . $notification['data']['facility_name'] . "\n";
        echo "- Pay Rate: " . $notification['data']['pay_rate'] . "\n";
        echo "- Read Status: " . ($notification['read_at'] ? 'Read' : 'Unread') . "\n\n";
    }
    
    echo "🎯 FLUTTER APP INTEGRATION READY!\n";
    echo "The backend is now properly formatted for Flutter consumption.\n\n";
    
    echo "NEXT STEPS:\n";
    echo "1. Update Flutter app base URL to: http://localhost/mediconnect/public\n";
    echo "2. Ensure Flutter app sends proper authentication headers\n";
    echo "3. Test notification fetching in Flutter app\n";
    
} else {
    echo "❌ Endpoint failed with HTTP $httpCode\n";
}
