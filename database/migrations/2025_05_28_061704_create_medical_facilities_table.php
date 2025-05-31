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
        Schema::create('medical_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('facility_name');
            $table->string('facility_type')->comment('Hospital, Clinic, Laboratory, etc.');
            $table->string('license_number')->unique();
            $table->string('tax_id')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code');
            $table->string('country');
            $table->string('phone');
            $table->string('email');
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->integer('bed_capacity')->nullable();
            $table->enum('status', ['pending', 'verified', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('status_reason')->nullable(); // For storing rejection/suspension reasons
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('last_status_change')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('operating_hours')->nullable(); // Store operating hours as JSON
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_facilities');
    }
};
