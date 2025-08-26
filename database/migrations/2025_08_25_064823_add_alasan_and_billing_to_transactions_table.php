<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->string('billing_code', 100)->nullable()->after('rejection_reason');
            $table->string('billing_qr', 255)->nullable()->after('billing_code'); // store path/URL of QR/barcode
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'billing_code', 'billing_qr']);
        });
    }
};
