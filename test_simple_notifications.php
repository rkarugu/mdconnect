<?php

// Test the simple notification endpoint
$url = 'http://localhost/mediconnect/public/api/test-notifications';

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

if ($httpCode === 200) {
    echo "\n✓ Simple notification endpoint is working!\n";
    echo "Now we can build the real notification functionality on this foundation.\n";
} else {
    echo "\n✗ Still having issues\n";
}
