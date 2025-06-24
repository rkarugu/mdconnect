<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MedicalWorker;
use App\Models\Shift;
use App\Models\InstantRequest;
use App\Models\BidInvitation;
use App\Models\Bid;
use Illuminate\Support\Facades\Auth;

class MedicalWorkerDashboardActionsTest extends TestCase
{
    public function test_accept_instant_request()
    {
        // Create medical worker and authenticate
        $worker = MedicalWorker::factory()->create();
        Auth::login($worker);

        // Create sample data
        $shift = Shift::factory()->create();
        $instantRequest = InstantRequest::factory()->create([
            'medical_worker_id' => $worker->id,
            'shift_id' => $shift->id,
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ]);

        // Make the request
        $response = $this->postJson("/api/worker/shifts/instant-requests/{$instantRequest->id}/accept");

        // Assert the response
        $response->assertStatus(200)
            ->assertJson(['message' => 'Shift accepted successfully']);

        // Assert database changes
        $this->assertDatabaseHas('shifts', [
            'id' => $shift->id,
            'medical_worker_id' => $worker->id,
            'status' => 'confirmed'
        ]);

        $this->assertDatabaseHas('instant_requests', [
            'id' => $instantRequest->id,
            'status' => 'accepted'
        ]);
    }

    public function test_apply_to_bid_invitation()
    {
        // Create medical worker and authenticate
        $worker = MedicalWorker::factory()->create();
        Auth::login($worker);

        // Create sample data
        $shift = Shift::factory()->create();
        $bidInvitation = BidInvitation::factory()->create([
            'medical_worker_id' => $worker->id,
            'shift_id' => $shift->id,
            'status' => 'open',
            'closes_at' => now()->addDays(2),
            'minimum_bid' => 50.00
        ]);

        // Make the request
        $response = $this->postJson("/api/worker/shifts/bid-invitations/{$bidInvitation->id}/apply", [
            'bid_amount' => 60.00
        ]);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Bid submitted successfully',
                'bid_amount' => 60.00
            ]);

        // Assert database changes
        $this->assertDatabaseHas('bids', [
            'bid_invitation_id' => $bidInvitation->id,
            'medical_worker_id' => $worker->id,
            'amount' => 60.00
        ]);
    }

    public function test_apply_to_bid_invitation_with_invalid_amount()
    {
        // Create medical worker and authenticate
        $worker = MedicalWorker::factory()->create();
        Auth::login($worker);

        // Create sample data
        $shift = Shift::factory()->create();
        $bidInvitation = BidInvitation::factory()->create([
            'medical_worker_id' => $worker->id,
            'shift_id' => $shift->id,
            'status' => 'open',
            'closes_at' => now()->addDays(2),
            'minimum_bid' => 50.00
        ]);

        // Make the request with invalid amount
        $response = $this->postJson("/api/worker/shifts/bid-invitations/{$bidInvitation->id}/apply", [
            'bid_amount' => 45.00 // Below minimum bid
        ]);

        // Assert the response
        $response->assertStatus(400)
            ->assertJson(['error' => 'Bid amount must be at least $50.00']);

        // Assert no bid was created
        $this->assertDatabaseMissing('bids', [
            'bid_invitation_id' => $bidInvitation->id,
            'medical_worker_id' => $worker->id
        ]);
    }
}
