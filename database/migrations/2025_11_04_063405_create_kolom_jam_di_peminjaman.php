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
        Schema::create('kolom_jam_di_peminjaman', function (Blueprint $table) {
            $table->id();
            // Tambahkan kolom jam start dan jam end
            $table->time('jam_start')->nullable()->comment('Jam mulai peminjaman (WIB)');
            $table->time('jam_end')->nullable()->comment('Jam selesai peminjaman (WIB)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kolom_jam_di_peminjaman');
    }
};
