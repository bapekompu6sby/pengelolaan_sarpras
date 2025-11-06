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
            $table->time('jam_start')->nullable()->after('end')->comment('Jam mulai peminjaman (WIB)');
            $table->time('jam_end')->nullable()->after('jam_start')->comment('Jam selesai peminjaman (WIB)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['jam_start', 'jam_end']);
        });
    }
};
