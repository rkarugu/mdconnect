<?php
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\MedicalWorker;
use Illuminate\Http\Request;

echo "=== Testing API Endpoint Directly ===\n\n";

// Get the medical worker and create a fresh token
$worker = MedicalWorker::first();
if (!$worker) {
    echo "No medical worker found!\n";
    exit(1);
}

echo "Worker: {$worker->email}\n";
echo "Status: {$worker->status}\n\n";

// Create a fresh token
$token = $worker->createToken('test_api_token');
$tokenString = $token->plainTextToken;
echo "Created fresh token: {$tokenString}\n\n";

// Test the dashboard endpoint directly
echo "=== Testing Dashboard Controller ===\n";

try {
    // Create a request instance
    $request = Request::create('/api/worker/dashboard', 'GET');
    $request->headers->set('Authorization', 'Bearer ' . $tokenString);
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('Content-Type', 'application/json');
    
    // Set the request in the app
    app()->instance('request', $request);
    
    // Create the controller and call the method
    $controller = new \App\Http\Controllers\Api\MedicalWorkerDashboardController();
    $response = $controller->index($request);
    
    echo "✅ Controller Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content:\n";
    echo $response->getContent() . "\n\n";
    
    if ($response->getStatusCode() === 200) {
        echo "🎉 Dashboard endpoint is working correctly!\n";
    } else {
        echo "❌ Dashboard endpoint returned error status\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error testing dashboard: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Test Complete ===\n";
