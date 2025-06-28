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
        Schema::table('locum_shifts', function (Blueprint $table) {
            $table->index('status');
            $table->index('start_datetime');
            $table->index('end_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locum_shifts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['start_datetime']);
            $table->dropIndex(['end_datetime']);
        });
    }
};
