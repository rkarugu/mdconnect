<?php
// Immediate test for notification system

echo "=== Testing Notification System Right Now ===\n\n";

// 1. Check if notifications exist
echo "1. Checking notifications in database...\n";
$notifications = \DB::table('notifications')->get();
echo "   Total notifications: " . count($notifications) . "\n";

if (count($notifications) > 0) {
    echo "   Latest notification:\n";
    $latest = $notifications->first();
    $data = json_decode($latest->data, true);
    echo "   - Type: {$latest->type}\n";
    echo "   - Worker ID: {$latest->notifiable_id}\n";
    echo "   - Title: " . ($data['title'] ?? 'N/A') . "\n";
    echo "   - Message: " . ($data['message'] ?? 'N/A') . "\n";
}

// 2. Check recent shifts
echo "\n2. Checking recent shifts...\n";
$shifts = \DB::table('locum_shifts')->orderBy('created_at', 'desc')->limit(3)->get();
echo "   Recent shifts: " . count($shifts) . "\n";
foreach ($shifts as $shift) {
    echo "   - ID: {$shift->id}, Title: {$shift->title}, Worker Type: {$shift->worker_type}\n";
}

// 3. Check medical workers
echo "\n3. Checking medical workers...\n";
$workers = \DB::table('medical_workers')->get();
echo "   Total workers: " . count($workers) . "\n";
foreach ($workers as $worker) {
    echo "   - ID: {$worker->id}, Email: {$worker->email}, Specialty: {$worker->medical_specialty_id}\n";
}

// 4. Test API endpoint directly
echo "\n4. Testing API endpoint...\n";
$worker = \DB::table('medical_workers')->first();
if ($worker) {
    $token = \DB::table('personal_access_tokens')->where('tokenable_id', $worker->id)->where('tokenable_type', 'App\\Models\\MedicalWorker')->first();
    if ($token) {
        echo "   Found worker token: " . substr($token->token, 0, 10) . "...\n";
    } else {
        echo "   No token found for worker {$worker->id}\n";
    }
}

echo "\n=== Test Complete ===\n";
echo "\nTo test notifications via API:\n";
echo "curl -X GET http://127.0.0.1:8000/api/worker/notifications \\n";
echo "  -H \"Authorization: Bearer YOUR_WORKER_TOKEN\"\n";
