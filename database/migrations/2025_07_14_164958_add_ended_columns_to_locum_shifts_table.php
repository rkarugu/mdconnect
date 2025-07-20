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
            $table->timestamp('ended_at')->nullable()->after('status');
            $table->foreignId('ended_by')->nullable()->constrained('users')->after('ended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locum_shifts', function (Blueprint $table) {
            $table->dropForeign(['ended_by']);
            $table->dropColumn(['ended_at', 'ended_by']);
        });
    }
};
