<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedBigInteger('current_organization_id')->nullable()->after('section');

            // Optional: add foreign key constraint
            $table->foreign('current_organization_id')->references('id')->on('organizations')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['current_organization_id']);
            $table->dropColumn('current_organization_id');
        });
    }

};
