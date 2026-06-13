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
        Schema::create('sqa_subject_assignments', function (Blueprint $table) {
            $table->id();
            // Links to the SQA user
            $table->foreignId('sqa_id')->constrained('users')->onDelete('cascade');
            // Links to the subject
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sqa_subject_assignments');
    }
};
