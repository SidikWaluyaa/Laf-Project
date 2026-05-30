<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add soft deletes to transaction tables for data safety.
     */
    public function up(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('purchase_order', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
