<?php

echo "Testing notification retrieval directly...\n";

try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Get the medical worker with the token
    $token = '138|6KD03MTN063pFIxY5wL2FBSpthVEr3tSCHernxlX';
    
    // Find the token in personal_access_tokens table
    $accessToken = Laravel\Sanctum\PersonalAccessToken::findToken($token);
    
    if ($accessToken) {
        echo "Token found for tokenable_type: " . $accessToken->tokenable_type . "\n";
        echo "Token found for tokenable_id: " . $accessToken->tokenable_id . "\n";
        
        $worker = $accessToken->tokenable;
        
        if ($worker) {
            echo "Worker authenticated: " . $worker->name . "\n";
            
            // Test direct notification query without pagination
            echo "Testing direct notifications query...\n";
            $notifications = $worker->notifications()->limit(5)->get();
            echo "Found " . $notifications->count() . " notifications\n";
            
            foreach ($notifications as $notification) {
                echo "- Notification ID: " . $notification->id . "\n";
                echo "  Type: " . $notification->type . "\n";
                echo "  Created: " . $notification->created_at . "\n";
            }
            
        } else {
            echo "No worker found for token\n";
        }
    } else {
        echo "Token not found in database\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
