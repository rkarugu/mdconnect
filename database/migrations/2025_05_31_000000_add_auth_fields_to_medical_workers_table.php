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
        // First, add the authentication fields to the medical_workers table only if they don't exist
        Schema::table('medical_workers', function (Blueprint $table) {
            // Check if columns exist before adding them
            if (!Schema::hasColumn('medical_workers', 'email')) {
                $table->string('email')->unique()->after('id');
            }
            if (!Schema::hasColumn('medical_workers', 'password')) {
                $table->string('password')->after('email');
            }
            if (!Schema::hasColumn('medical_workers', 'name')) {
                $table->string('name')->after('id');
            }
            if (!Schema::hasColumn('medical_workers', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('password');
            }
            if (!Schema::hasColumn('medical_workers', 'remember_token')) {
                $table->rememberToken()->after('email_verified_at');
            }
            if (!Schema::hasColumn('medical_workers', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('medical_workers', 'phone')) {
                $table->string('phone')->nullable()->after('profile_picture');
            }
            
            // Only drop if user_id exists and has a foreign key
            if (Schema::hasColumn('medical_workers', 'user_id')) {
                try {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                } catch (\Exception $e) {
                    // If there's no foreign key or the column doesn't exist, just continue
                }
            }
        });
        
        // Create a new table to store medical worker password reset tokens
        Schema::create('medical_worker_password_resets', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        
        // Create a new table to store medical worker sessions
        Schema::create('medical_worker_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('medical_worker_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the changes to medical_workers table
        Schema::table('medical_workers', function (Blueprint $table) {
            // Add back the user_id column
            $table->foreignId('user_id')->after('id')->nullable();
            
            // Remove the authentication fields
            $table->dropColumn([
                'email',
                'password',
                'name',
                'email_verified_at',
                'remember_token',
                'profile_picture',
                'phone'
            ]);
        });
        
        // Drop the additional tables
        Schema::dropIfExists('medical_worker_password_resets');
        Schema::dropIfExists('medical_worker_sessions');
    }
};
