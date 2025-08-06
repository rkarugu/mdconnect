<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'in_progress' and 'completed' to the status ENUM
        DB::statement("ALTER TABLE shift_applications MODIFY status ENUM('pending','waiting','approved','rejected','in_progress','completed') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Remove 'in_progress' and 'completed' from the status ENUM
        DB::statement("ALTER TABLE shift_applications MODIFY status ENUM('pending','waiting','approved','rejected') DEFAULT 'pending'");
    }
};
