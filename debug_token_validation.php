<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\MedicalWorker;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;

echo "=== Deep Token Validation Debug ===\n\n";

// Get the medical worker and their latest token
$worker = MedicalWorker::first();
$latestToken = $worker->tokens()->latest()->first();

echo "Worker: {$worker->email}\n";
echo "Token ID: {$latestToken->id}\n";
echo "Token Name: {$latestToken->name}\n";
echo "Tokenable Type: {$latestToken->tokenable_type}\n";
echo "Tokenable ID: {$latestToken->tokenable_id}\n\n";

// Check the actual token structure
echo "=== Token Structure ===\n";
echo "Raw token from DB: " . substr($latestToken->token, 0, 20) . "...\n";
echo "Token abilities: " . json_encode($latestToken->abilities) . "\n";
echo "Token expires at: " . ($latestToken->expires_at ?? 'Never') . "\n\n";

// Test token parsing like Sanctum does
echo "=== Testing Token Parsing ===\n";

// Create a proper test token
$newToken = $worker->createToken('debug_token');
$plainTextToken = $newToken->plainTextToken;
echo "New plain text token: {$plainTextToken}\n";

// Parse the token like Sanctum does
if (Str::contains($plainTextToken, '|')) {
    [$id, $token] = explode('|', $plainTextToken, 2);
    echo "Parsed ID: {$id}\n";
    echo "Parsed token: " . substr($token, 0, 20) . "...\n";
    
    // Find the token in database
    $accessToken = PersonalAccessToken::find($id);
    if ($accessToken) {
        echo "✅ Token found in database\n";
        echo "DB Token hash: " . substr($accessToken->token, 0, 20) . "...\n";
        
        // Verify hash
        $hashedToken = hash('sha256', $token);
        echo "Generated hash: " . substr($hashedToken, 0, 20) . "...\n";
        
        if (hash_equals($accessToken->token, $hashedToken)) {
            echo "✅ Token hash matches!\n";
            
            // Check tokenable
            if ($accessToken->tokenable_type === 'App\\Models\\MedicalWorker' && 
                $accessToken->tokenable_id == $worker->id) {
                echo "✅ Tokenable matches!\n";
                
                // Check abilities
                if (empty($accessToken->abilities) || in_array('*', $accessToken->abilities)) {
                    echo "✅ Token has required abilities!\n";
                    echo "🎉 Token should work perfectly!\n";
                } else {
                    echo "❌ Token abilities issue: " . json_encode($accessToken->abilities) . "\n";
                }
            } else {
                echo "❌ Tokenable mismatch!\n";
                echo "Expected: App\\Models\\MedicalWorker#{$worker->id}\n";
                echo "Got: {$accessToken->tokenable_type}#{$accessToken->tokenable_id}\n";
            }
        } else {
            echo "❌ Token hash mismatch!\n";
        }
    } else {
        echo "❌ Token not found in database with ID: {$id}\n";
    }
} else {
    echo "❌ Invalid token format (no | separator)\n";
}

echo "\n=== Checking Auth Configuration ===\n";

// Check auth guards
$authConfig = config('auth.guards');
echo "Available guards: " . implode(', ', array_keys($authConfig)) . "\n";
echo "Medical-worker guard config: " . json_encode($authConfig['medical-worker']) . "\n";

// Check sanctum config
$sanctumConfig = config('sanctum');
echo "Sanctum guards: " . json_encode($sanctumConfig['guard']) . "\n";

echo "\n=== Test Complete ===\n";
