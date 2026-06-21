<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            // The Coordinator who uploaded the file
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            
            $table->string('session'); // e.g., 2025/2026-1
            $table->string('type');    // e.g., Quiz, Assignment, Test, Final
            $table->string('title');
            $table->string('question_filename');
            $table->string('question_file'); 
            $table->string('schema_filename');
            $table->string('schema_file'); 
              
            
            // Overall Status (Calculated: Approved only if SME1, SME2, and KP approve)
            $table->string('status')->default('pending'); 

            // --- SME 1 Review Section ---
            $table->string('sme1_status')->default('pending');
            $table->text('sme1_comments')->nullable();
            $table->timestamp('sme1_verified_at')->nullable();
            $table->foreignId('sme1_id')->nullable()->constrained('users')->onDelete('set null');
            
            // --- SME 2 Review Section ---
            $table->string('sme2_status')->default('pending');
            $table->text('sme2_comments')->nullable();
            $table->timestamp('sme2_verified_at')->nullable();
            $table->foreignId('sme2_id')->nullable()->constrained('users')->onDelete('set null');

            // --- KP Review Section ---
            $table->string('kp_status')->default('pending'); 
            $table->text('kp_comments')->nullable();
            $table->timestamp('kp_verified_at')->nullable();
            $table->foreignId('kp_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};