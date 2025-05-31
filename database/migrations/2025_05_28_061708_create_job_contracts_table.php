<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locum_job_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('medical_worker_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_application_id')->nullable()->constrained()->onDelete('set null');
            $table->string('contract_number')->unique();
            $table->enum('status', ['draft', 'sent', 'signed_worker', 'signed_facility', 'active', 'completed', 'cancelled', 'disputed'])->default('draft');
            
            // Contract details
            $table->decimal('hourly_rate', 10, 2);
            $table->json('terms_and_conditions');
            $table->text('cancellation_policy')->nullable();
            $table->boolean('has_nda')->default(false);
            $table->text('nda_details')->nullable();
            
            // Signatures and timestamps
            $table->string('facility_signature_path')->nullable();
            $table->string('worker_signature_path')->nullable();
            $table->timestamp('facility_signed_at')->nullable();
            $table->timestamp('worker_signed_at')->nullable();
            $table->timestamp('contract_start')->nullable();
            $table->timestamp('contract_end')->nullable();
            $table->string('contract_document_path')->nullable();
            
            // Payment information
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'completed'])->default('pending');
            $table->json('payment_history')->nullable();
            
            // Recurring shifts
            $table->json('shift_schedule')->nullable()->comment('Schedule for recurring shifts');
            
            // Timestamps
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('dispute_filed_at')->nullable();
            $table->text('dispute_reason')->nullable();
            $table->timestamp('dispute_resolved_at')->nullable();
            $table->text('dispute_resolution')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_contracts');
    }
};
