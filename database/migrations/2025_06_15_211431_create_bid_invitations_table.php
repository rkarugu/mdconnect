<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bid_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->foreignId('medical_worker_id')->constrained('medical_workers')->onDelete('cascade');
            $table->decimal('minimum_bid', 10, 2);
            $table->dateTime('closes_at');
            $table->string('status')->default('open'); // open, closed
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bid_invitations');
    }
};
