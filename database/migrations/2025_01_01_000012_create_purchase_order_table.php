<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('supplier_id')->constrained('supplier')->cascadeOnDelete();
            $table->enum('status', ['draft', 'dikirim', 'sebagian', 'selesai'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
