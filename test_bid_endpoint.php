<?php

require_once 'vendor/autoload.php';

echo "🔍 Testing Bid Invitation Endpoint\n";
echo "===================================\n\n";

// Test the bid invitation endpoint directly
try {
    // Create test data if it doesn't exist
    $worker = \App\Models\MedicalWorker::first();
    if (!$worker) {
        echo "❌ No medical worker found. Creating test worker...\n";
        $user = \App\Models\User::create([
            'name' => 'Test Worker',
            'email' => 'test@worker.com',
            'password' => bcrypt('password'),
            'role' => 'medical_worker',
        ]);
        $worker = \App\Models\MedicalWorker::create([
            'user_id' => $user->id,
            'medical_specialty_id' => 1,
            'license_number' => 'TEST123',
            'phone' => '1234567890',
        ]);
    }
    
    $facility = \App\Models\MedicalFacility::first();
    if (!$facility) {
        echo "❌ No facility found. Creating test facility...\n";
        $facility = \App\Models\MedicalFacility::create([
            'name' => 'Test Hospital',
            'email' => 'test@hospital.com',
            'phone' => '1234567890',
            'address' => '123 Test St',
        ]);
    }

    // Create test shift
    $shift = \App\Models\LocumShift::create([
        'title' => 'Test Shift - ' . now()->format('Y-m-d H:i'),
        'facility_id' => $facility->id,
        'start_datetime' => now()->addDay(),
        'end_datetime' => now()->addDay()->addHours(8),
        'pay_rate' => 50.00,
        'status' => 'open',
        'description' => 'Test shift for debugging',
        'required_specialty_id' => 1,
    ]);
    
    // Create bid invitation
    $invitation = \App\Models\BidInvitation::create([
        'shift_id' => $shift->id,
        'medical_worker_id' => $worker->id,
        'minimum_bid' => 50.00,
        'closes_at' => now()->addDays(2),
        'status' => 'open',
    ]);
    
    echo "✅ Created test data:\n";
    echo "   - Worker: {$worker->user->name} (ID: {$worker->id})\n";
    echo "   - Shift: {$shift->title} (ID: {$shift->id})\n";
    echo "   - Bid Invitation: {$invitation->id}\n\n";

    // Test the endpoint
    echo "🔍 Testing endpoint: GET /worker/shifts/bid-invitations\n";
    
    // Simulate the API call
    $request = new \Illuminate\Http\Request();
    $request->headers->set('Authorization', 'Bearer test-token');
    
    // Mock the authenticated user
    \Illuminate\Support\Facades\Auth::shouldReceive('guard->user')->andReturn($worker);
    
    $controller = new \App\Http\Controllers\Api\MedicalWorkerDashboardController();
    $response = $controller->bidInvitations($request);
    
    $data = $response->getData();
    echo "✅ API Response:\n";
    echo "   - Success: " . ($data->success ? 'true' : 'false') . "\n";
    echo "   - Count: " . ($data->count ?? 0) . "\n";
    
    if (!empty($data->data)) {
        echo "   - First bid invitation:\n";
        echo "     - Facility: {$data->data[0]->facility}\n";
        echo "     - Shift Time: {$data->data[0]->shiftTime}\n";
        echo "     - Minimum Bid: \${$data->data[0]->minimumBid}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
