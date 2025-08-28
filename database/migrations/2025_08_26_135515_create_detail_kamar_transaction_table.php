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
        Schema::create('detail_kamar_transaction', function (Blueprint $table) {
            $table->id();

            // relasi ke transactions (plural)
            $table->foreignId('transaction_id')
                ->constrained('transactions')
                ->cascadeOnDelete();

            // relasi ke kamar (singular)
            $table->foreignId('kamar_id')
                ->constrained('kamar')
                ->cascadeOnDelete();

            $table->date('start');
            $table->date('end');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kamar_transaction');
    }
};
