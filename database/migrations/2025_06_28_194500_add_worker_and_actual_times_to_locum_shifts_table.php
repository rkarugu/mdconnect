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
            if (!Schema::hasColumn('locum_shifts', 'medical_worker_id')) {
                $table->foreignId('medical_worker_id')
                    ->nullable()
                    ->constrained('medical_workers')
                    ->nullOnDelete()
                    ->after('facility_id');
            }

            if (!Schema::hasColumn('locum_shifts', 'actual_start_time')) {
                $table->dateTime('actual_start_time')->nullable()->after('end_datetime');
            }

            if (!Schema::hasColumn('locum_shifts', 'actual_end_time')) {
                $table->dateTime('actual_end_time')->nullable()->after('actual_start_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locum_shifts', function (Blueprint $table) {
            if (Schema::hasColumn('locum_shifts', 'medical_worker_id')) {
                $table->dropConstrainedForeignId('medical_worker_id');
            }
            if (Schema::hasColumn('locum_shifts', 'actual_start_time')) {
                $table->dropColumn('actual_start_time');
            }
            if (Schema::hasColumn('locum_shifts', 'actual_end_time')) {
                $table->dropColumn('actual_end_time');
            }
        });
    }
};
