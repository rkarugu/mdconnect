<?php

echo "=== DEBUGGING LOGIN RESPONSE STRUCTURE ===\n\n";

$loginUrl = 'http://localhost/mediconnect/public/api/medical-worker/login';
$loginData = [
    'email' => 'ayden@uptownnvintage.com',
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Full Response:\n";
echo $response . "\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "Parsed JSON Structure:\n";
    echo "- success: " . ($data['success'] ? 'true' : 'false') . "\n";
    echo "- message: " . $data['message'] . "\n";
    
    if (isset($data['data'])) {
        echo "- data structure:\n";
        foreach ($data['data'] as $key => $value) {
            if ($key === 'token') {
                echo "  - token: " . substr($value, 0, 20) . "... (found!)\n";
            } elseif ($key === 'medical_worker') {
                echo "  - medical_worker: [object with id: " . $value['id'] . "]\n";
            } else {
                echo "  - $key: " . (is_array($value) ? '[array]' : $value) . "\n";
            }
        }
    }
    
    // Check different possible token locations
    $possibleTokens = [
        'token' => $data['token'] ?? null,
        'data.token' => $data['data']['token'] ?? null,
        'data.access_token' => $data['data']['access_token'] ?? null,
        'access_token' => $data['access_token'] ?? null,
        'auth_token' => $data['auth_token'] ?? null,
    ];
    
    echo "\nToken Location Check:\n";
    foreach ($possibleTokens as $location => $token) {
        if ($token) {
            echo "✅ Found token at: $location\n";
            echo "   Token preview: " . substr($token, 0, 30) . "...\n";
            
            // Test this token with dashboard
            echo "\nTesting dashboard with token from $location...\n";
            
            $dashboardUrl = 'http://localhost/mediconnect/public/api/worker/dashboard';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $dashboardUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]);
            $dashboardResponse = curl_exec($ch);
            $dashboardHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            echo "Dashboard HTTP Status: $dashboardHttpCode\n";
            
            if ($dashboardHttpCode === 200) {
                $dashboardData = json_decode($dashboardResponse, true);
                if (isset($dashboardData['data']['bid_invitations'])) {
                    echo "✅ Dashboard access successful!\n";
                    echo "Bid Invitations: " . count($dashboardData['data']['bid_invitations']) . "\n";
                    
                    if (count($dashboardData['data']['bid_invitations']) > 0) {
                        echo "🎉 NOTIFICATIONS CONVERTED TO BID INVITATIONS! 🎉\n";
                        echo "Sample: " . $dashboardData['data']['bid_invitations'][0]['title'] . "\n";
                    } else {
                        echo "❌ No bid invitations found\n";
                    }
                } else {
                    echo "❌ Dashboard response missing bid_invitations\n";
                }
            } else {
                echo "❌ Dashboard access failed: $dashboardHttpCode\n";
            }
            
            break; // Use the first token we find
        } else {
            echo "❌ No token at: $location\n";
        }
    }
    
} else {
    echo "❌ Login failed with HTTP $httpCode\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "LOGIN DEBUG SUMMARY:\n";
echo "- Find correct token location in login response\n";
echo "- Test dashboard access with proper authentication\n";
echo "- Verify notification conversion to bid invitations\n";
echo str_repeat("=", 60) . "\n";
