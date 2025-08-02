<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MedicalWorker;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;

class TestNotificationAPI extends Command
{
    protected $signature = 'test:notification-api';
    protected $description = 'Test the notification API endpoint';

    public function handle()
    {
        $this->info('Testing notification API...');
        
        try {
            // Get the medical worker
            $worker = MedicalWorker::first();
            if (!$worker) {
                $this->error('No medical worker found');
                return 1;
            }
            
            $this->info("Testing with worker: {$worker->name}");
            
            // Simulate authentication
            auth()->guard('medical-worker')->login($worker);
            
            // Create controller instance and test
            $controller = new NotificationController();
            $request = new Request();
            
            // Test the index method
            $response = $controller->index($request);
            $data = $response->getData(true);
            
            $this->info('API Response:');
            $this->info(json_encode($data, JSON_PRETTY_PRINT));
            
            // Test unread count
            $countResponse = $controller->unreadCount();
            $countData = $countResponse->getData(true);
            
            $this->info('Unread Count Response:');
            $this->info(json_encode($countData, JSON_PRETTY_PRINT));
            
        } catch (\Exception $e) {
            $this->error('Error testing API: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
        
        return 0;
    }
}
