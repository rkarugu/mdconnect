<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_worker_id')->nullable()->constrained('medical_workers')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->decimal('hourly_rate', 10, 2);
            $table->string('location');
            $table->string('status')->default('open'); // open, pending, confirmed, completed, cancelled
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shifts');
    }
};
