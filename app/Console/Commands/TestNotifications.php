<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MedicalWorker;
use App\Models\MedicalSpecialty;
use App\Models\LocumShift;
use App\Notifications\NewShiftAvailable;
use Illuminate\Support\Facades\Notification;

class TestNotifications extends Command
{
    protected $signature = 'test:notifications';
    protected $description = 'Test the notification system for medical workers';

    public function handle()
    {
        $this->info('Testing notification system...');
        
        // Check medical workers
        $workers = MedicalWorker::all();
        $this->info("Total medical workers: {$workers->count()}");
        
        foreach ($workers as $worker) {
            $this->info("Worker: {$worker->name} | Status: {$worker->status} | Available: " . ($worker->is_available ? 'Yes' : 'No') . " | Specialty ID: {$worker->medical_specialty_id}");
        }
        
        // Check specialties
        $specialties = MedicalSpecialty::all();
        $this->info("Total specialties: {$specialties->count()}");
        
        foreach ($specialties as $specialty) {
            $this->info("Specialty ID: {$specialty->id} | Name: {$specialty->name}");
        }
        
        // Check recent shift
        $shift = LocumShift::find(13);
        if ($shift) {
            $this->info("Shift 13 details:");
            $this->info("Title: {$shift->title}");
            $this->info("Worker Type: {$shift->worker_type}");
            $this->info("Status: {$shift->status}");
            
            // Find matching specialty
            $specialty = MedicalSpecialty::where('name', $shift->worker_type)->first();
            if ($specialty) {
                $this->info("Matching specialty found: {$specialty->name} (ID: {$specialty->id})");
                
                // Find eligible workers
                $eligibleWorkers = MedicalWorker::where('medical_specialty_id', $specialty->id)
                    ->where('status', 'approved')
                    ->where('is_available', true)
                    ->get();
                
                $this->info("Eligible workers for this shift: {$eligibleWorkers->count()}");
                
                if ($eligibleWorkers->count() > 0) {
                    $this->info("Sending test notification...");
                    $shift->load('facility');
                    Notification::send($eligibleWorkers, new NewShiftAvailable($shift));
                    $this->info("Notification sent successfully!");
                } else {
                    $this->warn("No eligible workers found. Checking requirements:");
                    $allWorkers = MedicalWorker::where('medical_specialty_id', $specialty->id)->get();
                    foreach ($allWorkers as $worker) {
                        $this->info("Worker {$worker->name}: Status={$worker->status}, Available=" . ($worker->is_available ? 'Yes' : 'No'));
                    }
                }
            } else {
                $this->error("No specialty found matching worker_type: {$shift->worker_type}");
                $this->info("Available specialties:");
                foreach ($specialties as $spec) {
                    $this->info("- {$spec->name}");
                }
            }
        } else {
            $this->error("Shift with ID 13 not found");
        }
        
        // Check notifications in database
        $notificationCount = \DB::table('notifications')->count();
        $this->info("Total notifications in database: {$notificationCount}");
        
        if ($notificationCount > 0) {
            $recentNotifications = \DB::table('notifications')->latest()->limit(3)->get();
            $this->info("Recent notifications:");
            foreach ($recentNotifications as $notification) {
                $this->info("- ID: {$notification->id}, Type: {$notification->type}, Created: {$notification->created_at}");
            }
        }
        
        return 0;
    }
}
