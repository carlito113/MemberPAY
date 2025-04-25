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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique(); // Will be used as the username
            $table->string('password')->unique(); // Will be used as the password
            $table->string('plain_password'); // ➡️ Add this line
            $table->enum('role', ['super_admin', 'admin']); // Role can either be 'super_admin' or 'admin'
            $table->string('name'); // Will be displayed as "Treasurer"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
