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
    Schema::table('admins', function (Blueprint $table) {
        $table->dropUnique('admins_username_unique');
        $table->dropUnique('admins_password_unique');
    });
}

public function down(): void
{
    Schema::table('admins', function (Blueprint $table) {
        $table->unique('username');
        $table->unique('password');
    });
}

};
