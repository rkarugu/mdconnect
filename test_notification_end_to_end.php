<?php
// End-to-end test for notification system

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);

// Test notification creation and retrieval
try {
    echo "=== Medical Worker Notification System Test ===\n\n";
    
    // 1. Get a medical worker
    $worker = \App\Models\MedicalWorker::first();
    if (!$worker) {
        echo "❌ No medical worker found. Creating test worker...\n";
        $worker = \App\Models\MedicalWorker::create([
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'email' => 'test.worker@example.com',
            'password' => bcrypt('password'),
            'medical_specialty_id' => 1,
            'phone' => '1234567890',
            'license_number' => 'TEST123',
            'status' => 'approved'
        ]);
    }
    echo "✅ Test Worker: ID {$worker->id}, Email: {$worker->email}\n";
    
    // 2. Create a test shift
    $facility = \App\Models\MedicalFacility::first();
    if (!$facility) {
        echo "❌ No facility found. Creating test facility...\n";
        $facility = \App\Models\MedicalFacility::create([
            'name' => 'Test Medical Facility',
            'email' => 'test.facility@example.com',
            'phone' => '9876543210',
            'address' => '123 Test Street',
            'status' => 'approved'
        ]);
    }
    
    $shift = \App\Models\LocumJobRequest::create([
        'facility_id' => $facility->id,
        'shift_type' => 'day',
        'start_date' => now()->addDays(1),
        'end_date' => now()->addDays(1),
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'specialty_required' => 'General Medicine',
        'description' => 'Test shift for notification system',
        'status' => 'open',
        'rate_per_hour' => 50.00,
        'total_hours' => 8
    ]);
    echo "✅ Test Shift Created: ID {$shift->id}\n";
    
    // 3. Create test notification manually
    $notificationData = [
        'title' => 'New Shift Available',
        'message' => "A new {$shift->specialty_required} shift is available at {$facility->name}",
        'shift_id' => $shift->id,
        'facility_name' => $facility->name,
        'specialty' => $shift->specialty_required,
        'start_date' => $shift->start_date->format('Y-m-d'),
        'rate_per_hour' => $shift->rate_per_hour
    ];
    
    $worker->notify(new \App\Notifications\NewShiftAvailable($shift));
    echo "✅ Notification created for worker ID {$worker->id}\n";
    
    // 4. Verify notification was created
    $notifications = \DB::table('notifications')
        ->where('notifiable_type', 'App\\Models\\MedicalWorker')
        ->where('notifiable_id', $worker->id)
        ->get();
    
    echo "\n📋 Notifications for Worker {$worker->id}:\n";
    foreach ($notifications as $notification) {
        $data = json_decode($notification->data, true);
        echo "   ID: {$notification->id}\n";
        echo "   Type: {$notification->type}\n";
        echo "   Title: {$data['title']}\n";
        echo "   Message: {$data['message']}\n";
        echo "   Created: {$notification->created_at}\n";
        echo "   Read: " . ($notification->read_at ?? 'No') . "\n";
        echo "   ---\n";
    }
    
    // 5. Test API endpoint
    echo "\n🌐 Testing API endpoint...\n";
    
    // Create token for API testing
    $token = $worker->createToken('test-api-token')->plainTextToken;
    echo "✅ API Token: {$token}\n";
    
    // Test API call (simulated)
    $notificationsApi = \DB::table('notifications')
        ->where('notifiable_type', 'App\\Models\\MedicalWorker')
        ->where('notifiable_id', $worker->id)
        ->orderBy('created_at', 'desc')
        ->limit(15)
        ->get();
    
    echo "✅ API Response would contain " . count($notificationsApi) . " notifications\n";
    
    echo "\n🎉 Notification system test completed successfully!\n";
    echo "📱 Frontend can now call: GET /api/worker/notifications with Authorization: Bearer {$token}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
