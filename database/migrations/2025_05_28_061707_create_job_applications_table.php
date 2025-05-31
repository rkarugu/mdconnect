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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locum_job_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('medical_worker_id')->constrained()->onDelete('cascade');
            $table->decimal('bid_amount', 10, 2)->comment('Hourly rate proposed by worker');
            $table->text('cover_note')->nullable();
            $table->enum('status', ['pending', 'viewed', 'shortlisted', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->json('availability')->nullable()->comment('Worker available dates/times for recurring shifts');
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->integer('ranking_score')->nullable()->comment('Score for auto-matching');
            $table->timestamps();
            $table->softDeletes();
            
            // Prevent duplicate applications
            $table->unique(['locum_job_request_id', 'medical_worker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
