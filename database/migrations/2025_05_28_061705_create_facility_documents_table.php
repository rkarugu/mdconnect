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
        Schema::create('facility_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_facility_id')->constrained()->onDelete('cascade');
            $table->string('document_type'); // e.g., 'license', 'tax_certificate', 'registration'
            $table->string('title');
            $table->string('document_number')->nullable();
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('file_size'); // in bytes
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_documents');
    }
};
