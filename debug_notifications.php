<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\LocumShift;
use Carbon\Carbon;

echo "=== Notification Debug ===\n\n";

// Check recent notifications
try {
    $notifications = DB::table('notifications')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    echo "📊 Recent Notifications:\n";
    foreach ($notifications as $notification) {
        echo "   ID: {$notification->id}\n";
        echo "   Type: {$notification->type}\n";
        echo "   Worker ID: {$notification->notifiable_id}\n";
        echo "   Created: {$notification->created_at}\n";
        
        $data = json_decode($notification->data, true);
        echo "   Title: " . ($data['title'] ?? 'N/A') . "\n";
        echo "   ---\n";
    }
    
    // Check medical workers
    $workers = DB::table('medical_workers')->get();
    echo "\n👥 Medical Workers:\n";
    foreach ($workers as $worker) {
        echo "   ID: {$worker->id}, Email: {$worker->email}\n";
    }
    
    // Check specialties
    $specialties = DB::table('medical_specialties')->get();
    echo "\n🏷️ Specialties:\n";
    foreach ($specialties as $specialty) {
        echo "   ID: {$specialty->id}, Name: {$specialty->name}\n";
    }
    
    // Check shifts with detailed date analysis
    $shifts = DB::table('locum_shifts')->orderBy('created_at', 'desc')->limit(5)->get();
    echo "\n📅 Recent Shifts (with date analysis):\n";
    $now = Carbon::now();
    echo "   Current time: {$now}\n\n";
    
    foreach ($shifts as $shift) {
        $startTime = Carbon::parse($shift->start_datetime);
        $isExpired = $startTime <= $now;
        $status = $isExpired ? '❌ EXPIRED' : '✅ ACTIVE';
        
        echo "   ID: {$shift->id}\n";
        echo "   Title: {$shift->title}\n";
        echo "   Worker Type: {$shift->worker_type}\n";
        echo "   Start: {$shift->start_datetime}\n";
        echo "   Status: {$shift->status}\n";
        echo "   {$status}\n";
        echo "   ---\n";
    }
    
    // Check notifications with shift data
    echo "\n🔔 Notification Analysis:\n";
    $notifications = DB::table('notifications')
        ->where('notifiable_type', 'App\\Models\\MedicalWorker')
        ->whereNull('read_at')
        ->orderBy('created_at', 'desc')
        ->get();
        
    foreach ($notifications as $notification) {
        $data = json_decode($notification->data, true);
        if (isset($data['shift_id'])) {
            $shift = LocumShift::find($data['shift_id']);
            if ($shift) {
                $startTime = Carbon::parse($shift->start_datetime);
                $isExpired = $startTime <= $now;
                $status = $isExpired ? '❌ EXPIRED' : '✅ ACTIVE';
                
                echo "   Notification ID: {$notification->id}\n";
                echo "   Worker ID: {$notification->notifiable_id}\n";
                echo "   Shift ID: {$data['shift_id']}\n";
                echo "   Shift Title: {$shift->title}\n";
                echo "   Shift Start: {$shift->start_datetime}\n";
                echo "   Shift Status: {$shift->status}\n";
                echo "   {$status}\n";
                echo "   ---\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🔍 Debug complete. Check above data.\n";
