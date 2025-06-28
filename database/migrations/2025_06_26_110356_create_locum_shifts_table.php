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
        Schema::create('locum_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('medical_facilities');
            $table->string('title');
            $table->text('description');
            $table->date('shift_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location');
            $table->enum('worker_type', ['Nurse', 'Doctor', 'Phlebotomist']);
            $table->integer('slots_available');
            $table->decimal('pay_rate', 8, 2);
            $table->enum('status', ['open', 'filled', 'canceled', 'closed'])->default('open');
            $table->boolean('auto_match')->default(false);
            $table->boolean('instant_book')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locum_shifts');
    }
};
