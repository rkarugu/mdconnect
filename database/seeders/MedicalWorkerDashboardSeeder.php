<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalWorker;
use App\Models\Shift;
use App\Models\InstantRequest;
use App\Models\BidInvitation;
use Carbon\Carbon;

class MedicalWorkerDashboardSeeder extends Seeder
{
    public function run()
    {
        // Get a sample medical worker (or create one if needed)
        $worker = MedicalWorker::first() ?? MedicalWorker::factory()->create();

        // Create some upcoming shifts
        Shift::factory()->count(2)->create([
            'medical_worker_id' => $worker->id,
            'status' => 'confirmed',
            'start_time' => now()->addDays(1),
            'end_time' => now()->addDays(1)->addHours(8),
        ]);

        // Create some instant requests
        InstantRequest::factory()->count(3)->create([
            'medical_worker_id' => $worker->id,
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ]);

        // Create some bid invitations
        BidInvitation::factory()->count(2)->create([
            'medical_worker_id' => $worker->id,
            'status' => 'open',
            'closes_at' => now()->addDays(2),
        ]);

        // Create some completed shifts for history
        Shift::factory()->count(3)->create([
            'medical_worker_id' => $worker->id,
            'status' => 'completed',
            'start_time' => now()->subDays(3),
            'end_time' => now()->subDays(3)->addHours(8),
        ]);
    }
}
