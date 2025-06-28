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
            $table->dateTime('start_datetime')->after('description')->comment('Full start date and time of the shift');
            $table->dateTime('end_datetime')->after('start_datetime')->comment('Full end date and time of the shift');

            $table->dropColumn(['shift_date', 'start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locum_shifts', function (Blueprint $table) {
            $table->date('shift_date')->after('description');
            $table->time('start_time')->after('shift_date');
            $table->time('end_time')->after('start_time');

            $table->dropColumn(['start_datetime', 'end_datetime']);
        });
    }
};
