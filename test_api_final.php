<?php

echo "Final API Test - Testing notification endpoints...\n";

$token = '141|UwthHHYLucHjgXfzyr6bqlrQDNl7Uv4tIAu0Er9C19908cd8';
$baseUrl = 'http://127.0.0.1:8000';

// Test notifications endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/worker/notifications');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "=== Notifications Endpoint Test ===\n";
echo "HTTP Status: " . $httpCode . "\n";
if ($error) {
    echo "cURL Error: " . $error . "\n";
}
echo "Response: " . $response . "\n\n";

// Test unread count endpoint
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/worker/notifications/unread-count');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error2 = curl_error($ch);
curl_close($ch);

echo "=== Unread Count Endpoint Test ===\n";
echo "HTTP Status: " . $httpCode2 . "\n";
if ($error2) {
    echo "cURL Error: " . $error2 . "\n";
}
echo "Response: " . $response2 . "\n\n";

// Summary
if ($httpCode == 200 && $httpCode2 == 200) {
    echo "✅ SUCCESS: Both notification API endpoints are working!\n";
    echo "Frontend integration should now be able to retrieve notifications.\n";
} else {
    echo "❌ ISSUE: One or both endpoints still have issues.\n";
    echo "Check Laravel logs for detailed error information.\n";
}
