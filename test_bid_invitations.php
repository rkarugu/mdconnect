<?php

require_once 'vendor/autoload.php';

// Test script to verify bid invitations are created and visible

echo "🔍 Testing Bid Invitation Visibility\n";
echo "====================================\n\n";

// Get a medical worker
try {
    $worker = \App\Models\MedicalWorker::first();
    if (!$worker) {
        echo "❌ No medical worker found\n";
        exit(1);
    }
    echo "✅ Found medical worker: {$worker->user->name} (ID: {$worker->id})\n";

    // Create a test shift
    $facility = \App\Models\MedicalFacility::first();
    if (!$facility) {
        echo "❌ No facility found\n";
        exit(1);
    }

    $shift = \App\Models\LocumShift::create([
        'title' => 'Test Shift - ' . now()->format('Y-m-d H:i'),
        'facility_id' => $facility->id,
        'start_datetime' => now()->addDay(),
        'end_datetime' => now()->addDay()->addHours(8),
        'pay_rate' => 50.00,
        'status' => 'open',
        'description' => 'Test shift for debugging',
        'required_specialty_id' => 1, // Make sure this matches worker's specialty
    ]);
    echo "✅ Created test shift: {$shift->title} (ID: {$shift->id})\n";

    // Create bid invitation
    $invitation = \App\Models\BidInvitation::create([
        'shift_id' => $shift->id,
        'medical_worker_id' => $worker->id,
        'minimum_bid' => 50.00,
        'closes_at' => now()->addDays(2),
        'status' => 'open',
    ]);
    echo "✅ Created bid invitation: ID {$invitation->id}\n";

    // Test API endpoint
    echo "\n🔍 Testing API endpoint...\n";
    
    // Simulate API call
    $bidInvitations = \App\Models\BidInvitation::with(['shift.facility'])
        ->where('medical_worker_id', $worker->id)
        ->where('status', 'open')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($invitation) {
            return [
                'invitationId' => $invitation->id,
                'facility' => $invitation->shift->facility->name ?? 'Medical Facility',
                'shiftTime' => $invitation->shift->start_datetime->format('M d, Y H:i') . ' - ' . $invitation->shift->end_datetime->format('H:i'),
                'minimumBid' => $invitation->minimum_bid,
                'status' => $invitation->status,
                'createdAt' => $invitation->created_at->toISOString(),
            ];
        });

    echo "✅ API would return " . $bidInvitations->count() . " bid invitations\n";
    
    if ($bidInvitations->isNotEmpty()) {
        echo "\n📋 Bid Invitation Details:\n";
        foreach ($bidInvitations as $bid) {
            echo "   - {$bid['facility']}: {$bid['shiftTime']} (Min: \${$bid['minimumBid']})\n";
        }
    }

    echo "\n🎯 Test Complete! The bid invitation should now be visible in the frontend.\n";
    echo "   - Frontend endpoint: /api/worker/shifts/bid-invitations\n";
    echo "   - Authenticate as medical worker ID: {$worker->id}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
