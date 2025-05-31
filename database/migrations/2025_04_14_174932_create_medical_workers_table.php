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
        Schema::create('medical_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialty_id')->constrained('medical_specialties');
            $table->string('license_number')->unique();
            $table->string('years_of_experience');
            $table->text('bio')->nullable();
            $table->text('education')->nullable();
            $table->text('certifications')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('status_reason')->nullable(); // For storing rejection/suspension reasons
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('last_status_change')->nullable();
            $table->boolean('is_available')->default(true);
            $table->json('working_hours')->nullable(); // Store working hours as JSON
            $table->timestamps();
            $table->softDeletes(); // For soft deleting records
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_workers');
    }
};
