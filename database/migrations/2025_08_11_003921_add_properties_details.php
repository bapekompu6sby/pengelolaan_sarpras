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
        Schema::table('properties', function (Blueprint $table) {
            $table->string('room_type');
            $table->string('area');
            $table->string('facilities');
            $table->string('price');
            $table->integer('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('room_type');
            $table->dropColumn('area');
            $table->dropColumn('facilities');
            $table->dropColumn('price');
            $table->dropColumn('unit');
        });
    }
};
