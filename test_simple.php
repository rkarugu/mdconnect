<?php

echo "Testing notification API fix...\n";

// Test database connection
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Test if we can get a medical worker
    $worker = App\Models\MedicalWorker::first();
    if ($worker) {
        echo "Worker found: " . $worker->name . "\n";
        echo "Worker ID: " . $worker->id . "\n";
        
        // Test notifications count
        $count = $worker->notifications()->count();
        echo "Notifications count: " . $count . "\n";
        
        // Test unread notifications count
        $unreadCount = $worker->unreadNotifications()->count();
        echo "Unread notifications count: " . $unreadCount . "\n";
        
    } else {
        echo "No medical workers found in database\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
