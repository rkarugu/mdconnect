<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MedicalWorker;
use App\Models\Shift;
use App\Models\InstantRequest;
use App\Models\BidInvitation;
use Illuminate\Support\Facades\Auth;

class MedicalWorkerDashboardTest extends TestCase
{
    public function test_dashboard_endpoint_returns_expected_data()
    {
        // Create a medical worker and authenticate
        $worker = MedicalWorker::factory()->create();
        Auth::login($worker);

        // Create sample data
        $upcomingShift = Shift::factory()->create([
            'medical_worker_id' => $worker->id,
            'status' => 'confirmed',
            'start_time' => now()->addDays(1),
        ]);

        $instantRequest = InstantRequest::factory()->create([
            'medical_worker_id' => $worker->id,
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ]);

        $bidInvitation = BidInvitation::factory()->create([
            'medical_worker_id' => $worker->id,
            'status' => 'open',
            'closes_at' => now()->addDays(2),
        ]);

        // Make the request
        $response = $this->getJson('/api/worker/dashboard');

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'upcoming_shifts' => [
                    '*' => ['id', 'title', 'start_time', 'end_time', 'status']
                ],
                'instant_requests' => [
                    '*' => ['id', 'shift', 'hourly_rate', 'expires_at', 'status']
                ],
                'bid_invitations' => [
                    '*' => ['id', 'shift', 'minimum_bid', 'closes_at', 'status']
                ],
                'shift_history' => [
                    '*' => ['id', 'title', 'start_time', 'end_time', 'status']
                ]
            ]);

        // Assert specific data points
        $response->assertJson([
            'upcoming_shifts' => [[
                'id' => $upcomingShift->id,
                'status' => 'confirmed'
            ]],
            'instant_requests' => [[
                'id' => $instantRequest->id,
                'status' => 'pending'
            ]],
            'bid_invitations' => [[
                'id' => $bidInvitation->id,
                'status' => 'open'
            ]]
        ]);
    }

    public function test_upcoming_shifts_endpoint_returns_correct_data()
    {
        $worker = MedicalWorker::factory()->create();
        Auth::login($worker);

        // Create sample data
        $upcomingShift = Shift::factory()->create([
            'medical_worker_id' => $worker->id,
            'status' => 'confirmed',
            'start_time' => now()->addDays(1),
        ]);

        // Make the request
        $response = $this->getJson('/api/worker/shifts/upcoming');

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                0 => [
                    'id' => $upcomingShift->id,
                    'status' => 'confirmed'
                ]
            ]);
    }

    public function test_instant_requests_endpoint_returns_correct_data()
    {
        $worker = MedicalWorker::factory()->create();
        Auth::login($worker);

        // Create sample data
        $instantRequest = InstantRequest::factory()->create([
            'medical_worker_id' => $worker->id,
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ]);

        // Make the request
        $response = $this->getJson('/api/worker/shifts/instant-requests');

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                0 => [
                    'id' => $instantRequest->id,
                    'status' => 'pending'
                ]
            ]);
    }

    public function test_bid_invitations_endpoint_returns_correct_data()
    {
        $worker = MedicalWorker::factory()->create();
        Auth::login($worker);

        // Create sample data
        $bidInvitation = BidInvitation::factory()->create([
            'medical_worker_id' => $worker->id,
            'status' => 'open',
            'closes_at' => now()->addDays(2),
        ]);

        // Make the request
        $response = $this->getJson('/api/worker/shifts/bid-invitations');

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                0 => [
                    'id' => $bidInvitation->id,
                    'status' => 'open'
                ]
            ]);
    }

    public function test_shift_history_endpoint_returns_correct_data()
    {
        $worker = MedicalWorker::factory()->create();
        Auth::login($worker);

        // Create sample data
        $completedShift = Shift::factory()->create([
            'medical_worker_id' => $worker->id,
            'status' => 'completed',
            'start_time' => now()->subDays(1),
        ]);

        // Make the request
        $response = $this->getJson('/api/worker/shifts/history');

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                0 => [
                    'id' => $completedShift->id,
                    'status' => 'completed'
                ]
            ]);
    }
}
