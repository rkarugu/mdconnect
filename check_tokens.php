<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking personal access tokens...\n";

// Check existing tokens
$tokens = Laravel\Sanctum\PersonalAccessToken::all();
echo "Found " . $tokens->count() . " tokens\n";

foreach($tokens as $token) {
    echo "Token ID: " . $token->id . ", Tokenable: " . $token->tokenable_type . " #" . $token->tokenable_id . ", Name: " . $token->name . "\n";
}

// Get first medical worker and create a new token
$worker = App\Models\MedicalWorker::first();
if ($worker) {
    echo "\nCreating new token for worker: " . $worker->name . "\n";
    $token = $worker->createToken('test-token');
    echo "New token created: " . $token->plainTextToken . "\n";
} else {
    echo "\nNo medical workers found\n";
}
