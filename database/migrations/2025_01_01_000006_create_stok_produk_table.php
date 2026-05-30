<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasi')->cascadeOnDelete();
            $table->integer('total_stok')->default(0);
            $table->timestamps();

            $table->unique(['produk_id', 'lokasi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_produk');
    }
};
