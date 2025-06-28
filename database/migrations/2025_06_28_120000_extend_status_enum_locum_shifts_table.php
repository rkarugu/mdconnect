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
        // Extend the status enum to include additional states
        Schema::table('locum_shifts', function (Blueprint $table) {
            $table->enum('status', ['open', 'confirmed', 'in_progress', 'filled', 'expired', 'canceled'])
                  ->default('open')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the original enum values (adjust if original differs)
        Schema::table('locum_shifts', function (Blueprint $table) {
            $table->enum('status', ['open', 'filled', 'canceled', 'closed'])
                  ->default('open')
                  ->change();
        });
    }
};
