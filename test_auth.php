<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\MedicalWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== Testing Medical Worker Authentication ===\n\n";

// Get the medical worker
$worker = MedicalWorker::first();
if (!$worker) {
    echo "No medical worker found!\n";
    exit(1);
}

echo "Testing worker: {$worker->email}\n";
echo "Status: {$worker->status}\n";
echo "Approved: " . ($worker->isApproved() ? 'Yes' : 'No') . "\n\n";

// Get the latest token
$latestToken = $worker->tokens()->latest()->first();
if (!$latestToken) {
    echo "No tokens found! Creating new token...\n";
    $token = $worker->createToken('test_token');
    $tokenString = $token->plainTextToken;
    echo "New token created: {$tokenString}\n\n";
} else {
    echo "Latest token ID: {$latestToken->id}\n";
    echo "Token name: {$latestToken->name}\n";
    echo "Created: {$latestToken->created_at}\n";
    echo "Last used: " . ($latestToken->last_used_at ?? 'Never') . "\n\n";
    
    // Get the full token string (this is just for testing - normally you'd have this from login)
    $tokenString = $latestToken->id . '|' . hash('sha256', $latestToken->token);
    echo "Testing with token: {$latestToken->id}|[hash]\n\n";
}

// Test authentication manually
echo "=== Testing Token Authentication ===\n";

// Simulate the request that would come from Flutter
$request = Request::create('/api/worker/dashboard', 'GET');
$request->headers->set('Authorization', 'Bearer ' . ($tokenString ?? 'no-token'));
$request->headers->set('Accept', 'application/json');
$request->headers->set('Content-Type', 'application/json');

// Set the request for Laravel
app()->instance('request', $request);

try {
    // Test the medical-worker guard
    $authenticatedUser = Auth::guard('medical-worker')->user();
    
    if ($authenticatedUser) {
        echo "✅ Authentication SUCCESSFUL!\n";
        echo "Authenticated as: {$authenticatedUser->email}\n";
        echo "Worker ID: {$authenticatedUser->id}\n";
    } else {
        echo "❌ Authentication FAILED!\n";
        echo "Guard 'medical-worker' could not authenticate the token.\n";
    }
} catch (Exception $e) {
    echo "❌ Authentication ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
