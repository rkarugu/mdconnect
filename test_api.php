<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\MedicalWorker;
use App\Http\Controllers\Api\MedicalWorkerDashboardController;
use Illuminate\Http\Request;

echo "=== API Test ===\n\n";

try {
    // Get a medical worker
    $worker = MedicalWorker::find(1);
    if (!$worker) {
        echo "❌ No medical worker found\n";
        exit;
    }
    
    echo "✅ Found worker: {$worker->email}\n";
    
    // Simulate authentication by manually setting the user
    Auth::guard('medical-worker')->setUser($worker);
    echo "✅ Worker authenticated\n";
    
    // Create controller and request
    $controller = new MedicalWorkerDashboardController();
    $request = new Request();
    
    // Call the bidInvitations method
    echo "🔍 Calling bidInvitations API...\n";
    $response = $controller->bidInvitations($request);
    $data = $response->getData(true);
    
    echo "📊 API Response:\n";
    echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    echo "Count: " . $data['count'] . "\n";
    echo "Data items: " . count($data['data']) . "\n\n";
    
    if (!empty($data['data'])) {
        echo "📋 Bid Invitations:\n";
        foreach ($data['data'] as $invitation) {
            echo "  - ID: {$invitation['invitationId']}\n";
            echo "    Title: {$invitation['title']}\n";
            echo "    Facility: {$invitation['facility']}\n";
            echo "    Shift Time: {$invitation['shiftTime']}\n";
            echo "    Status: {$invitation['status']}\n";
            echo "    ---\n";
        }
    } else {
        echo "✅ No bid invitations returned (expired shifts filtered out)\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n🔍 Test complete.\n";
