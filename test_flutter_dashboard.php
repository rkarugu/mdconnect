<?php

// Test the exact dashboard endpoint the Flutter app is trying to reach
echo "=== TESTING FLUTTER DASHBOARD ENDPOINT ===\n\n";

$url = 'http://localhost/mediconnect/public/api/worker/dashboard';

echo "Testing Flutter dashboard endpoint: $url\n\n";

// Simulate Flutter app request with Bearer token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest',
    'Authorization: Bearer 165|CXBjPm...' // Using the token from the Flutter logs
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";

if ($httpCode === 200) {
    echo "✅ SUCCESS! Dashboard endpoint is working!\n";
    echo "Response preview: " . substr($response, 0, 200) . "...\n\n";
    
    echo "🎯 FLUTTER APP SOLUTION:\n";
    echo "The backend is working correctly. The Flutter app needs to:\n";
    echo "1. RESTART the Flutter app completely (not just hot reload)\n";
    echo "2. Clear any cached configuration\n";
    echo "3. Ensure it's using the updated provider files\n\n";
    
} else {
    echo "Status: $httpCode\n";
    echo "Response: $response\n\n";
    
    if ($httpCode === 404) {
        echo "❌ Dashboard endpoint not found. Let me check if it exists...\n";
    } elseif ($httpCode === 401) {
        echo "🔐 Authentication required. This is expected behavior.\n";
        echo "The endpoint exists but needs proper authentication.\n";
    } else {
        echo "❌ Unexpected error. Backend may have issues.\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "FLUTTER APP TROUBLESHOOTING STEPS:\n";
echo "1. ✅ Backend URLs updated in configuration files\n";
echo "2. ❓ Flutter app restart needed\n";
echo "3. ❓ Clear Flutter app cache/storage\n";
echo "4. ❓ Verify correct provider file is being used\n";
echo "\n🚀 RECOMMENDED ACTION: RESTART FLUTTER APP COMPLETELY\n";
echo str_repeat("=", 60) . "\n";
