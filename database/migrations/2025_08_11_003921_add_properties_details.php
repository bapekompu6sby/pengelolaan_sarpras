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
            $table->string('room_type')->nullable();
            $table->string('area')->nullable();
            $table->string('facilities')->nullable();
            $table->string('price')->nullable();
            $table->integer('unit')->nullable();
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
