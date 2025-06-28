<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add new enum values alongside existing ones to avoid truncation
        DB::statement("ALTER TABLE shift_applications MODIFY status ENUM('pending','accepted','waiting','approved','rejected') DEFAULT 'pending'");

        // 2. Move legacy rows to the new value
        DB::statement("UPDATE shift_applications SET status='approved' WHERE status='accepted'");

        // 3. Drop the obsolete 'accepted' value
        DB::statement("ALTER TABLE shift_applications MODIFY status ENUM('pending','waiting','approved','rejected') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // 1. Re-introduce 'accepted' so data can be converted back
        DB::statement("ALTER TABLE shift_applications MODIFY status ENUM('pending','waiting','approved','accepted','rejected') DEFAULT 'pending'");

        // 2. Convert 'approved' rows back to 'accepted'
        DB::statement("UPDATE shift_applications SET status='accepted' WHERE status='approved'");

        // 3. Remove 'waiting' and 'approved' values, restore original enum
        DB::statement("ALTER TABLE shift_applications MODIFY status ENUM('pending','accepted','rejected') DEFAULT 'pending'");
    }
};
