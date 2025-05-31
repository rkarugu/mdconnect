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
        Schema::create('locum_job_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialty_id')->constrained('medical_specialties');
            $table->string('title');
            $table->text('description');
            $table->integer('required_experience_years')->default(0);
            $table->json('required_qualifications')->nullable();
            $table->text('responsibilities');
            
            // Shift details
            $table->boolean('is_recurring')->default(false);
            $table->enum('shift_type', ['day', 'night', 'evening', 'custom'])->default('day');
            $table->timestamp('shift_start')->nullable(); // For single shifts
            $table->timestamp('shift_end')->nullable(); // For single shifts
            $table->json('recurring_pattern')->nullable(); // For recurring shifts (days of week, start/end times)
            $table->integer('recurring_duration_days')->nullable(); // How many days the recurring job lasts
            
            // Compensation
            $table->decimal('hourly_rate', 10, 2);
            $table->json('benefits')->nullable(); // Additional benefits as JSON
            
            // Location
            $table->boolean('is_remote')->default(false);
            $table->string('location')->nullable(); // Specific location within the facility
            
            // Status
            $table->enum('status', ['open', 'in_progress', 'filled', 'cancelled', 'completed'])->default('open');
            $table->integer('slots_available')->default(1); // How many workers needed
            $table->boolean('auto_match_enabled')->default(false); // For AI matching
            $table->boolean('instant_book_enabled')->default(false); // For Instant Book
            
            // Timestamps
            $table->timestamp('posted_at')->useCurrent();
            $table->timestamp('deadline')->nullable();
            $table->timestamp('filled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locum_job_requests');
    }
};
