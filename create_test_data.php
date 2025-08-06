<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\MedicalWorker;
use App\Models\LocumShift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "Creating test bid invitation data...\n";

// Find or create a test worker
$worker = MedicalWorker::find(1);
if (!$worker) {
    echo "Creating test worker...\n";
    $worker = MedicalWorker::create([
        'first_name' => 'Test',
        'last_name' => 'Worker',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'status' => 'approved',
        'is_available' => true,
        'medical_specialty_id' => 1
    ]);
}

// Create test shift
echo "Creating test shift...\n";
$shift = LocumShift::create([
    'facility_id' => 1,
    'title' => 'Emergency Nursing Shift',
    'worker_type' => 'Nurse',
    'location' => 'Test Medical Center, Downtown',
    'start_datetime' => now()->addHours(2),
    'end_datetime' => now()->addHours(10),
    'pay_rate' => 500,
    'status' => 'open',
    'slots_available' => 1,
    'description' => 'Urgent nursing shift needed',
    'created_by' => 1
]);

// Create notification manually
echo "Creating bid invitation notification...\n";
DB::table('notifications')->insert([
    'id' => Str::uuid(),
    'type' => 'App\\Notifications\\NewShiftAvailable',
    'notifiable_type' => 'App\\Models\\MedicalWorker',
    'notifiable_id' => $worker->id,
    'data' => json_encode([
        'shift_id' => $shift->id,
        'title' => $shift->title,
        'facility' => 'Test Medical Center',
        'start_datetime' => $shift->start_datetime->toISOString(),
        'end_datetime' => $shift->end_datetime->toISOString(),
        'pay_rate' => $shift->pay_rate,
        'minimum_bid' => 450
    ]),
    'read_at' => null,
    'created_at' => now(),
    'updated_at' => now()
]);

echo "✅ Test data created successfully!\n";
echo "Worker ID: " . $worker->id . "\n";
echo "Shift ID: " . $shift->id . "\n";
echo "Now refresh your Flutter app to see the bid invitation!\n";
