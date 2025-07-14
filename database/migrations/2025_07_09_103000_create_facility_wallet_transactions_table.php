<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_wallet_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['credit', 'debit', 'hold', 'release']);
            $table->decimal('amount', 15, 2);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_wallet_transactions');
    }
};
