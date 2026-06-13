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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            
            // Connects the subject to a specific Program (e.g., BCS)
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');

            // --- Assignment Columns ---
            // We use 'users' table and 'set null' so data persists if a staff member is removed
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('sme1_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('sme2_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->string('subject_code')->unique(); // e.g., BCS2243
            $table->string('subject_name');           // e.g., Web Development
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};