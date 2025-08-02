<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request to the notification API
$request = Request::create('/api/worker/notifications', 'GET', [], [], [], [
    'HTTP_AUTHORIZATION' => 'Bearer 138|6KD03MTN063pFIxY5wL2FBSpthVEr3tSCHernxlX',
    'HTTP_ACCEPT' => 'application/json',
    'HTTP_CONTENT_TYPE' => 'application/json'
]);

try {
    $response = $kernel->handle($request);
    
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Response Headers:\n";
    foreach ($response->headers->all() as $name => $values) {
        echo "  $name: " . implode(', ', $values) . "\n";
    }
    echo "\nResponse Body:\n";
    echo $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Exception occurred:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response ?? null);
