<?php

echo "Testing minimal API approach...\n";

try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Test direct database query for notifications
    $notifications = DB::table('notifications')
        ->where('notifiable_type', 'App\\Models\\MedicalWorker')
        ->where('notifiable_id', 1)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    echo "Found " . $notifications->count() . " notifications via direct query\n";
    
    foreach ($notifications as $notification) {
        echo "- ID: " . $notification->id . ", Type: " . $notification->type . "\n";
    }
    
    // Test token authentication
    $token = '141|UwthHHYLucHjgXfzyr6bqlrQDNl7Uv4tIAu0Er9C19908cd8';
    $accessToken = Laravel\Sanctum\PersonalAccessToken::findToken($token);
    
    if ($accessToken && $accessToken->tokenable) {
        echo "Token authentication successful for: " . $accessToken->tokenable->name . "\n";
    } else {
        echo "Token authentication failed\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
