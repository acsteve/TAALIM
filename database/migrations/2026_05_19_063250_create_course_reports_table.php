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
        Schema::create('course_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('session'); // e.g., '20252026-01' or '2025/2026-SEM1'
            $table->string('type');    // e.g., 'teaching_plan', 'midterm_boe', 'student_grades'
            
            // FIXED: Swapped section_number for a foreignId linked to your sections table with cascade delete
            $table->foreignId('section_id')
                  ->nullable() // Left nullable since course-wide standard operational files don't use sections
                  ->constrained('sections')
                  ->onDelete('cascade'); 
                  
            $table->string('file_path');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The coordinator who uploaded it
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_reports');
    }
};