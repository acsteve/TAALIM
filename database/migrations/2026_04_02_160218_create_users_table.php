<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id')->unique(); 
            $table->string('name');
            // Changed to nullable to support your SQA staff registration
            $table->string('email')->unique()->nullable(); 
            $table->string('password');
            
            // Added 'sqa' to the allowed ENUM values
            $table->enum('role', ['admin', 'kp', 'lecturer', 'sqa'])->default('lecturer'); 

            $table->unsignedBigInteger('course_id')->nullable(); 
            $table->index('course_id');

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};