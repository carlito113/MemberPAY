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
    Schema::create('yearsections', function (Blueprint $table) {
        $table->id(); // Primary key
        $table->unsignedBigInteger('admin_id'); // Link to admin
        $table->integer('year'); // 1, 2, 3, 4 (year level)
        $table->string('section'); // e.g., AI11, SF22
        $table->timestamps(); // created_at, updated_at

        $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
    });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yearsection');
    }
};
