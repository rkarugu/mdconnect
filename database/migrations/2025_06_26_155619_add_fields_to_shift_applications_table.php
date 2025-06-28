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
        Schema::table('shift_applications', function (Blueprint $table) {
            $table->foreignId('shift_id')->constrained('locum_shifts')->onDelete('cascade')->after('id');
            $table->foreignId('medical_worker_id')->constrained('medical_workers')->onDelete('cascade')->after('shift_id');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->after('medical_worker_id');
            $table->timestamp('applied_at')->useCurrent()->after('status');
            $table->timestamp('selected_at')->nullable()->after('applied_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_applications', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropForeign(['medical_worker_id']);
            $table->dropColumn(['shift_id', 'medical_worker_id', 'status', 'applied_at', 'selected_at']);
        });
    }
};
