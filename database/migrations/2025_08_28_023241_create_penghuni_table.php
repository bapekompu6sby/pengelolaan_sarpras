<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penghuni', function (Blueprint $table) {
            $table->id();

            // FK ke tabel detail_kamar_transaction
            $table->foreignId('detail_kamar_transaction_id')
                  ->constrained('detail_kamar_transaction')
                  ->cascadeOnDelete();

            $table->string('nama_penghuni', 120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penghuni');
    }
};
