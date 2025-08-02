<?php
// Simple test script to check notification API endpoints

// Test the notification API endpoint directly
$url = 'http://127.0.0.1:8000/api/worker/notifications';
$token = '138|6KD03MTN063pFIxY5wL2FBSpthVEr3tSCHernxlXca823a80';

$headers = [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
];

echo "Testing notification API endpoint...\n";
echo "URL: $url\n";
echo "Token: $token\n\n";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status Code: $httpCode\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response:\n";
    echo $response . "\n";
}

// Test unread count endpoint
echo "\n" . str_repeat("=", 50) . "\n";
echo "Testing unread count endpoint...\n";

$unreadUrl = 'http://127.0.0.1:8000/api/worker/notifications/unread-count';
echo "URL: $unreadUrl\n";

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $unreadUrl);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch2, CURLOPT_TIMEOUT, 30);

$response2 = curl_exec($ch2);
$httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
$error2 = curl_error($ch2);
curl_close($ch2);

echo "HTTP Status Code: $httpCode2\n";

if ($error2) {
    echo "cURL Error: $error2\n";
} else {
    echo "Response:\n";
    echo $response2 . "\n";
}
?>
