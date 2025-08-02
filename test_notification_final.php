<?php
// Final test script for notification API endpoints

// Test notification API endpoints with proper authentication

// Start by creating a test token for a medical worker
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Create a test token for a medical worker
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);

// Get a medical worker and create a token
try {
    $worker = \App\Models\MedicalWorker::first();
    if (!$worker) {
        echo "No medical worker found. Creating test worker...\n";
        $worker = \App\Models\MedicalWorker::create([
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'email' => 'test@worker.com',
            'password' => bcrypt('password'),
            'medical_specialty_id' => 1,
            'phone' => '1234567890',
            'license_number' => 'TEST123',
            'status' => 'active'
        ]);
    }
    
    $token = $worker->createToken('test-api-token')->plainTextToken;
    echo "Test Medical Worker ID: {$worker->id}\n";
    echo "Test Token: {$token}\n\n";
    
    // Test the notification endpoints
    $baseUrl = 'http://127.0.0.1:8000';
    
    // Test 1: Get notifications
    echo "Testing GET /api/worker/notifications...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/worker/notifications');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "   Status: $httpCode\n";
    if ($response) {
        $data = json_decode($response, true);
        echo "   Success: " . ($data['success'] ?? 'N/A') . "\n";
        echo "   Notifications count: " . (count($data['data'] ?? []) ?? 0) . "\n";
    } else {
        echo "   No response received\n";
    }
    
    // Test 2: Get unread count
    echo "\nTesting GET /api/worker/notifications/unread-count...\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/worker/notifications/unread-count');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "   Status: $httpCode\n";
    if ($response) {
        $data = json_decode($response, true);
        echo "   Success: " . ($data['success'] ?? 'N/A') . "\n";
        echo "   Unread count: " . ($data['unread_count'] ?? 'N/A') . "\n";
    } else {
        echo "   No response received\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
