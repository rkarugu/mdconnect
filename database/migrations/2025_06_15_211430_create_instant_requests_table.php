<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('instant_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->foreignId('medical_worker_id')->constrained('medical_workers')->onDelete('cascade');
            $table->decimal('hourly_rate', 10, 2);
            $table->dateTime('expires_at');
            $table->string('status')->default('pending'); // pending, accepted, rejected, expired
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('instant_requests');
    }
};
