<?php

// Simple test to check notification API endpoint
$url = 'http://localhost/mediconnect/public/api/worker/notifications';

// Test without auth first
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "URL: $url\n";
echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 401) {
    echo "\n✓ API endpoint exists and requires authentication (expected)\n";
} elseif ($httpCode === 404) {
    echo "\n✗ API endpoint not found - route issue\n";
} elseif ($httpCode === 500) {
    echo "\n✗ Server error - check controller\n";
} else {
    echo "\n? Unexpected response code\n";
}
