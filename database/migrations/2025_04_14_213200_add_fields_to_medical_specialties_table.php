<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_specialties', function (Blueprint $table) {
            $table->text('qualification_requirements')->nullable()->after('description');
            $table->integer('minimum_experience_years')->default(0)->after('qualification_requirements');
            $table->string('slug')->unique()->after('name');
            $table->string('icon')->nullable()->after('slug');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('medical_specialties', function (Blueprint $table) {
            $table->dropColumn([
                'qualification_requirements',
                'minimum_experience_years',
                'slug',
                'icon'
            ]);
            $table->dropSoftDeletes();
        });
    }
};
