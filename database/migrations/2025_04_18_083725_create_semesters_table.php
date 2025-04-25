<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('admin_id'); // Add admin_id
            $table->string('semester'); // Semester name (e.g., "First Semester")
            $table->string('academic_year'); // Academic year (e.g., "2024-2025")
            $table->timestamps(); // Created at and updated at timestamps
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        }); 
    }   
    /**
     * Reverse the migrations.
     *
     *
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};