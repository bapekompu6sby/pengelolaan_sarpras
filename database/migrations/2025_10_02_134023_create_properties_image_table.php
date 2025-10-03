<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties_image', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')
                ->constrained('properties')
                ->cascadeOnDelete();          // hapus gambar saat property dihapus
            $table->string('image_path', 255)->nullable(); // simpan NAMA FILE hash saja
            $table->timestamps();              // created_at & updated_at (nullable)
            $table->index('property_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties_image');
    }
};
