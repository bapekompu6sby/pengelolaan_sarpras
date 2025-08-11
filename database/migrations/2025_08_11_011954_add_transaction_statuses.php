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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('affiliation');
            $table->string('phone_number');
            $table->string('email');
            $table->integer('ordered_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('affiliation');
            $table->dropColumn('phone_number');
            $table->dropColumn('email');
            $table->dropColumn('ordered_unit');
        });
    }
};
