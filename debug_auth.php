<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MedicalWorker;

echo "=== Medical Worker Authentication Debug ===\n\n";

// Check all medical workers
$workers = MedicalWorker::all(['id', 'email', 'status', 'created_at']);
echo "Total Medical Workers: " . $workers->count() . "\n\n";

foreach ($workers as $worker) {
    echo "ID: {$worker->id}\n";
    echo "Email: {$worker->email}\n";
    echo "Status: {$worker->status}\n";
    echo "Approved: " . ($worker->isApproved() ? 'Yes' : 'No') . "\n";
    echo "Active Tokens: " . $worker->tokens()->count() . "\n";
    echo "Created: {$worker->created_at}\n";
    echo "---\n";
}

// Check recent tokens
echo "\n=== Recent Tokens ===\n";
$recentTokens = \Laravel\Sanctum\PersonalAccessToken::where('tokenable_type', 'App\Models\MedicalWorker')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get(['id', 'tokenable_id', 'name', 'created_at', 'last_used_at']);

foreach ($recentTokens as $token) {
    echo "Token ID: {$token->id}\n";
    echo "Worker ID: {$token->tokenable_id}\n";
    echo "Name: {$token->name}\n";
    echo "Created: {$token->created_at}\n";
    echo "Last Used: " . ($token->last_used_at ?? 'Never') . "\n";
    echo "---\n";
}
