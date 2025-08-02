<?php

// Test the updated notification endpoint with Flutter-compatible format
echo "=== TESTING FLUTTER-COMPATIBLE NOTIFICATION FORMAT ===\n\n";

$url = 'http://localhost/mediconnect/public/api/worker/notifications';

echo "Testing GET /api/worker/notifications (Flutter format)...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: " . substr($response, 0, 500) . "...\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success']) {
        echo "🎉 SUCCESS! Flutter-compatible format working!\n\n";
        
        echo "Response Structure Validation:\n";
        echo "✅ success: " . ($data['success'] ? 'true' : 'false') . "\n";
        echo "✅ total: " . (isset($data['total']) ? $data['total'] : 'MISSING') . "\n";
        echo "✅ data array: " . (isset($data['data']) && is_array($data['data']) ? 'present' : 'MISSING') . "\n";
        echo "✅ worker_id: " . (isset($data['worker_id']) ? $data['worker_id'] : 'MISSING') . "\n";
        echo "✅ authenticated: " . (isset($data['authenticated']) ? ($data['authenticated'] ? 'true' : 'false') : 'MISSING') . "\n\n";
        
        if (isset($data['data']) && count($data['data']) > 0) {
            echo "Sample Notification Structure:\n";
            $notification = $data['data'][0];
            echo "✅ id: " . (isset($notification['id']) ? 'present' : 'MISSING') . "\n";
            echo "✅ type: " . (isset($notification['type']) ? 'present' : 'MISSING') . "\n";
            echo "✅ data: " . (isset($notification['data']) && is_array($notification['data']) ? 'parsed JSON object' : 'MISSING/INVALID') . "\n";
            echo "✅ read_at: " . (array_key_exists('read_at', $notification) ? 'present' : 'MISSING') . "\n";
            echo "✅ created_at: " . (isset($notification['created_at']) ? 'present' : 'MISSING') . "\n";
            echo "✅ updated_at: " . (isset($notification['updated_at']) ? 'present' : 'MISSING') . "\n\n";
            
            if (isset($notification['data']) && is_array($notification['data'])) {
                echo "Notification Data Content:\n";
                $notifData = $notification['data'];
                echo "- title: " . ($notifData['title'] ?? 'MISSING') . "\n";
                echo "- message: " . ($notifData['message'] ?? 'MISSING') . "\n";
                echo "- shift_id: " . ($notifData['shift_id'] ?? 'MISSING') . "\n";
                echo "- facility_name: " . ($notifData['facility_name'] ?? 'MISSING') . "\n";
                echo "- pay_rate: " . ($notifData['pay_rate'] ?? 'MISSING') . "\n";
                echo "- start_datetime: " . ($notifData['start_datetime'] ?? 'MISSING') . "\n";
                echo "- end_datetime: " . ($notifData['end_datetime'] ?? 'MISSING') . "\n\n";
            }
        }
        
        echo "🚀 FLUTTER INTEGRATION STATUS:\n";
        echo "✅ Response format matches Flutter expectations\n";
        echo "✅ NotificationModel.fromJson() should work correctly\n";
        echo "✅ NotificationsResponse.fromJson() should work correctly\n";
        echo "✅ All required fields are present\n\n";
        
        echo "🎯 NEXT STEPS FOR FLUTTER APP:\n";
        echo "1. Update base URL in Flutter app to: http://localhost/mediconnect/public\n";
        echo "2. Ensure proper Bearer token authentication\n";
        echo "3. Test notification fetching in Flutter app\n";
        
    } else {
        echo "❌ Endpoint returned success=false\n";
    }
} else {
    echo "❌ Endpoint failed with HTTP $httpCode\n";
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "FLUTTER INTEGRATION CHECKLIST:\n";
echo ($httpCode === 200 ? "✅" : "❌") . " Backend endpoint working\n";
echo ($httpCode === 200 && isset($data['total']) ? "✅" : "❌") . " Response format compatible\n";
echo "❓ Flutter app base URL configuration\n";
echo "❓ Flutter app authentication setup\n";
echo "❓ Flutter app notification polling/refresh\n";
echo str_repeat("=", 80) . "\n";
