<?php
// Simple test for notification API endpoints

require_once 'vendor/autoload.php';

// Test with a simple HTTP client approach
echo "Testing Notification API Endpoints...\n\n";

// Test 1: Check if Laravel is running
echo "1. Testing Laravel server status...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8000/api/worker/notifications');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Status: $httpCode\n";
if ($response) {
    echo "   Response: " . substr($response, 0, 200) . "...\n";
} else {
    echo "   No response received\n";
}

// Test 2: Check route configuration
echo "\n2. Checking route configuration...\n";
exec('php artisan route:list --path=worker/notifications', $output, $returnCode);
if ($returnCode === 0) {
    echo "   Routes found:\n";
    foreach ($output as $line) {
        echo "   $line\n";
    }
} else {
    echo "   Route check failed\n";
}

// Test 3: Check auth configuration
echo "\n3. Checking auth guards...\n";
$guards = include 'config/auth.php';
echo "   Medical-worker guard: " . ($guards['guards']['medical-worker']['driver'] ?? 'NOT FOUND') . "\n";
echo "   Medical-worker provider: " . ($guards['providers']['medical-workers']['driver'] ?? 'NOT FOUND') . "\n";

echo "\nTest completed.\n";
