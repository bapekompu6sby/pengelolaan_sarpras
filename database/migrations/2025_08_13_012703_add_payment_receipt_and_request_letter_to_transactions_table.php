<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_receipt')->nullable()->after('status'); 
            $table->string('request_letter')->nullable()->after('payment_receipt'); 
        });
    }

    /**
     * Balikkan migration.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['payment_receipt', 'request_letter']);
        });
    }
};
